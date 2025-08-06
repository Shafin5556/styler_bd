@extends('layouts.app')

@section('content')
    <div class="shop-section">
        <div class="container">


            <div class="row">
                <!-- Filter Sidebar -->
                <div class="col-lg-3 col-md-4 mb-4">
                    <div class="filter-sidebar">
                        <h4 class="filter-title">Filters</h4>
                        <form action="{{ route('shop') }}" method="GET">
                            <!-- Product Name Search -->
                            <div class="mb-4">
                                <label for="name" class="form-label">Search Products</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter product name" value="{{ request('name') }}">
                            </div>

                            <!-- Category/Subcategory Dropdown -->
                            <div class="mb-4">
                                <label for="category_id" class="form-label">Category</label>
                                <select name="category_id" id="category_id" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @foreach($category->subcategories as $subcategory)
                                            <option value="{{ $subcategory->id }}" {{ request('category_id') == $subcategory->id ? 'selected' : '' }}>&nbsp;&nbsp;↳ {{ $subcategory->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>

                            <!-- Price Range Slider -->
                            <div class="mb-4">
                                <label class="form-label">Price Range</label>
                                <div class="price-filter">
                                    <div id="range-slider" class="noUi-target noUi-ltr noUi-horizontal"></div>
                                    <div class="d-flex justify-content-between mt-3">
                                        <input type="text" id="range-from-display" class="form-control" value="{{ request('min_price') ? (int)request('min_price') : floor($minPrice ?? 0) }}">
                                        <input type="text" id="range-to-display" class="form-control" value="{{ request('max_price') ? (int)request('max_price') : ceil($maxPrice ?? 100) }}">
                                        <input type="hidden" id="range-from" name="min_price">
                                        <input type="hidden" id="range-to" name="max_price">
                                    </div>
                                </div>
                            </div>

                            <!-- Filter and Dress Up Buttons -->
                            <div class="d-flex flex-column gap-2">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <a href="{{ route('dressup') }}" class="btn btn-success">Dress Up</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="col-lg-9 col-md-8">
    
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                <div class="card product-card h-100">
                                    @if($product->image)
                                        <img src="{{ asset($product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                                    @else
                                        <div class="card-img-top text-center bg-light d-flex align-items-center justify-content-center">No Image</div>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <p class="card-price">৳{{ number_format($product->price, 2) }}</p>
                                        @auth
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                                @csrf
                                                <div class="input-group">
                                                    <input type="number" name="quantity" class="form-control quantity-input" value="1" min="1" required>
                                                    <button type="submit" class="btn btn-primary"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                                                </div>
                                            </form>
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Login to Add</a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if($products->isEmpty())
                            <p class="text-muted text-center">No products found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include noUiSlider CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var slider = document.getElementById('range-slider');
            var minDisplay = document.getElementById('range-from-display');
            var maxDisplay = document.getElementById('range-to-display');
            var minInput = document.getElementById('range-from');
            var maxInput = document.getElementById('range-to');

            @php
                $minPrice = floor($minPrice ?? 0);
                $maxPrice = ceil($maxPrice ?? 100);
            @endphp

            noUiSlider.create(slider, {
                start: [
                    {{ request('min_price') ? (int)request('min_price') : $minPrice }},
                    {{ request('max_price') ? (int)request('max_price') : $maxPrice }}
                ],
                connect: true,
                range: {
                    'min': {{ $minPrice }},
                    'max': {{ $maxPrice }}
                },
                step: 1,
                format: {
                    to: function (value) {
                        return Math.round(value);
                    },
                    from: function (value) {
                        return Number(value);
                    }
                }
            });

            slider.noUiSlider.on('update', function (values, handle) {
                if (handle === 0) {
                    minDisplay.value = '৳' + values[0];
                    minInput.value = values[0];
                } else {
                    maxDisplay.value = '৳' + values[1];
                    maxInput.value = values[1];
                }
            });

            minDisplay.addEventListener('change', function () {
                var value = parseInt(minDisplay.value.replace('৳', '')) || {{ $minPrice }};
                value = Math.max({{ $minPrice }}, Math.min(value, slider.noUiSlider.get()[1]));
                slider.noUiSlider.set([value, null]);
            });

            maxDisplay.addEventListener('change', function () {
                var value = parseInt(maxDisplay.value.replace('৳', '')) || {{ $maxPrice }};
                value = Math.min({{ $maxPrice }}, Math.max(value, slider.noUiSlider.get()[0]));
                slider.noUiSlider.set([null, value]);
            });
        });
    </script>

    <style>
        /* Shop Section Styling */
        .shop-section {
            padding: 1rem 0;
        }
        .shop-title {
            font-size: 2.8rem;
            font-weight: 600;
            color: #222;
            text-align: center;
            margin-bottom: 1rem;
        }
        .shop-subtitle {
            font-size: 1.3rem;
            color: #555;
            text-align: center;
            margin-bottom: 3rem;
        }
        /* Filter Sidebar Styling */
        .filter-sidebar {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .filter-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 1.5rem;
        }
        .form-control, .btn {
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
            font-weight: 500;
        }
        .btn-success:hover {
            background-color: #1e7e34;
        }
        /* Price Slider Styling */
        .price-filter {
            padding: 10px 0;
        }
        .noUi-target {
            margin: 20px 0;
            border: none;
            box-shadow: none;
            background: none;
        }
        .noUi-connect {
            background: #007bff;
        }
        .noUi-handle {
            background: #007bff;
            border-radius: 50%;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            border: 2px solid #fff;
            cursor: grab;
        }
        .noUi-handle:active {
            cursor: grabbing;
        }
        .price-filter input.form-control {
            width: 100px;
            text-align: center;
            border: 1px solid #ddd;
        }
        /* Product Card Styling */
        .product-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            background-color: #ffffff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        .card-img-top {
            height: 220px;
            object-fit: cover;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .card-img-top.bg-light {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 1rem;
            height: 220px;
        }
        .card-body {
            padding: 1.5rem;
        }
        .card-title {
            font-size: 1.3rem;
            font-weight: 500;
            color: #222;
            margin-bottom: 0.5rem;
        }
        .card-price {
            font-size: 1.2rem;
            font-weight: 600;
            color: #007bff;
            margin-bottom: 1rem;
        }
        .input-group .form-control.quantity-input {
            max-width: 70px;
            border-radius: 8px 0 0 8px;
        }
        .btn i {
            margin-right: 6px;
        }
        @media (max-width: 991px) {
            .filter-sidebar {
                padding: 1.5rem;
            }
            .shop-title {
                font-size: 2.2rem;
            }
            .shop-subtitle {
                font-size: 1.1rem;
            }
            .card-img-top, .card-img-top.bg-light {
                height: 180px;
            }
        }
        @media (max-width: 767px) {
            .filter-sidebar {
                margin-bottom: 2rem;
            }
            .products-title {
                text-align: center;
            }
        }
    </style>
@endsection