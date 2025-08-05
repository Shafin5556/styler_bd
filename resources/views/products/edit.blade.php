@extends('layouts.app')

@section('content')
    <h1>Edit Product</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control">{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $product->price) }}" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="image">Image (PNG only)</label>
            <input type="file" name="image" id="image" class="form-control-file" accept="image/png">
            @if($product->image)
                <div class="mt-2">
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="width: 100px; height: auto;">
                    <p>Current Image</p>
                </div>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Update Product</button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection