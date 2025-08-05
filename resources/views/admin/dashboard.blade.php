@extends('layouts.app')

@section('content')
    <h1>Admin Dashboard</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="mb-3">
        <a href="{{ route('products.create') }}" class="btn btn-primary me-2">Add New Product</a>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">Add Category</a>
    </div>
    <table class="table table-bordered">
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
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="width: 100px; height: auto;">
                        @else
                            No Image
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>৳{{ $product->price }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning me-1">Edit</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection