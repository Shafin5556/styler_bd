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
        \Log::info('Accessing shop page', ['filters' => $request->only(['name', 'min_price', 'max_price', 'category_id'])]);

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

        if ($request->filled('category_id')) {
            $category = Category::find($request->input('category_id'));
            if ($category) {
                if ($category->parent_id === null) {
                    $subcategoryIds = $category->subcategories->pluck('id')->toArray();
                    $categoryIds = array_merge([$category->id], $subcategoryIds);
                    $query->whereIn('category_id', $categoryIds);
                } else {
                    $query->where('category_id', $category->id);
                }
            }
        }

        $products = $query->get();
        $minPrice = Product::min('price');
        $maxPrice = Product::max('price');
        $categories = Category::whereNull('parent_id')->with('subcategories')->get();

        \Log::info('Products retrieved', ['product_count' => $products->count()]);

        return view('shop.index', compact('products', 'minPrice', 'maxPrice', 'categories'));
    }

    public function dressup()
    {
        $categories = Category::whereNull('parent_id')->with('subcategories')->get();
        \Log::info('Accessing dressup page', ['category_count' => $categories->count()]);
        return view('dressup.index', compact('categories'));
    }

    public function getProductsBySubcategory($subcategory)
    {
        $category = Category::find($subcategory);
        if (!$category) {
            \Log::warning('Subcategory not found for products request', ['subcategory_id' => $subcategory]);
            return response()->json(['error' => 'Subcategory not found'], 404);
        }
        $products = Product::where('category_id', $subcategory)->select('id', 'name', 'price', 'image')->get();
        \Log::info('Fetched products for subcategory', ['subcategory_id' => $subcategory, 'product_count' => $products->count()]);
        return response()->json($products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image ? asset($product->image) : null,
            ];
        }));
    }

    public function category($category)
    {
        \Log::info('Accessing category page', ['category' => $category]);
        $category = Category::findOrFail($category);
        
        if ($category->parent_id === null) {
            $subcategoryIds = $category->subcategories->pluck('id')->toArray();
            $categoryIds = array_merge([$category->id], $subcategoryIds);
            $products = Product::whereIn('category_id', $categoryIds)->get();
        } else {
            $products = Product::where('category_id', $category->id)->get();
        }

        if ($products->isEmpty()) {
            \Log::warning('No products found for category', ['category_id' => $category->id]);
            return redirect()->route('shop')->with('error', 'No products found in this category.');
        }

        $minPrice = Product::min('price');
        $maxPrice = Product::max('price');
        $categories = Category::whereNull('parent_id')->with('subcategories')->get();

        return view('shop.index', compact('products', 'minPrice', 'maxPrice', 'categories'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            \Log::warning('Unauthorized access to product create', [
                'user_id' => Auth::user()->id,
                'user_role' => Auth::user()->role
            ]);
            return redirect('/')->with('error', 'You are not authorized to access this page.');
        }

        $categories = Category::whereNull('parent_id')->with('subcategories')->get();
        \Log::info('Loading product create view', ['category_count' => $categories->count()]);
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
                $image->move(public_path('asset/product'), $filename);
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

        $categories = Category::whereNull('parent_id')->with('subcategories')->get();
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
                if ($product->image && file_exists(public_path($product->image))) {
                    unlink(public_path($product->image));
                }
                $image = $request->file('image');
                $filename = time() . '.png';
                $path = 'product/' . $filename;
                $image->move(public_path('asset/product'), $filename);
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
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
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