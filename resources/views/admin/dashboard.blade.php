@extends('layouts.app')

@section('content')
    <div class="admin-section">
        <div class="container">

            <!-- Alerts -->
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

            <!-- Action Buttons -->
            <div class="d-flex gap-2 mb-4">
                <a href="{{ route('products.create') }}" class="btn btn-primary flex-grow-1"><i class="bi bi-plus-circle"></i> Add New Product</a>
                <!-- <a href="{{ route('categories.create') }}" class="btn btn-primary flex-grow-1"><i class="bi bi-tags"></i> Add Category</a> -->
            </div>

            <!-- Products Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Products</h4>
                </div>
                <div class="card-body">
                    @if($products->isEmpty())
                        <p class="text-muted text-center">No products found.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Category</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                @if($product->image)
                                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-width: 80px; height: auto;">
                                                @else
                                                    <span class="text-muted">No Image</span>
                                                @endif
                                            </td>
                                            <td class="fw-medium">{{ $product->name }}</td>
                                            <td>৳{{ number_format($product->price, 2) }}</td>
                                            <td>{{ $product->category->name }}</td>
                                            <td>
                                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil"></i> Edit</a>
                                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                                                </form>
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

    <style>
        /* Admin Section Styling */
        .admin-section {
            padding: 3rem 0;
        }
        .admin-title {
            font-size: 2.8rem;
            font-weight: 600;
            color: #222;
            text-align: center;
            margin-bottom: 1rem;
        }
        .admin-subtitle {
            font-size: 1.3rem;
            color: #555;
            text-align: center;
            margin-bottom: 3rem;
        }
        /* Alert Styling */
        .alert {
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .alert-success {
            background-color: #e6f4ea;
            border-color: #28a745;
            color: #1e7e34;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        /* Button Styling */
        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-warning {
            background-color: #ffc107;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .btn-danger:hover {
            background-color: #b02a37;
        }
        .btn i {
            margin-right: 6px;
        }
        /* Table Styling */
        .card {
            border: none;
            border-radius: 12px;
            background-color: #ffffff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        .card-header {
            border-bottom: none;
            border-radius: 12px 12px 0 0;
            padding: 1rem 1.5rem;
        }
        .card-body {
            padding: 1.5rem;
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
            background-color: #ffffff;
        }
        .table th {
            background-color: #f8f9fa;
            color: #222;
            font-weight: 600;
        }
        .table td {
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
        }
        .table img {
            max-width: 80px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        @media (max-width: 991px) {
            .admin-title {
                font-size: 2.2rem;
            }
            .admin-subtitle {
                font-size: 1.1rem;
            }
            .card-body {
                padding: 1.2rem;
            }
            .table {
                font-size: 0.9rem;
            }
            .btn-sm {
                font-size: 0.8rem;
                padding: 0.3rem 0.6rem;
            }
        }
        @media (max-width: 767px) {
            .admin-section {
                padding: 2rem 0;
            }
            .d-flex.gap-2 {
                flex-direction: column;
                gap: 1rem;
            }
            .btn {
                width: 100%;
            }
        }
    </style>
@endsection