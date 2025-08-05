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
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
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