@extends('layouts.app')

@section('content')
    <div class="shop-section">
        <div class="container">
            @auth
                <div class="row">
                    <!-- Outfit Builder -->
                    <div class="col-lg-8 col-md-7 mb-4">
                        <h3 class="filter-title">Build Your Outfit</h3>
                        <div class="filter-sidebar">
                            <div class="mb-4">
                                <label for="category_id" class="form-label">Select Category</label>
                                <select id="category_id" class="form-control" onchange="loadSubcategories()">
                                    <option value="">Select a Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="subcategories" class="mb-4"></div>
                            <div id="products" class="row"></div>
                        </div>
                    </div>
                    <!-- Selected Outfit -->
                    <div class="col-lg-4 col-md-5">
                        <h3 class="filter-title">Selected Outfit</h3>
                        <div class="filter-sidebar">
                            <div id="selected-products" class="mb-4">
                                <p class="text-muted">No products selected yet.</p>
                            </div>
                            <button id="finish-btn" class="btn btn-primary w-100" disabled>
                                <i class="bi bi-cart-plus"></i> Finish and Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Include Bootstrap Icons -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

                <script>
                    let selectedProducts = [];

                    // Helper function to escape strings for JavaScript
                    function escapeForJS(str) {
                        if (!str) return '';
                        return str.replace(/'/g, "\\'").replace(/\n/g, '\\n').replace(/\r/g, '\\r').replace(/"/g, '\\"');
                    }

                    function loadSubcategories() {
                        const categoryId = document.getElementById('category_id').value;
                        const subcategoriesDiv = document.getElementById('subcategories');
                        const productsDiv = document.getElementById('products');
                        subcategoriesDiv.innerHTML = '';
                        productsDiv.innerHTML = '';

                        if (!categoryId) {
                            subcategoriesDiv.innerHTML = '<p class="text-muted">Please select a category.</p>';
                            return;
                        }

                        subcategoriesDiv.innerHTML = '<p>Loading subcategories...</p>';
                        fetch(`/categories/${categoryId}/subcategories`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => {
                                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                                return response.json();
                            })
                            .then(data => {
                                if (data.error) {
                                    subcategoriesDiv.innerHTML = `<p class="text-danger">${data.error}</p>`;
                                    return;
                                }
                                if (data.length === 0) {
                                    subcategoriesDiv.innerHTML = '<p class="text-muted">No subcategories available.</p>';
                                    return;
                                }

                                subcategoriesDiv.innerHTML = '<h4 class="filter-title">Subcategories</h4>';
                                const select = document.createElement('select');
                                select.className = 'form-control mb-3';
                                select.onchange = loadProducts;
                                select.innerHTML = '<option value="">Select a Subcategory</option>' + 
                                    data.map(sub => `<option value="${sub.id}">${sub.name}</option>`).join('');
                                subcategoriesDiv.appendChild(select);
                            })
                            .catch(error => {
                                console.error('Error loading subcategories:', error);
                                subcategoriesDiv.innerHTML = '<p class="text-danger">Error loading subcategories. Please try again.</p>';
                            });
                    }

                    function loadProducts() {
                        const subcategoryId = this.value;
                        const productsDiv = document.getElementById('products');
                        productsDiv.innerHTML = '';

                        if (!subcategoryId) {
                            productsDiv.innerHTML = '<p class="text-muted">Please select a subcategory.</p>';
                            return;
                        }

                        productsDiv.innerHTML = '<p>Loading products...</p>';
                        fetch(`/subcategories/${subcategoryId}/products`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => {
                                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                                return response.json();
                            })
                            .then(data => {
                                if (data.length === 0) {
                                    productsDiv.innerHTML = '<p class="text-muted">No products available.</p>';
                                    return;
                                }

                                data.forEach(product => {
                                    const card = document.createElement('div');
                                    card.className = 'col-lg-4 col-md-6 col-sm-6 mb-4';
                                    card.innerHTML = `
                                        <div class="card product-card h-100">
                                            ${product.images.length > 0 ? `
                                                <div id="productCarousel${product.id}" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                                                    <div class="carousel-inner">
                                                        ${product.images.map((img, index) => `
                                                            <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                                                <img src="${img}" class="d-block w-100 card-img-top" alt="${product.name} Image ${index + 1}" data-bs-toggle="modal" data-bs-target="#productModal${product.id}">
                                                            </div>
                                                        `).join('')}
                                                    </div>
                                                </div>
                                            ` : `
                                                <div class="card-img-top text-center bg-light d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#productModal${product.id}">No Image</div>
                                            `}
                                            <div class="card-body">
                                                <h5 class="card-title" data-bs-toggle="modal" data-bs-target="#productModal${product.id}">${product.name}</h5>
                                                <p class="card-price">৳${Number(product.price).toFixed(2)}</p>
                                                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal${product.id}">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Product Details Modal -->
                                        <div class="modal fade" id="productModal${product.id}" tabindex="-1" aria-labelledby="productModalLabel${product.id}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="productModalLabel${product.id}">${product.name}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ${product.images.length > 0 ? `
                                                            <div id="productModalCarousel${product.id}" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-interval="3000">
                                                                <div class="carousel-inner">
                                                                    ${product.images.map((img, index) => `
                                                                        <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                                                            <img src="${img}" class="d-block w-100 modal-carousel-img" alt="${product.name} Image ${index + 1}">
                                                                        </div>
                                                                    `).join('')}
                                                                </div>
                                                                <button class="carousel-control-prev" type="button" data-bs-target="#productModalCarousel${product.id}" data-bs-slide="prev">
                                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                    <span class="visually-hidden">Previous</span>
                                                                </button>
                                                                <button class="carousel-control-next" type="button" data-bs-target="#productModalCarousel${product.id}" data-bs-slide="next">
                                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                    <span class="visually-hidden">Next</span>
                                                                </button>
                                                            </div>
                                                        ` : `
                                                            <p class="text-center">No Images Available</p>
                                                        `}
                                                        <p><strong>Description:</strong> ${product.description || 'No description available.'}</p>
                                                        <p><strong>Price:</strong> ৳${Number(product.price).toFixed(2)}</p>
                                                        <p><strong>Category:</strong> ${product.category}</p>
                                                        <p><strong>Created At:</strong> ${product.created_at}</p>
                                                        <p><strong>Updated At:</strong> ${product.updated_at}</p>
                                                        <div class="input-group mt-3">
                                                            <input type="number" class="form-control quantity-input" value="1" min="1" data-product-id="${product.id}">
                                                            <button class="btn btn-primary add-to-cart" data-product-id="${product.id}" onclick="addToCart(${product.id}, '${escapeForJS(product.name)}', ${product.price}, '${product.images[0] || ''}', '${escapeForJS(product.description || '')}', '${escapeForJS(product.category)}', '${escapeForJS(product.created_at)}', '${escapeForJS(product.updated_at)}')">
                                                                <i class="bi bi-cart-plus"></i> Add to Cart
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    productsDiv.appendChild(card);
                                });
                            })
                            .catch(error => {
                                console.error('Error loading products:', error);
                                productsDiv.innerHTML = '<p class="text-danger">Error loading products. Please try again.</p>';
                            });
                    }

                    function addToCart(id, name, price, image, description, category, created_at, updated_at) {
                        const quantityInput = document.querySelector(`input[data-product-id="${id}"]`);
                        const quantity = parseInt(quantityInput.value) || 1;

                        const existingProduct = selectedProducts.find(p => p.id === id);
                        if (existingProduct) {
                            existingProduct.quantity = quantity;
                        } else {
                            selectedProducts.push({ id, name, price, image, quantity, description, category, created_at, updated_at });
                        }

                        updateSelectedProducts();
                        bootstrap.Modal.getInstance(document.getElementById(`productModal${id}`)).hide();
                    }

                    function removeProduct(id) {
                        selectedProducts = selectedProducts.filter(p => p.id !== id);
                        updateSelectedProducts();
                    }

                    function updateSelectedProducts() {
                        const selectedDiv = document.getElementById('selected-products');
                        const finishBtn = document.getElementById('finish-btn');
                        selectedDiv.innerHTML = '';

                        if (selectedProducts.length === 0) {
                            selectedDiv.innerHTML = '<p class="text-muted">No products selected yet.</p>';
                            finishBtn.disabled = true;
                            return;
                        }

                        finishBtn.disabled = false;
                        selectedProducts.forEach(product => {
                            const item = document.createElement('div');
                            item.className = 'card product-card mb-2';
                            item.innerHTML = `
                                <div class="card-body d-flex align-items-center">
                                    ${product.image ? `<img src="${product.image}" alt="${product.name}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">` : 
                                    '<div class="me-3 bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 8px;">No Image</div>'}
                                    <div>
                                        <h6 class="card-title mb-0">${product.name}</h6>
                                        <p class="card-price mb-0">৳${Number(product.price).toFixed(2)} x ${product.quantity}</p>
                                    </div>
                                    <button class="btn btn-sm btn-danger ms-auto" onclick="removeProduct(${product.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `;
                            selectedDiv.appendChild(item);
                        });
                    }

                    document.getElementById('finish-btn').addEventListener('click', () => {
                        if (selectedProducts.length === 0) return;

                        fetch('{{ route('cart.bulk-add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ products: selectedProducts })
                        })
                        .then(response => {
                            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            window.location.href = '{{ route('cart.index') }}';
                        })
                        .catch(error => {
                            console.error('Error adding to cart:', error);
                            alert('Failed to add outfit to cart. Please try again.');
                        });
                    });
                </script>

                <style>
                    /* Inherit Shop Section Styling */
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
                    .btn-danger {
                        background-color: #dc3545;
                        border: none;
                        font-weight: 500;
                    }
                    .btn-danger:hover {
                        background-color: #b02a37;
                    }
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
                        cursor: pointer;
                    }
                    .card-img-top.bg-light {
                        display: flex;
                        align-items: center;
                        justify-content-center;
                        color: #666;
                        font-size: 1rem;
                        height: 220px;
                        background-color: #f8f9fa;
                        cursor: pointer;
                    }
                    .card-body {
                        padding: 1.5rem;
                    }
                    .card-title {
                        font-size: 1.3rem;
                        font-weight: 500;
                        color: #222;
                        margin-bottom: 0.5rem;
                        cursor: pointer;
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
                    /* Carousel Styling */
                    .carousel-inner img {
                        height: 220px;
                        object-fit: cover;
                    }
                    /* Modal Styling */
                    .modal-content {
                        border-radius: 12px;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    }
                    .modal-header {
                        border-bottom: none;
                        background-color: #f8f9fa;
                        border-radius: 12px 12px 0 0;
                    }
                    .modal-body {
                        padding: 1.5rem;
                    }
                    .modal-body strong {
                        font-weight: 600;
                        color: #222;
                    }
                    .modal-body p {
                        margin-bottom: 1rem;
                        color: #555;
                    }
                    .modal-carousel-img {
                        height: 300px;
                        object-fit: contain;
                    }
                    .carousel-control-prev, .carousel-control-next {
                        filter: invert(1);
                    }
                    @media (max-width: 991px) {
                        .filter-sidebar {
                            padding: 1.5rem;
                        }
                        .shop-title {
                            font-size: 2.2rem;
                        }
                        .card-img-top, .carousel-inner img {
                            height: 180px;
                        }
                        .modal-carousel-img {
                            height: 200px;
                        }
                    }
                    @media (max-width: 767px) {
                        .filter-sidebar {
                            margin-bottom: 2rem;
                        }
                    }
                </style>
            @else
                <p class="text-center text-muted">Please <a href="{{ route('login') }}" class="text-primary">login</a> to use the Dress Up feature.</p>
            @endauth
        </div>
    </div>
@endsection