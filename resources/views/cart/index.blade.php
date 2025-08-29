@extends('layouts.app')

@section('content')
    <div class="shop-section">
        <div class="container">
            <!-- @if(session('success'))
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
            @endif -->
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
                            @php $subtotal = 0; @endphp
                            @foreach($cartItems as $cartItem)
                                @php $subtotal += $cartItem->product->price * $cartItem->quantity; @endphp
                                <tr class="product-card" data-cart-id="{{ $cartItem->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($cartItem->product->images->isNotEmpty())
                                                <img src="{{ asset($cartItem->product->images->first()->image) }}"
                                                    alt="{{ $cartItem->product->name }}" class="me-3"
                                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                            @else
                                                <div class="me-3 bg-light d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 50px; border-radius: 8px; color: #666;">No Image</div>
                                            @endif
                                            <h6 class="card-title mb-0">{{ $cartItem->product->name }}</h6>
                                        </div>
                                    </td>
                                    <td><span class="card-price" data-price="{{ $cartItem->product->price }}">৳{{ number_format($cartItem->product->price, 2) }}</span></td>
                                    <td>
                                        <input type="number" class="form-control quantity-input" style="width: 80px;" 
                                            value="{{ $cartItem->quantity }}" min="1" data-cart-id="{{ $cartItem->id }}">
                                    </td>
                                    <td><span class="card-price total-price">৳{{ number_format($cartItem->product->price * $cartItem->quantity, 2) }}</span></td>
                                    <td>
                                        <form action="{{ route('cart.remove', $cartItem->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Subtotal -->
                    <div class="d-flex justify-content-end mt-3">
                        <div class="subtotal-box p-3 bg-light rounded">
                            <h5 class="mb-0 fw-bold">Subtotal: <span id="subtotal" class="text-primary">৳{{ number_format($subtotal, 2) }}</span></h5>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('shop') }}" class="btn btn-primary w-100 w-md-auto">
                        <i class="bi bi-arrow-left-circle"></i> Continue Shopping
                    </a>
                    <a href="{{ route('checkout') }}" class="btn btn-success w-100 w-md-auto">
                        <i class="bi bi-cart-check"></i> Order Now
                    </a>
                </div>
            @endif

            <!-- Include Bootstrap Icons -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

            <style>
                .shop-section {
                    padding: 2rem 0;
                    background-color: #f8f9fa;
                }

                .shop-title {
                    font-size: 2rem;
                    font-weight: 600;
                    color: #1a1a1a;
                    text-align: center;
                    margin-bottom: 1.5rem;
                    font-family: 'Inter', sans-serif;
                }

                .alert {
                    border-radius: 10px;
                    margin-bottom: 1.5rem;
                    padding: 1rem;
                    font-family: 'Inter', sans-serif;
                }

                .alert-success {
                    background-color: #d4f4e2;
                    border-color: #28a745;
                    color: #1a6332;
                }

                .alert-danger {
                    background-color: #f8d7da;
                    border-color: #dc3545;
                    color: #721c24;
                }

                .table-responsive {
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                    background-color: #ffffff;
                }

                .table {
                    margin-bottom: 0;
                }

                .table thead th {
                    background-color: #f1f5f9;
                    color: #1a1a1a;
                    font-weight: 600;
                    padding: 1.5rem;
                    font-family: 'Inter', sans-serif;
                }

                .table tbody tr.product-card {
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }

                .table tbody tr.product-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
                }

                .table tbody td {
                    vertical-align: middle;
                    padding: 1.5rem;
                    border-top: 1px solid #e5e7eb;
                    font-family: 'Inter', sans-serif;
                    color: #2d3748;
                }

                .card-title {
                    font-size: 1.2rem;
                    font-weight: 500;
                    color: #1a1a1a;
                    font-family: 'Inter', sans-serif;
                }

                .card-price {
                    font-size: 1.1rem;
                    font-weight: 600;
                    color: #2563eb;
                }

                .btn {
                    border-radius: 8px;
                    font-family: 'Inter', sans-serif;
                    transition: all 0.3s ease;
                }

                .btn-primary {
                    background-color: #2563eb;
                    border: none;
                    font-weight: 500;
                    padding: 0.75rem 1.5rem;
                }

                .btn-primary:hover {
                    background-color: #1e40af;
                    transform: translateY(-2px);
                }

                .btn-success {
                    background-color: #16a34a;
                    border: none;
                    font-weight: 500;
                    padding: 0.75rem 1.5rem;
                }

                .btn-success:hover {
                    background-color: #15803d;
                    transform: translateY(-2px);
                }

                .btn-outline-danger {
                    border-color: #dc3545;
                    color: #dc3545;
                    font-weight: 500;
                    padding: 0.25rem 0.5rem;
                }

                .btn-outline-danger:hover {
                    background-color: #dc3545;
                    color: #fff;
                    transform: translateY(-2px);
                }

                .btn i {
                    margin-right: 6px;
                }

                .subtotal-box {
                    min-width: 200px;
                    text-align: right;
                    font-family: 'Inter', sans-serif;
                }

                .subtotal-box h5 {
                    font-size: 1.25rem;
                    color: #1a1a1a;
                }

                .subtotal-box .text-primary {
                    color: #2563eb !important;
                }

                .quantity-input {
                    border-radius: 6px;
                    padding: 0.25rem;
                    font-size: 1rem;
                }

                @media (max-width: 991px) {
                    .shop-title {
                        font-size: 1.75rem;
                    }

                    .table thead th,
                    .table tbody td {
                        padding: 1rem;
                    }

                    .table img {
                        width: 40px;
                        height: 40px;
                    }

                    .subtotal-box {
                        min-width: 150px;
                    }

                    .subtotal-box h5 {
                        font-size: 1.1rem;
                    }

                    .quantity-input {
                        width: 60px;
                    }
                }

                @media (max-width: 767px) {
                    .shop-section {
                        padding: 1.5rem 0;
                    }

                    .btn-primary,
                    .btn-success {
                        width: 100% !important;
                    }

                    .d-flex.gap-2 {
                        flex-direction: column;
                        gap: 1rem;
                    }

                    .table-responsive {
                        box-shadow: none;
                    }

                    .subtotal-box {
                        width: 100%;
                        text-align: center;
                    }

                    .quantity-input {
                        width: 50px;
                    }
                }
            </style>

            <!-- JavaScript for Quantity Update -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('.quantity-input').on('change', function() {
                        let $input = $(this);
                        let cartId = $input.data('cart-id');
                        let quantity = parseInt($input.val());
                        let $row = $input.closest('tr');
                        let price = parseFloat($row.find('.card-price').first().data('price'));

                        if (isNaN(quantity) || quantity < 1) {
                            quantity = 1;
                            $input.val(1);
                        }

                        $.ajax({
                            url: '{{ route("cart.update") }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                cart_id: cartId,
                                quantity: quantity
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Update item total
                                    let total = (price * quantity).toFixed(2);
                                    $row.find('.total-price').text(`৳${total}`);

                                    // Update subtotal
                                    let subtotal = 0;
                                    $('.product-card').each(function() {
                                        let rowPrice = parseFloat($(this).find('.card-price').first().data('price'));
                                        let rowQuantity = parseInt($(this).find('.quantity-input').val());
                                        subtotal += rowPrice * rowQuantity;
                                    });
                                    $('#subtotal').text(`৳${subtotal.toFixed(2)}`);

                                    // // Show success message
                                    // let alert = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    //     ${response.message}
                                    //     <button type="button" class="btn-close" data="Close"></button>
                                    // </div>`;
                                    // $('.container').prepend(alert);

                                    // // Remove alert after 3 seconds
                                    // setTimeout(() => $('.alert').alert('close'), 3000);
                                }
                            },
                            error: function(xhr) {
                                // Revert quantity input on error
                                $input.val($input.data('original-value') || 1);
                                
                                let alert = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    Failed to update quantity. Please try again.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>`;
                                $('.container').prepend(alert);

                                // Remove alert after 3 seconds
                                setTimeout(() => $('.alert').alert('close'), 3000);
                            }
                        });

                        // Store current value for potential revert
                        $input.data('original-value', quantity);
                    });
                });
            </script>
        </div>
    </div>
@endsection