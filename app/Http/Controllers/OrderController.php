<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{


    public function index(Request $request)
    {
        Log::info('Accessing admin orders page', [
            'user_id' => Auth::user()->id,
            'user_role' => Auth::user()->role
        ]);

        $query = Cart::with(['user', 'product']);

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
            Log::info('Filtering carts by search term', ['search' => $search]);
        }

        $carts = $query->orderBy('created_at', 'desc')->get();
        $admin = Auth::user();

        return view('admin.orders', compact('carts', 'admin'));
    }
}