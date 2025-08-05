<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
       

        return view('home');
    }

     public function shop(Request $request)
    {
        \Log::info('Accessing home page', ['filters' => $request->only(['name', 'min_price', 'max_price'])]);

        $query = Product::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        $products = $query->get();
        $minPrice = Product::min('price');
        $maxPrice = Product::max('price');

        \Log::info('Products retrieved', ['product_count' => $products->count()]);

        return view('shop.index', compact('products', 'minPrice', 'maxPrice'));
    }
    public function category($category)
    {
        \Log::info('Accessing category page', ['category' => $category]);
        $categories = Category::all();
        $products = Product::where('category_id', $category)->get();
        if ($products->isEmpty()) {
            \Log::warning('No products found for category', ['category_id' => $category]);
            return redirect()->route('home')->with('error', 'No products found in this category.');
        }
        return view('home', compact('categories', 'products'));
    }
    public function create()
    {
        \Log::info('Entering ProductController::create', ['user_id' => Auth::id()]);
        if (Auth::user()->role !== 'admin') {
            \Log::warning('Unauthorized access to product create', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role
            ]);
            return redirect('/')->with('error', 'You are not authorized to access this page.');
        }

        $categories = Category::all();
        \Log::info('Loading product create view', [
            'view' => 'products.create',
            'category_count' => $categories->count()
        ]);
        return view('products.create', compact('categories'));
    }
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            \Log::warning('Unauthorized access to product store', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role
            ]);
            return redirect('/')->with('error', 'You are not authorized to access this page.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $filename = time() . '.png';
                $path = 'product/' . $filename;
                $image->move(base_path('asset/product'), $filename);
                $validated['image'] = 'asset/' . $path;
            } catch (\Exception $e) {
                \Log::error('Failed to save image', ['error' => $e->getMessage()]);
                return redirect()->back()->withErrors(['image' => 'Failed to save image: ' . $e->getMessage()]);
            }
        }

        Product::create($validated);

        \Log::info('Product created', ['name' => $validated['name'], 'image' => $validated['image'] ?? null]);
        return redirect()->route('admin.dashboard')->with('success', 'Product added successfully.');
    }

    public function edit(Product $product)
    {
        if (Auth::user()->role !== 'admin') {
            \Log::warning('Unauthorized access to product edit', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role
            ]);
            return redirect('/')->with('error', 'You are not authorized to access this page.');
        }

        $categories = Category::all();
        \Log::info('Loading product edit view', ['product_id' => $product->id, 'category_count' => $categories->count()]);
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if (Auth::user()->role !== 'admin') {
            \Log::warning('Unauthorized access to product update', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role
            ]);
            return redirect('/')->with('error', 'You are not authorized to access this page.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            try {
                // Delete old image if it exists
                if ($product->image && file_exists(base_path($product->image))) {
                    unlink(base_path($product->image));
                }
                $image = $request->file('image');
                $filename = time() . '.png';
                $path = 'product/' . $filename;
                $image->move(base_path('asset/product'), $filename);
                $validated['image'] = 'asset/' . $path;
            } catch (\Exception $e) {
                \Log::error('Failed to save image', ['error' => $e->getMessage()]);
                return redirect()->back()->withErrors(['image' => 'Failed to save image: ' . $e->getMessage()]);
            }
        }

        $product->update($validated);

        \Log::info('Product updated', ['name' => $validated['name'], 'image' => $validated['image'] ?? $product->image]);
        return redirect()->route('admin.dashboard')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if (Auth::user()->role !== 'admin') {
            \Log::warning('Unauthorized access to product delete', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role
            ]);
            return redirect('/')->with('error', 'You are not authorized to access this page.');
        }

        try {
            // Delete image if it exists
            if ($product->image && file_exists(base_path($product->image))) {
                unlink(base_path($product->image));
            }
            $product->delete();
            \Log::info('Product deleted', ['name' => $product->name, 'id' => $product->id]);
            return redirect()->route('admin.dashboard')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete product', ['id' => $product->id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.dashboard')->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }
}