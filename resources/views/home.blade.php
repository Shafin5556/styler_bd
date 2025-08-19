@extends('layouts.app')

@section('content')
    <div class="home-section">
        <!-- Hero Carousel -->
        <div class="hero-section">
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-inner">
                    @foreach($featuredProducts as $index => $product)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <a href="{{ route('shop') }}">
                                <div class="hero-image" style="background-image: url('{{ $product->images->isNotEmpty() ? asset($product->images->first()->image) : asset('asset/logo/shop_now.gif') }}')">
                                    <div class="hero-overlay">
                                        <h1 class="hero-title">{{ $product->name }}</h1>
                                        <p class="hero-subtitle">Discover Now</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                @if($featuredProducts->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Product Gallery -->
        <div class="container gallery-section">
            <h2 class="section-title">Our Collection</h2>
            <p class="section-subtitle">Explore our latest products</p>
            <div class="row">
                @if($products->isEmpty())
                    <p class="text-muted text-center w-100">No products available.</p>
                @else
                    @foreach($products as $product)
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="gallery-card" data-bs-toggle="modal" data-bs-target="#productModal{{ $product->id }}">
                                @if($product->images->isNotEmpty())
                                    <div class="gallery-image">
                                        <img src="{{ asset($product->images->first()->image) }}" alt="{{ $product->name }}" class="img-fluid">
                                        <div class="gallery-overlay">
                                            <h5 class="gallery-title-text">{{ $product->name }}</h5>
                                        </div>
                                    </div>
                                @else
                                    <div class="gallery-image bg-light d-flex align-items-center justify-content-center">
                                        <span>No Image</span>
                                        <div class="gallery-overlay">
                                            <h5 class="gallery-title-text">{{ $product->name }}</h5>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Modal -->
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
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- New Arrivals -->
        <div class="container new-arrivals-section">
            <h2 class="section-title">New Arrivals</h2>
            <p class="section-subtitle">Check out our latest additions</p>
            <div class="row">
                @foreach($featuredProducts as $product)
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                        <div class="gallery-card" data-bs-toggle="modal" data-bs-target="#productModal{{ $product->id }}">
                            @if($product->images->isNotEmpty())
                                <div class="gallery-image">
                                    <img src="{{ asset($product->images->first()->image) }}" alt="{{ $product->name }}" class="img-fluid">
                                    <div class="gallery-overlay">
                                        <h5 class="gallery-title-text">{{ $product->name }}</h5>
                                    </div>
                                </div>
                            @else
                                <div class="gallery-image bg-light d-flex align-items-center justify-content-center">
                                    <span>No Image</span>
                                    <div class="gallery-overlay">
                                        <h5 class="gallery-title-text">{{ $product->name }}</h5>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Home Section */
        .home-section {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }

        /* Hero Carousel */
        .hero-section {
            margin-bottom: 3rem;
        }
        .hero-image {
            height: 500px;
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-overlay {
            text-align: center;
            color: #ffffff;
            background: rgba(0, 0, 0, 0.5);
            padding: 2rem;
            border-radius: 8px;
        }
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .hero-subtitle {
            font-size: 1.2rem;
            font-weight: 400;
        }
        .carousel-control-prev, .carousel-control-next {
            width: 5%;
            background: rgba(0, 0, 0, 0.3);
        }
        .carousel-control-prev-icon, .carousel-control-next-icon {
            filter: invert(1);
        }

        /* Section Titles */
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .section-subtitle {
            font-size: 1.1rem;
            color: #4b5563;
            text-align: center;
            margin-bottom: 2rem;
        }

        /* Product Gallery & New Arrivals */
        .gallery-section, .new-arrivals-section {
            padding: 2rem 0;
        }
        .gallery-card {
            display: block;
            text-decoration: none;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .gallery-card:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        .gallery-image {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        .gallery-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }
        .gallery-image.bg-light {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
            height: 200px;
        }
        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.6);
            padding: 0.75rem;
            transition: background 0.3s ease;
        }
        .gallery-card:hover .gallery-overlay {
            background: rgba(0, 0, 0, 0.7);
        }
        .gallery-title-text {
            font-size: 1rem;
            font-weight: 500;
            color: #ffffff;
            margin: 0;
            text-align: center;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
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
        }
        .modal-body {
            padding: 1.5rem;
        }
        .modal-body strong {
            font-weight: 600;
            color: #1a1a1a;
        }
        .modal-body p {
            margin-bottom: 1rem;
            color: #2d3748;
        }
        .modal-carousel img {
            max-height: 350px;
            object-fit: contain;
            border-radius: 8px;
        }
        .carousel-control-prev, .carousel-control-next {
            width: 10%;
            background: rgba(0, 0, 0, 0.3);
        }
        .input-group .form-control.quantity-input {
            max-width: 60px;
            border-radius: 8px 0 0 8px;
            font-size: 0.9rem;
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
            font-weight: 500;
            padding: 0.75rem;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background-color: #1e40af;
            transform: translateY(-2px);
        }
        .btn-outline-primary {
            border-color: #2563eb;
            color: #2563eb;
            border-radius: 8px;
        }
        .btn-outline-primary:hover {
            background-color: #2563eb;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-outline-secondary {
            border-color: #6b7280;
            color: #6b7280;
            border-radius: 8px;
        }
        .btn-outline-secondary:hover {
            background-color: #6b7280;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn i {
            margin-right: 6px;
        }

        /* Customer Reviews */
        .reviews-section {
            padding: 2rem 0;
            background-color: #ffffff;
        }
        .review-card {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .review-text {
            font-size: 1rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        .review-author {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
        }

        /* Promotional Banner */
        .banner-section {
            padding: 2rem 0;
            text-align: center;
        }
        .banner-image {
            width: 100%;
            max-width: 1200px;
            height: auto;
            border-radius: 8px;
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .hero-image {
                height: 400px;
            }
            .hero-title {
                font-size: 2rem;
            }
            .hero-subtitle {
                font-size: 1rem;
            }
            .section-title {
                font-size: 1.8rem;
            }
            .section-subtitle {
                font-size: 1rem;
            }
            .gallery-image {
                height: 180px;
            }
            .gallery-title-text {
                font-size: 0.9rem;
            }
            .modal-carousel img {
                max-height: 250px;
            }
        }
        @media (max-width: 767px) {
            .hero-image {
                height: 300px;
            }
            .hero-title {
                font-size: 1.5rem;
            }
            .hero-subtitle {
                font-size: 0.9rem;
            }
            .gallery-section, .new-arrivals-section, .reviews-section, .banner-section {
                padding: 1.5rem 0;
            }
            .gallery-image {
                height: 150px;
            }
            .gallery-title-text {
                font-size: 0.85rem;
            }
            .review-card {
                padding: 1rem;
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
            .modal-carousel img {
                max-height: 200px;
            }
        }
    </style>
@endsection