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

        $categories = Category::whereNull('parent_id')->with('subcategories')->get();
        \Log::info('Loading category create view', ['category_count' => $categories->count()]);
        return view('products.create', compact('categories'));
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
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create([
            'name' => $validated['name'],
            'parent_id' => $request->input('parent_id', null),
        ]);

        \Log::info('Category created', ['name' => $validated['name'], 'parent_id' => $request->input('parent_id', null)]);
        return redirect()->route('products.create')->with('success', 'Category added successfully.');
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
            if ($category->products()->exists()) {
                \Log::warning('Attempted to delete category with products', ['category_id' => $category->id]);
                return redirect()->route('products.create')->with('error', 'Cannot delete category because it has associated products.');
            }
            if ($category->subcategories()->exists()) {
                \Log::warning('Attempted to delete category with subcategories', ['category_id' => $category->id]);
                return redirect()->route('products.create')->with('error', 'Cannot delete category because it has subcategories.');
            }

            $category->delete();
            \Log::info('Category deleted', ['name' => $category->name, 'id' => $category->id]);
            return redirect()->route('products.create')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete category', ['id' => $category->id, 'error' => $e->getMessage()]);
            return redirect()->route('products.create')->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    public function getSubcategories($categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category) {
            \Log::warning('Category not found for subcategories request', ['category_id' => $categoryId]);
            return response()->json(['error' => 'Category not found'], 404);
        }
        $subcategories = $category->subcategories()->select('id', 'name')->get();
        \Log::info('Fetched subcategories', ['category_id' => $categoryId, 'subcategory_count' => $subcategories->count()]);
        return response()->json($subcategories);
    }
}