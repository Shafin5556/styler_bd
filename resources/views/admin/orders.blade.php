@extends('layouts.app')

@section('content')
    <div class="admin-section">
        <div class="container">
            <!-- Header with Admin Details and Back Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-profile-pic">
                        @if($admin->profile_picture)
                            <img src="{{ asset($admin->profile_picture) }}" alt="{{ $admin->name }}" class="rounded-circle">
                        @else
                            <div class="profile-placeholder rounded-circle d-flex align-items-center justify-content-center">
                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="admin-title mb-0">{{ $admin->name }}</h1>
                        <p class="admin-info mb-0 text-muted">{{ $admin->email }} • {{ ucfirst($admin->role) }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            </div>



            <!-- Orders Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Orders</h4>
                    <form action="{{ route('admin.orders') }}" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by user name or email..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                    </form>
                </div>
                <div class="card-body">
                    @if($orders->isEmpty())
                        <p class="text-muted text-center">No orders found.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover" id="orders-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>User</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr data-search="{{ strtolower($order->user->name . ' ' . $order->user->email) }}">
                                            <td>{{ $order->id }}</td>
                                            <td>
                                                <div>{{ $order->user->name }}</div>
                                                <small class="text-muted">{{ $order->user->email }}</small>
                                            </td>
                                            <td>৳{{ number_format($order->total, 2) }}</td>
                                            <td>
                                                <select class="form-control form-control-sm status-select" data-order-id="{{ $order->id }}">
                                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                </select>
                                            </td>
                                            <td>{{ $order->created_at->format('d M Y') }}</td>
                                            <td>
                                                <button class="btn btn-primary btn-sm update-status" data-order-id="{{ $order->id }}">Update</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.querySelector('input[name="search"]');
            const tableRows = document.querySelectorAll('#orders-table tbody tr');

            // Client-side search filtering
            searchInput.addEventListener('input', function () {
                const searchTerm = searchInput.value.toLowerCase();

                tableRows.forEach(row => {
                    const searchData = row.getAttribute('data-search');
                    if (searchData.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show "No orders found" if no rows are visible
                const tableBody = document.querySelector('#orders-table tbody');
                const noOrdersMessage = document.querySelector('.text-muted.text-center');
                const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
                if (visibleRows.length === 0) {
                    if (!noOrdersMessage) {
                        const message = document.createElement('p');
                        message.className = 'text-muted text-center';
                        message.textContent = 'No orders found.';
                        tableBody.parentElement.appendChild(message);
                    }
                } else {
                    if (noOrdersMessage) {
                        noOrdersMessage.remove();
                    }
                }
            });

            // Status update handling
            $('.update-status').on('click', function() {
                const orderId = $(this).data('order-id');
                const status = $(`select[data-order-id="${orderId}"]`).val();

                $.ajax({
                    url: '{{ route("admin.orders.updateStatus") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order_id: orderId,
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            // const alert = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                            //     ${response.message}
                            //     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            // </div>`;
                            // $('.container').prepend(alert);
                            // setTimeout(() => $('.alert').alert('close'), 3000);
                        }
                    },
                    error: function(xhr) {
                        const alert = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Failed to update order status. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`;
                        $('.container').prepend(alert);
                        setTimeout(() => $('.alert').alert('close'), 3000);
                    }
                });
            });
        });
    </script>

    <style>
        .admin-section {
            padding: 2rem 0;
            background-color: #f8f9fa;
        }
        .admin-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0;
        }
        .admin-info {
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: #4b5563;
        }
        .admin-profile-pic img, .profile-placeholder {
            width: 48px;
            height: 48px;
            object-fit: cover;
            background-color: #e5e7eb;
            color: #6b7280;
            font-weight: 600;
            font-size: 1.25rem;
            font-family: 'Inter', sans-serif;
        }
        .profile-placeholder {
            text-align: center;
            line-height: 48px;
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
        .btn-primary {
            background-color: #2563eb;
            border: none;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #1e40af;
            transform: translateY(-2px);
        }
        .btn-outline-secondary {
            border-color: #6b7280;
            color: #6b7280;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-outline-secondary:hover {
            background-color: #6b7280;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-sm {
            padding: 0.2rem 0.5rem;
        }
        .btn i {
            margin-right: 4px;
        }
        .card {
            border: none;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            border-bottom: none;
            border-radius: 12px 12px 0 0;
            padding: 1rem 1.5rem;
            background-color: #fff;
        }
        .card-header h4 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
        }
        .card-body {
            padding: 1.5rem;
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
            background-color: #ffffff;
            margin-bottom: 0;
        }
        .table th {
            background-color: #f1f5f9;
            color: #1a1a1a;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            padding: 1rem;
        }
        .table td {
            vertical-align: middle;
            padding: 1rem;
            font-family: 'Inter', sans-serif;
            color: #2d3748;
        }
        .table-hover tbody tr:hover {
            background-color: #f8fafc;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        .text-muted {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
        }
        .form-control {
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            border: 1px solid #d1d5db;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 8px rgba(37, 99, 235, 0.2);
        }
        .form-control-sm {
            max-width: 200px;
        }
        .status-select {
            width: 120px;
        }
        @media (max-width: 991px) {
            .admin-title {
                font-size: 1.5rem;
            }
            .admin-info {
                font-size: 0.85rem;
            }
            .admin-profile-pic img, .profile-placeholder {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            .card-header h4 {
                font-size: 1.1rem;
            }
            .table {
                font-size: 0.85rem;
            }
            .form-control-sm {
                max-width: 150px;
            }
            .status-select {
                width: 100px;
            }
        }
        @media (max-width: 767px) {
            .admin-section {
                padding: 1.5rem 0;
            }
            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .card-header .d-flex {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .form-control-sm {
                max-width: 100%;
            }
            .btn-sm {
                width: 100%;
                text-align: center;
            }
            .admin-profile-pic img, .profile-placeholder {
                width: 36px;
                height: 36px;
                font-size: 0.9rem;
            }
            .status-select {
                width: 100%;
            }
        }
    </style>
@endsection