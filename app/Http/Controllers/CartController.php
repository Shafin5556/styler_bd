<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())->with(['product' => function ($query) {
            $query->with('images');
        }])->get();
        \Log::info('Viewing cart', ['user_id' => Auth::id(), 'item_count' => $cartItems->count()]);
        return view('cart.index', compact('cartItems'));
    }

    public function add(Request $request, $product)
    {
        $productModel = Product::find($product);
        if (!$productModel) {
            \Log::warning('Attempted to add invalid product to cart', ['product_id' => $product]);
            return redirect()->back()->with('error', 'Product not found.');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::where('user_id', Auth::id())
                        ->where('product_id', $product)
                        ->first();

        if ($cartItem) {
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
            \Log::info('Updated cart item quantity', ['user_id' => Auth::id(), 'product_id' => $product, 'new_quantity' => $cartItem->quantity]);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product,
                'quantity' => $validated['quantity'],
            ]);
            \Log::info('Product added to cart', ['user_id' => Auth::id(), 'product_id' => $product, 'quantity' => $validated['quantity']]);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart.');
    }

    public function bulkAdd(Request $request)
    {
        try {
            $products = $request->input('products', []);
            if (empty($products)) {
                \Log::warning('No products provided for bulk add', ['user_id' => Auth::id()]);
                return response()->json(['error' => 'No products provided'], 400);
            }

            foreach ($products as $productData) {
                if (!isset($productData['id']) || !isset($productData['quantity'])) {
                    \Log::warning('Invalid product data in bulk add', [
                        'user_id' => Auth::id(),
                        'product_data' => $productData
                    ]);
                    return response()->json(['error' => 'Invalid product data'], 400);
                }

                $product = Product::find($productData['id']);
                if (!$product) {
                    \Log::warning('Product not found for bulk add', [
                        'user_id' => Auth::id(),
                        'product_id' => $productData['id']
                    ]);
                    return response()->json(['error' => "Product ID {$productData['id']} not found"], 404);
                }

                $quantity = max(1, (int) $productData['quantity']);
                $cart = Cart::firstOrCreate(
                    ['user_id' => Auth::id(), 'product_id' => $product->id],
                    ['quantity' => $quantity]
                );

                if (!$cart->wasRecentlyCreated) {
                    $cart->increment('quantity', $quantity);
                }
            }

            \Log::info('Bulk products added to cart', [
                'user_id' => Auth::id(),
                'products' => $products
            ]);

            return response()->json(['message' => 'Products added to cart successfully'], 200);
        } catch (\Exception $e) {
            \Log::error('Error in bulk add to cart', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'products' => $request->input('products', [])
            ]);
            return response()->json(['error' => 'Failed to add products to cart: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::where('id', $validated['cart_id'])
                        ->where('user_id', Auth::id())
                        ->with('product')
                        ->first();

        if (!$cartItem) {
            \Log::warning('Attempted to update invalid or unauthorized cart item', [
                'cart_id' => $validated['cart_id'],
                'user_id' => Auth::id()
            ]);
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $cartItem->quantity = $validated['quantity'];
        $cartItem->save();

        \Log::info('Cart item quantity updated', [
            'user_id' => Auth::id(),
            'cart_id' => $validated['cart_id'],
            'new_quantity' => $validated['quantity']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quantity updated successfully',
            'price' => $cartItem->product->price
        ]);
    }

    public function remove($cart)
    {
        $cartItem = Cart::find($cart);
        if ($cartItem && $cartItem->user_id === Auth::id()) {
            $cartItem->delete();
            \Log::info('Product removed from cart', ['user_id' => Auth::id(), 'cart_id' => $cart]);
            return redirect()->route('cart.index')->with('success', 'Product removed from cart.');
        }

        \Log::warning('Attempted to remove invalid or unauthorized cart item', ['cart_id' => $cart, 'user_id' => Auth::id()]);
        return redirect()->route('cart.index')->with('error', 'Cart item not found.');
    }
}