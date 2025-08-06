
@extends('layouts.app')

@section('content')
    <div class="dashboard-section">
        <div class="container d-flex justify-content-center">
            <div class="dashboard-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="section-title">Your Profile</h2>
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center">
                                @if(auth()->user()->profile_picture)
                                    <img src="{{ asset(auth()->user()->profile_picture) }}" alt="{{ auth()->user()->name }}" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; font-size: 0.9rem; color: #666;">
                                        No Image
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <h4 class="card-title">{{ auth()->user()->name }}</h4>
                                <p class="text-muted"><strong>Email:</strong> {{ auth()->user()->email }}</p>
                                <a href="{{ route('user.edit') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i> Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h2 class="section-title">Your Cart</h2>
                        @if($cartItems->isEmpty())
                            <p class="text-center text-muted">Your cart is empty.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-borderless">
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
                                            <tr class="product-row">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($cartItem->product->image)
                                                            <img src="{{ asset($cartItem->product->image) }}" alt="{{ $cartItem->product->name }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                                        @else
                                                            <div class="me-3 bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 8px; color: #666; font-size: 0.85rem;">No Image</div>
                                                        @endif
                                                        <h6 class="card-title mb-0">{{ $cartItem->product->name }}</h6>
                                                    </div>
                                                </td>
                                                <td><span class="card-price">৳{{ number_format($cartItem->product->price, 2) }}</span></td>
                                                <td>{{ $cartItem->quantity }}</td>
                                                <td><span class="card-price">৳{{ number_format($cartItem->product->price * $cartItem->quantity, 2) }}</span></td>
                                                <td>
                                                    <form action="{{ route('cart.remove', $cartItem->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="bi bi-trash"></i> Remove
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('home') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-arrow-left-circle"></i> Continue Shopping
                                </a>
                                <a href="{{ route('cart.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="bi bi-cart"></i> View Full Cart
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .dashboard-section {
         
            background-color: #f8f9fa;
       
            display: flex;
            align-items: center;
        }
        .dashboard-content {
            max-width: 800px;
            width: 100%;
        }
        .section-title {
            font-size: 1.6rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 1.5rem;
            font-family: 'Poppins', sans-serif;
        }
        .card {
            border: none;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
        }
        .alert {
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            padding: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: 500;
            color: #222;
            font-family: 'Poppins', sans-serif;
        }
        .card-price {
            font-size: 1rem;
            font-weight: 600;
            color: #007bff;
        }
        .text-muted {
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background-color: #f8f9fa;
            color: #222;
            font-weight: 600;
            padding: 0.75rem;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }
        .table tbody tr.product-row {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .table tbody tr.product-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .table tbody td {
            vertical-align: middle;
            padding: 0.75rem;
            border-top: 1px solid #dee2e6;
            font-size: 0.9rem;
        }
        .btn {
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #b02a37;
        }
        .btn i {
            margin-right: 6px;
        }
        @media (max-width: 576px) {
            .section-title {
                font-size: 1.4rem;
            }
            .card-title {
                font-size: 1.1rem;
            }
            .dashboard-content {
                margin: 0 1rem;
            }
            .table thead th, .table tbody td {
                padding: 0.5rem;
                font-size: 0.85rem;
            }
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            .d-flex.gap-2 {
                flex-direction: column;
            }
        }
    </style>
@endsection
