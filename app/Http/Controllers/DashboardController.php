<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('Accessing admin dashboard', [
            'user_id' => Auth::user()->id,
            'user_role' => Auth::user()->role
        ]);

        if (Auth::user()->role !== 'admin') {
            \Log::warning('Unauthorized access to admin dashboard', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role
            ]);
            return redirect('/')->with('error', 'You are not authorized to access the admin dashboard.');
        }

        $query = Product::with('category', 'images');

        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%');
            \Log::info('Filtering products by search term', ['search' => $request->search]);
        }

        $products = $query->get();
        $admin = Auth::user(); // Pass authenticated user to the view

        return view('admin.dashboard', compact('products', 'admin'));
    }

  public function userDashboard()
    {
        \Log::info('Accessing user dashboard', [
            'user_id' => Auth::user()->id,
            'user_role' => Auth::user()->role
        ]);

        if (Auth::user()->role !== 'user') {
            \Log::warning('Unauthorized access to user dashboard', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role
            ]);
            return redirect('/')->with('error', 'You are not authorized to access the user dashboard.');
        }

        $user = Auth::user();
        $cartItems = Cart::where('user_id', Auth::id())->with(['product' => function ($query) {
            $query->with('images');
        }])->get();
        return view('user.dashboard', compact('user', 'cartItems'));
    }

    public function editProfile()
    {
        \Log::info('Accessing user profile edit', ['user_id' => Auth::id()]);
        $user = Auth::user();
        return view('user.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        \Log::info('Updating user profile', ['user_id' => Auth::id()]);

        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_picture' => 'nullable|image|mimes:png|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            try {
                if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
                    unlink(public_path($user->profile_picture));
                }
                $image = $request->file('profile_picture');
                $filename = time() . '.png';
                $path = 'profile/' . $filename;
                $image->move(public_path('asset/profile'), $filename);
                $validated['profile_picture'] = 'asset/' . $path;
            } catch (\Exception $e) {
                \Log::error('Failed to save profile picture', ['error' => $e->getMessage()]);
                return redirect()->back()->withErrors(['profile_picture' => 'Failed to save profile picture: ' . $e->getMessage()]);
            }
        }

        $user->update($validated);

        \Log::info('User profile updated', ['user_id' => $user->id, 'name' => $validated['name'], 'profile_picture' => $validated['profile_picture'] ?? $user->profile_picture]);
        return redirect()->route('user.dashboard')->with('success', 'Profile updated successfully.');
    }
}