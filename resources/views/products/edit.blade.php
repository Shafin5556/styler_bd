@extends('layouts.app')

@section('content')
    <div class="admin-section">
        <div class="container">
            <!-- Alerts -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Edit Product Form -->
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Update Product</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $product->price) }}" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Subcategory</label>
                                    <select name="category_id" id="category_id" class="form-control" required>
                                        <option value="">Select Subcategory</option>
                                        @foreach ($categories as $category)
                                            @foreach ($category->subcategories as $subcategory)
                                                <option value="{{ $subcategory->id }}" {{ old('category_id', $product->category_id) == $subcategory->id ? 'selected' : '' }}>{{ $category->name }} > {{ $subcategory->name }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="images" class="form-label">Images (PNG only, up to 5 total)</label>
                                    <input type="file" name="images[]" id="images" class="form-control" accept="image/png" multiple>
                                    @error('images.*')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('images')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    @if($product->images->isNotEmpty())
                                        <div class="image-preview mt-3">
                                            <p class="text-muted">Current Images (check to delete):</p>
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach($product->images as $image)
                                                    <div class="image-item">
                                                        <img src="{{ asset($image->image) }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-width: 100px;">
                                                        <div class="form-check mt-2">
                                                            <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="form-check-input" id="delete_image_{{ $image->id }}">
                                                            <label class="form-check-label" for="delete_image_{{ $image->id }}">Delete</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-save"></i> Update Product</button>
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
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
        .alert-danger {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        /* Card Styling */
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
        /* Form Styling */
        .form-label {
            font-weight: 500;
            color: #222;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }
        .form-control-file {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 0.5rem;
        }
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
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn i {
            margin-right: 6px;
        }
        /* Image Preview Styling */
        .image-preview img {
            max-width: 100px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .image-preview p {
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        .image-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .form-check-label {
            font-size: 0.85rem;
            color: #555;
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
        }
        @media (max-width: 767px) {
            .admin-section {
                padding: 2rem 0;
            }
            .image-preview img {
                max-width: 80px;
            }
        }
    </style>
@endsection