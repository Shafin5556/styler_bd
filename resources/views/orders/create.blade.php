@extends('layouts.app')

@section('content')
    <div class="checkout-section">
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
                <p class="text-center text-muted">Your cart is empty. <a href="{{ route('shop') }}" class="text-primary">Go shopping</a>.</p>
            @else
                <h2 class="section-title">Checkout</h2>
                <div class="row">
                    <div class="col-md-8">
                        <form action="{{ route('orders.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="address" class="form-label">Shipping Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                                @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                    <label class="form-check-label" for="cod">Cash on Delivery</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="online" value="online">
                                    <label class="form-check-label" for="online">Online Payment (SSLCommerz)</label>
                                </div>
                                @error('payment_method') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Place Order</button>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <div class="subtotal-box p-3 bg-light rounded">
                            <h5 class="fw-bold">Order Summary</h5>
                            @php $subtotal = 0; @endphp
                            @foreach($cartItems as $cartItem)
                                @php $subtotal += $cartItem->product->price * $cartItem->quantity; @endphp
                            @endforeach
                            <p>Subtotal: <span class="text-primary">৳{{ number_format($subtotal, 2) }}</span></p>
                            <h5 class="fw-bold">Total: <span class="text-primary">৳{{ number_format($subtotal, 2) }}</span></h5>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Include Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .checkout-section {
            padding: 2rem 0;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
        }
        .container {
            max-width: 800px;
        }
        .section-title {
            font-size: 1.6rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
            font-family: 'Inter', sans-serif;
        }
        .alert {
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            padding: 0.75rem;
            margin-bottom: 1.5rem;
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
        .form-label {
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            color: #1a1a1a;
        }
        .form-control {
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .form-check-label {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #2d3748;
        }
        .text-danger {
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
        }
        .btn {
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
        }
        .btn-primary:hover {
            background-color: #1e40af;
            transform: translateY(-2px);
        }
        .subtotal-box {
            min-width: 200px;
            font-family: 'Inter', sans-serif;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .subtotal-box h5 {
            font-size: 1.25rem;
            color: #1a1a1a;
        }
        .subtotal-box p {
            font-size: 1rem;
            color: #2d3748;
        }
        .text-primary {
            color: #2563eb !important;
        }
        @media (max-width: 576px) {
            .section-title {
                font-size: 1.4rem;
            }
            .subtotal-box {
                width: 100%;
                text-align: center;
                margin-top: 1rem;
            }
            .form-control {
                font-size: 0.85rem;
            }
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>

    <script>
        (function (window, document) {
            var loader = function () {
                var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
                script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7);
                tag.parentNode.insertBefore(script, tag);
            };

            window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
        })(window, document);
    </script>
@endsection