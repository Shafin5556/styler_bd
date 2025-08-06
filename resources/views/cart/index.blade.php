@extends('layouts.app')

@section('content')
    <div class="shop-section">
        <div class="container">
      
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
            @if($cartItems->isEmpty())
                <p class="text-center text-muted">Your cart is empty.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Price</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Total</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $cartItem)
                                <tr class="product-card">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($cartItem->product->image)
                                                <img src="{{ asset($cartItem->product->image) }}" alt="{{ $cartItem->product->name }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                            @else
                                                <div class="me-3 bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 8px; color: #666;">No Image</div>
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
            @endif
            <a href="{{ route('shop') }}" class="btn btn-primary w-100 w-md-auto">
                <i class="bi bi-arrow-left-circle"></i> Continue Shopping
            </a>

            <!-- Include Bootstrap Icons -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

            <style>
                /* Inherit Shop Section Styling */
                .shop-section {
                    padding: 1rem 0;
                }
                .shop-title {
                    font-size: 2.8rem;
                    font-weight: 600;
                    color: #222;
                    text-align: center;
                    margin-bottom: 1rem;
                }
                .alert {
                    border-radius: 8px;
                    font-family: 'Poppins', sans-serif;
                    margin-bottom: 2rem;
                }
                .table-responsive {
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                    background-color: #ffffff;
                }
                .table {
                    margin-bottom: 0;
                }
                .table thead th {
                    background-color: #f8f9fa;
                    color: #222;
                    font-weight: 600;
                    padding: 1.5rem;
                    font-family: 'Poppins', sans-serif;
                }
                .table tbody tr.product-card {
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }
                .table tbody tr.product-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                }
                .table tbody td {
                    vertical-align: middle;
                    padding: 1.5rem;
                    border-top: 1px solid #dee2e6;
                }
                .card-title {
                    font-size: 1.3rem;
                    font-weight: 500;
                    color: #222;
                }
                .card-price {
                    font-size: 1.2rem;
                    font-weight: 600;
                    color: #007bff;
                }
                .btn {
                    border-radius: 8px;
                    font-family: 'Poppins', sans-serif;
                    transition: all 0.3s ease;
                }
                .btn-primary {
                    background-color: #007bff;
                    border: none;
                    font-weight: 500;
                }
                .btn-primary:hover {
                    background-color: #0056b3;
                }
                .btn-danger {
                    background-color: #dc3545;
                    border: none;
                    font-weight: 500;
                }
                .btn-danger:hover {
                    background-color: #b02a37;
                }
                .btn i {
                    margin-right: 6px;
                }
                @media (max-width: 991px) {
                    .shop-title {
                        font-size: 2.2rem;
                    }
                    .table thead th, .table tbody td {
                        padding: 1rem;
                    }
                }
                @media (max-width: 767px) {
                    .btn-primary {
                        width: 100% !important;
                    }
                    .table-responsive {
                        box-shadow: none;
                    }
                }
            </style>
        </div>
    </div>
@endsection