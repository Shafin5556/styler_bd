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
                                <div class="input-group">
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Search by product name..." value="{{ request('name') }}">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                                </div>
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
                                    <div class="d-flex justify-content-between mt-3 gap-2">
                                        <input type="text" id="range-from-display" class="form-control" value="{{ request('min_price') ? (int)request('min_price') : floor($minPrice ?? 0) }}">
                                        <input type="text" id="range-to-display" class="form-control" value="{{ request('max_price') ? (int)request('max_price') : ceil($maxPrice ?? 100) }}">
                                        <input type="hidden" id="range-from" name="min_price">
                                        <input type="hidden" id="range-to" name="max_price">
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Button -->
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </form>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="col-lg-9 col-md-8">
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                <div class="card product-card h-100" data-bs-toggle="modal" data-bs-target="#productModal{{ $product->id }}">
                                    @if($product->images->isNotEmpty())
                                        <div id="productCarousel{{ $product->id }}" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                                            <div class="carousel-inner">
                                                @foreach($product->images as $index => $image)
                                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                        <img src="{{ asset($image->image) }}" class="d-block w-100 card-img-top" alt="{{ $product->name }} Image {{ $index + 1 }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if($product->images->count() > 1)
                                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel{{ $product->id }}" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Previous</span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel{{ $product->id }}" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Next</span>
                                                </button>
                                            @endif
                                        </div>
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
                                                    <button type="submit" class="btn btn-primary"><i class="bi bi-cart-plus"></i></button>
                                                </div>
                                            </form>
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Login to Add</a>
                                        @endauth
                                    </div>
                                </div>
                            </div>

                            <!-- Product Details Modal -->
                            <div class="modal fade" id="productModal{{ $product->id }}" tabindex="-1" aria-labelledby="productModalLabel{{ $product->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="productModalLabel{{ $product->id }}">{{ $product->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    @if($product->images->isNotEmpty())
                                                        <div id="productModalCarousel{{ $product->id }}" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-interval="3000">
                                                            <div class="carousel-inner">
                                                                @foreach($product->images as $index => $image)
                                                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                                        <img src="{{ asset($image->image) }}" class="d-block w-100" alt="{{ $product->name }} Image {{ $index + 1 }}">
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            @if($product->images->count() > 1)
                                                                <button class="carousel-control-prev" type="button" data-bs-target="#productModalCarousel{{ $product->id }}" data-bs-slide="prev">
                                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                    <span class="visually-hidden">Previous</span>
                                                                </button>
                                                                <button class="carousel-control-next" type="button" data-bs-target="#productModalCarousel{{ $product->id }}" data-bs-slide="next">
                                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                    <span class="visually-hidden">Next</span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <p class="text-center">No Images Available</p>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Description:</strong> {{ $product->description ?? 'No description available.' }}</p>
                                                    <p><strong>Price:</strong> ৳{{ number_format($product->price, 2) }}</p>
                                                    <p><strong>Category:</strong> {{ $product->category->name }}</p>
                                                    <p><strong>Created At:</strong> {{ $product->created_at->format('d M Y') }}</p>
                                                    <p><strong>Updated At:</strong> {{ $product->updated_at->format('d M Y') }}</p>
                                                    @auth
                                                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                                            @csrf
                                                            <div class="input-group mb-3">
                                                                <input type="number" name="quantity" class="form-control quantity-input" value="1" min="1" required>
                                                                <button type="submit" class="btn btn-primary"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                                                            </div>
                                                        </form>
                                                    @else
                                                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Login to Add</a>
                                                    @endauth
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if($products->isEmpty())
                            <p class="text-muted text-center w-100">No products found.</p>
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
            const slider = document.getElementById('range-slider');
            const minDisplay = document.getElementById('range-from-display');
            const maxDisplay = document.getElementById('range-to-display');
            const minInput = document.getElementById('range-from');
            const maxInput = document.getElementById('range-to');

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
                    minDisplay.value = values[0];
                    minInput.value = values[0];
                } else {
                    maxDisplay.value = values[1];
                    maxInput.value = values[1];
                }
            });

            minDisplay.addEventListener('change', function () {
                let value = parseInt(minDisplay.value) || {{ $minPrice }};
                value = Math.max({{ $minPrice }}, Math.min(value, slider.noUiSlider.get()[1]));
                slider.noUiSlider.set([value, null]);
            });

            maxDisplay.addEventListener('change', function () {
                let value = parseInt(maxDisplay.value) || {{ $maxPrice }};
                value = Math.min({{ $maxPrice }}, Math.max(value, slider.noUiSlider.get()[0]));
                slider.noUiSlider.set([null, value]);
            });
        });
    </script>

    <style>
        /* Shop Section Styling */
        .shop-section {
            padding: 2rem 0;
            background-color: #f8f9fa;
        }
        .shop-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 0.5rem;
            font-family: 'Inter', sans-serif;
        }
        .shop-subtitle {
            font-size: 1.1rem;
            color: #4b5563;
            text-align: center;
            margin-bottom: 2rem;
            font-family: 'Inter', sans-serif;
        }
        /* Filter Sidebar Styling */
        .filter-sidebar {
            background-color: #ffffff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .filter-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
            font-family: 'Inter', sans-serif;
        }
        .form-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #2d3748;
            font-family: 'Inter', sans-serif;
        }
        .form-control, .btn {
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .form-control {
            border: 1px solid #d1d5db;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 8px rgba(37, 99, 235, 0.2);
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
            font-weight: 500;
            padding: 0.75rem;
        }
        .btn-primary:hover {
            background-color: #1e40af;
            transform: translateY(-2px);
        }
        .btn-outline-primary {
            border-color: #2563eb;
            color: #2563eb;
        }
        .btn-outline-primary:hover {
            background-color: #2563eb;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-outline-secondary {
            border-color: #6b7280;
            color: #6b7280;
        }
        .btn-outline-secondary:hover {
            background-color: #6b7280;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn i {
            margin-right: 6px;
        }
        /* Price Slider Styling */
        .price-filter {
            padding: 10px 0;
        }
        .noUi-target {
            margin: 1.5rem 0;
            border: none;
            box-shadow: none;
            background: none;
        }
        .noUi-connect {
            background: #2563eb;
        }
        .noUi-handle {
            background: #2563eb;
            border-radius: 50%;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
            border: 2px solid #fff;
            cursor: grab;
        }
        .noUi-handle:active {
            cursor: grabbing;
        }
        .price-filter input.form-control {
            width: 90px;
            text-align: center;
            border: 1px solid #d1d5db;
        }
        /* Product Card Styling */
        .product-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .card-img-top.bg-light {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
            height: 200px;
            font-family: 'Inter', sans-serif;
        }
        .card-body {
            padding: 1.25rem;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
            font-family: 'Inter', sans-serif;
        }
        .card-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2563eb;
            margin-bottom: 1rem;
        }
        .input-group .form-control.quantity-input {
            max-width: 60px;
            border-radius: 8px 0 0 8px;
            font-size: 0.9rem;
        }
        .carousel-control-prev, .carousel-control-next {
            width: 10%;
            background: rgba(0, 0, 0, 0.3);
        }
        .carousel-control-prev-icon, .carousel-control-next-icon {
            filter: invert(1);
        }
        /* Modal Styling */
        .modal-content {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            border-bottom: none;
            background-color: #ffffff;
            border-radius: 12px 12px 0 0;
            padding: 1.25rem;
        }
        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a1a1a;
            font-family: 'Inter', sans-serif;
        }
        .modal-body {
            padding: 1.5rem;
        }
        .modal-body strong {
            font-weight: 600;
            color: #1a1a1a;
            font-family: 'Inter', sans-serif;
        }
        .modal-body p {
            margin-bottom: 1rem;
            color: #2d3748;
            font-family: 'Inter', sans-serif;
        }
        .modal-carousel img {
            max-height: 350px;
            object-fit: contain;
            border-radius: 8px;
        }
        .text-muted {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #6b7280;
        }
        @media (max-width: 991px) {
            .shop-title {
                font-size: 2rem;
            }
            .shop-subtitle {
                font-size: 1rem;
            }
            .filter-sidebar {
                padding: 1.25rem;
            }
            .card-img-top, .carousel-inner img {
                height: 180px;
            }
            .modal-carousel img {
                max-height: 250px;
            }
            .price-filter input.form-control {
                width: 80px;
            }
        }
        @media (max-width: 767px) {
            .shop-section {
                padding: 1.5rem 0;
            }
            .filter-sidebar {
                margin-bottom: 2rem;
            }
            .card-img-top, .carousel-inner img {
                height: 160px;
            }
            .modal-carousel img {
                max-height: 200px;
            }
            .input-group {
                flex-direction: column;
                gap: 0.5rem;
            }
            .input-group .form-control.quantity-input {
                max-width: 100%;
                border-radius: 8px;
            }
            .input-group .btn {
                width: 100%;
            }
        }
    </style>
@endsection