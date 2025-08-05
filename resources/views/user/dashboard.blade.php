@extends('layouts.app')

@section('content')
    <h1>User Dashboard</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h2>Your Profile</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ asset(auth()->user()->profile_picture) }}" alt="{{ auth()->user()->name }}" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                            <span>No Image</span>
                        </div>
                    @endif
                </div>
                <div class="col-md-9">
                    <h4>{{ auth()->user()->name }}</h4>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <a href="{{ route('user.edit') }}" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Your Cart</h2>
        </div>
        <div class="card-body">
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
            <a href="{{ route('cart.index') }}" class="btn btn-secondary">View Full Cart</a>
        </div>
    </div>
@endsection