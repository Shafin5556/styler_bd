@extends('layouts.app')

@section('content')
    <h1>Styler BD</h1>
    <div class="mb-4">
        <form action="{{ route('home') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Search by product name" value="{{ request('name') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Price Range</label>
                <div class="price-filter ws-box">
                    <div id="range-slider" class="noUi-target noUi-ltr noUi-horizontal"></div>
                    <div class="d-flex justify-content-between mt-2">
                        <div class="range-label">
                            <input type="text" id="range-from-display" class="form-control" value="{{ request('min_price') ? (int)request('min_price') : floor($minPrice ?? 0) }}">
                            <input type="hidden" id="range-from" name="min_price">
                        </div>
                        <div class="range-label">
                            <input type="text" id="range-to-display" class="form-control" value="{{ request('max_price') ? (int)request('max_price') : ceil($maxPrice ?? 100) }}">
                            <input type="hidden" id="range-to" name="max_price">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>
    <h3>Products</h3>
    <div class="row">
        @foreach($products as $product)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    @if($product->image)
                        <img src="{{ asset($product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top text-center bg-light" style="height: 200px; line-height: 200px;">No Image</div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">৳{{ $product->price }}</p>
                        @auth
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <div class="input-group mb-2">
                                    <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                                </div>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">Login to Add to Cart</a>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
        @if($products->isEmpty())
            <p>No products found.</p>
        @endif
    </div>

    <!-- Include noUiSlider CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.js"></script>

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
                        return Math.round(value); // Ensure integer output
                    },
                    from: function (value) {
                        return Number(value);
                    }
                }
            });

            // Update display and hidden inputs when slider changes
            slider.noUiSlider.on('update', function (values, handle) {
                if (handle === 0) {
                    minDisplay.value = '৳' + values[0]; // Display with currency
                    minInput.value = values[0]; // Hidden input with integer value
                } else {
                    maxDisplay.value = '৳' + values[1]; // Display with currency
                    maxInput.value = values[1]; // Hidden input with integer value
                }
            });

            // Update slider when input fields change
            minDisplay.addEventListener('change', function () {
                var value = parseInt(minDisplay.value.replace('৳', '')) || {{ $minPrice }};
                value = Math.max({{ $minPrice }}, Math.min(value, slider.noUiSlider.get()[1])); // Ensure within bounds and not above max
                slider.noUiSlider.set([value, null]);
            });

            maxDisplay.addEventListener('change', function () {
                var value = parseInt(maxDisplay.value.replace('৳', '')) || {{ $maxPrice }};
                value = Math.min({{ $maxPrice }}, Math.max(value, slider.noUiSlider.get()[0])); // Ensure within bounds and not below min
                slider.noUiSlider.set([null, value]);
            });
        });
    </script>

    <style>
        .price-filter.ws-box {
            padding: 10px;
        }
        .noUi-target {
            margin: 15px 0;
        }
        .noUi-connect {
            background: #007bff;
        }
        .noUi-handle {
            background: #007bff;
            border-radius: 50%;
            box-shadow: none;
            border: 2px solid #fff;
        }
        .range-label input {
            width: 100px;
            text-align: center;
        }
    </style>
@endsection