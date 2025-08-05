@extends('layouts.app')

@section('content')
    <h1>Admin Dashboard</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Add New Product</a>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>৳{{ $product->price }}</td>
                    <td>{{ $product->category->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection