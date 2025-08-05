@extends('layouts.app')

@section('content')
    <h1>Your Cart</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($cartItems->isEmpty())
        <p>Your cart is empty.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $cartItem)
                    <tr>
                        <td>{{ $cartItem->product->name }}</td>
                        <td>৳{{ $cartItem->product->price }}</td>
                        <td>{{ $cartItem->quantity }}</td>
                        <td>৳{{ $cartItem->product->price * $cartItem->quantity }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $cartItem->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
@endsection