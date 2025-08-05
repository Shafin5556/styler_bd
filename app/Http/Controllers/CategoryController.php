<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            \Log::warning('Unauthorized access to category create', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role
            ]);
            return redirect('/')->with('error', 'You are not authorized to access this page.');
        }

        $categories = Category::all();
        \Log::info('Loading category create view', ['category_count' => $categories->count()]);
        return view('categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            \Log::warning('Unauthorized access to category store', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role
            ]);
            return redirect('/')->with('error', 'You are not authorized to access this page.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $validated['name'],
        ]);

        \Log::info('Category created', ['name' => $validated['name']]);
        return redirect()->route('admin.dashboard')->with('success', 'Category added successfully.');
    }

    public function destroy(Category $category)
    {
        if (Auth::user()->role !== 'admin') {
            \Log::warning('Unauthorized access to category delete', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role
            ]);
            return redirect('/')->with('error', 'You are not authorized to access this page.');
        }

        try {
            // Check if category has associated products
            if ($category->products()->exists()) {
                \Log::warning('Attempted to delete category with products', ['category_id' => $category->id]);
                return redirect()->route('categories.create')->with('error', 'Cannot delete category because it has associated products.');
            }

            $category->delete();
            \Log::info('Category deleted', ['name' => $category->name, 'id' => $category->id]);
            return redirect()->route('categories.create')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete category', ['id' => $category->id, 'error' => $e->getMessage()]);
            return redirect()->route('categories.create')->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }
}