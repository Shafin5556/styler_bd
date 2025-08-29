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
                                    <img src="{{ asset(auth()->user()->profile_picture) }}" alt="{{ auth()->user()->name }}"
                                        class="img-fluid rounded-circle"
                                        style="width: 120px; height: 120px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 120px; height: 120px; font-size: 1.5rem; color: #666; font-weight: 600;">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-9 d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">{{ auth()->user()->name }}</h4>
                                    <p class="text-muted"><strong>Email:</strong> {{ auth()->user()->email }}</p>
                                    <a href="{{ route('user.edit') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil"></i> Edit Profile
                                    </a>
                                </div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm logout-btn">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


    <!-- Orders Table -->
     <!-- Orders Table -->
            <div class="card">
                <div class="card-body">
                    <h2 class="section-title">Your Orders</h2>
                    @if($orders->isEmpty())
                        <p class="text-center text-muted">You have no orders.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $receivable = 0;
                                        $paid = 0;
                                        $hasPendingOrders = false;
                                    @endphp
                                    @foreach($orders as $order)
                                        @php
                                            if ($order->status === 'pending') {
                                                $receivable += $order->total;
                                                $hasPendingOrders = true;
                                            } else {
                                                $paid += $order->total;
                                            }
                                        @endphp
                                        <tr class="order-row">
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->payment_method }}</td>
                                            <td>{{ ucfirst($order->status) }}</td>
                                            <td>{{ $order->created_at->format('d M Y') }}</td>
                                            <td><span class="card-price">৳{{ number_format($order->total, 2) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-3">
                                <div class="subtotal-box p-3 bg-light rounded">
                                    <h5 class="mb-1 fw-bold">Receivable (Due): <span class="text-danger">৳{{ number_format($receivable, 2) }}</span></h5>
                                    <h5 class="mb-0 fw-bold">Paid: <span class="text-success">৳{{ number_format($paid, 2) }}</span></h5>
                                </div>
                            </div>
                            @if($hasPendingOrders)
                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <a href="{{ route('home') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-arrow-left-circle"></i> Continue Shopping
                                    </a>
                                    <a href="{{ route('pay-pending-orders') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-credit-card"></i> Pay Now
                                    </a>
                                </div>
                            @else
                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <a href="{{ route('home') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-arrow-left-circle"></i> Continue Shopping
                                    </a>
                                </div>
                            @endif
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
            padding: 2rem 0;
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
            color: #1a1a1a;
            margin-bottom: 1.5rem;
            font-family: 'Inter', sans-serif;
        }

        .card {
            border: none;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
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

        .card-title {
            font-size: 1.2rem;
            font-weight: 500;
            color: #1a1a1a;
            font-family: 'Inter', sans-serif;
        }

        .card-price {
            font-size: 1rem;
            font-weight: 600;
            color: #2563eb;
        }

        .text-muted {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f1f5f9;
            color: #1a1a1a;
            font-weight: 600;
            padding: 0.75rem;
            font-family: 'Inter', sans-serif;
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
            border-top: 1px solid #e5e7eb;
            font-family: 'Inter', sans-serif;
            color: #2d3748;
            font-size: 0.9rem;
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

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: #16a34a;
            border: none;
        }

        .btn-success:hover {
            background-color: #15803d;
            transform: translateY(-2px);
        }

        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
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

            .table thead th,
            .table tbody td {
                padding: 0.5rem;
                font-size: 0.85rem;
            }

            .table img {
                width: 40px;
                height: 40px;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .d-flex.gap-2 {
                flex-direction: column;
            }

            .subtotal-box {
                width: 100%;
                text-align: center;
            }
        }

        .logout-btn {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #dc3545;
            color: #fff;
            border-color: #dc3545;
            transform: translateY(-2px);
        }

        .logout-btn i {
            margin-right: 5px;
        }
    </style>
@endsection