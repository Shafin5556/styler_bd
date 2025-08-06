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

            <!-- Categories Table -->
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Existing Categories & Subcategories</h4>
                </div>
                <div class="card-body">
                    @if($categories->isEmpty())
                        <p class="text-muted">No categories found.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Parent Category</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td class="fw-medium">{{ $category->name }}</td>
                                            <td>{{ $category->parent ? $category->parent->name : 'None' }}</td>
                                            <td>
                                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @foreach($category->subcategories as $subcategory)
                                            <tr>
                                                <td>↳ {{ $subcategory->name }}</td>
                                                <td>{{ $category->name }}</td>
                                                <td>
                                                    <form action="{{ route('categories.destroy', $subcategory->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this subcategory?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Forms Section -->
            <div class="row">
                <!-- Add Category Form -->
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Add New Category</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('categories.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="category_name" class="form-label">Category Name</label>
                                    <input type="text" name="name" id="category_name" class="form-control" value="{{ old('name') }}" required>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-plus-circle"></i> Add Category</button>
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Add Subcategory Form -->
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Add New Subcategory</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('categories.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="subcategory_name" class="form-label">Subcategory Name</label>
                                    <input type="text" name="name" id="subcategory_name" class="form-control" value="{{ old('name') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="parent_id" class="form-label">Parent Category</label>
                                    <select name="parent_id" id="parent_id" class="form-control" required>
                                        <option value="">Select Parent Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-plus-circle"></i> Add Subcategory</button>
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
        .admin-section { padding: 3rem 0; }
        .alert { border-radius: 8px; margin-bottom: 2rem; }
        .alert-success { background-color: #e6f4ea; border-color: #28a745; color: #1e7e34; }
        .alert-danger { background-color: #f8d7da; border-color: #dc3545; color: #721c24; }
        .table { border-radius: 8px; overflow: hidden; background-color: #ffffff; }
        .table th { background-color: #f8f9fa; color: #222; font-weight: 600; }
        .table td { vertical-align: middle; }
        .table-hover tbody tr:hover { background-color: #f1f3f5; }
        .card { border: none; border-radius: 12px; transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); }
        .card-header { border-bottom: none; border-radius: 12px 12px 0 0; padding: 1rem 1.5rem; }
        .card-body { padding: 1.5rem; }
        .form-label { font-weight: 500; color: #222; }
        .form-control { border-radius: 8px; border: 1px solid #ddd; transition: all 0.3s ease; }
        .form-control:focus { border-color: #007bff; box-shadow: 0 0 8px rgba(0, 123, 255, 0.3); }
        .btn-primary { background-color: #007bff; border: none; border-radius: 8px; font-weight: 500; transition: background-color 0.3s ease; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-secondary { background-color: #6c757d; border: none; border-radius: 8px; font-weight: 500; transition: background-color 0.3s ease; }
        .btn-secondary:hover { background-color: #5a6268; }
        .btn i { margin-right: 6px; }
        @media (max-width: 767px) {
            .admin-section { padding: 2rem 0; }
            .table { font-size: 0.9rem; }
            .btn-sm { font-size: 0.8rem; padding: 0.3rem 0.6rem; }
        }
    </style>
@endsection