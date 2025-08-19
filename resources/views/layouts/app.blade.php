<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styler BD</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts for modern typography -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        /* Primary Navbar Styling */
        .navbar-primary {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }
        .navbar-brand img {
            height: 50px;
            transition: transform 0.3s ease;
        }
        .navbar-brand img:hover {
            transform: scale(1.05);
        }
        .search-form {
            flex-grow: 1;
            margin: 0 20px;
        }
        .search-form .input-group {
            width: 100%;
        }
        .search-form input.form-control {
            border-radius: 8px 0 0 8px;
            border: 1px solid #ced4da;
            font-size: 0.9rem;
        }
        .search-form button {
            border-radius: 0 8px 8px 0;
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease;
        }
        .search-form button:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        /* Cart Button Styling */
        .cart-btn {
            font-size: 1.2rem;
            padding: 0.5rem 1rem;
            color: #333;
            transition: color 0.3s ease, transform 0.3s ease;
            text-decoration: none;
        }
        .navbar-nav {
            flex-direction: row;
        }
        .cart-btn:hover {
            color: #007bff;
            transform: scale(1.1);
        }
        .cart-btn.active {
            color: #007bff;
            font-weight: 700;
        }
        /* User Button Styling */
        .user-btn {
            font-size: 1.2rem;
            padding: 0.5rem 1rem;
            color: #333;
            transition: color 0.3s ease, transform 0.3s ease;
            text-decoration: none;
        }
        .user-btn:hover {
            color: #007bff;
            transform: scale(1.1);
        }
        .user-btn.active {
            color: #007bff;
            font-weight: 700;
        }
        /* Secondary Navbar Styling */
        .navbar-secondary {
            background-color: #f1f3f5;
            padding: 0.5rem 0;
        }
        .navbar-secondary .navbar-nav {
            gap: 15px;
        }
        .navbar-nav .nav-link {
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .navbar-nav .nav-link:hover {
            color: #007bff;
        }
        .navbar-nav .nav-link.active {
            color: #007bff;
            font-weight: 700;
        }
        /* Social Icons Styling */
        .social-icons {
            gap: 10px;
            margin-left: 15px;
        }
        .social-btn {
            font-size: 1.1rem;
            padding: 0.5rem;
            color: #333;
            transition: color 0.3s ease, transform 0.3s ease;
            text-decoration: none;
        }
        .social-btn:hover {
            color: #007bff;
            
        }
        /* Dropup Styling */
        .nav-item.dropup:hover .dropdown-menu {
            display: block;
            margin-bottom: 0;
            bottom: 100%;
            top: auto;
        }
        .dropdown-menu {
            border: none;
            border-radius: 8px;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.15);
            background-color: #ffffff;
            min-width: 200px;
        }
        .dropdown-item {
            padding: 10px 20px;
            color: #333;
            font-weight: 400;
            transition: background-color 0.3s ease;
        }
        .dropdown-item:hover {
            background-color: #f1f3f5;
            color: #007bff;
        }
        .dropdown-item.active {
            color: #007bff;
            font-weight: 700;
            background-color: #e9ecef;
        }
        .dropdown-submenu {
            position: relative;
        }
        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-left: 0.1rem;
            display: none;
            border-radius: 8px;
        }
        .dropdown-submenu:hover > .dropdown-menu {
            display: block;
        }
        .dropdown-submenu > a:after {
            content: "\203A";
            float: right;
            margin-top: 2px;
            font-weight: bold;
            color: #007bff;
        }
        /* Dress Button Styling */
        .dress-btn {
            font-size: 1rem;
            padding: 0.5rem 1rem;
            color: #fff;
            font-weight: 600;
            background: linear-gradient(45deg, #007bff, #00d4ff);
            border-radius: 8px;
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .dress-btn:hover {
            
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
            color: #fff;
        }
        .dress-btn.active {
            background: linear-gradient(45deg, #0056b3, #00b7eb);
            font-weight: 700;
        }
        /* Alert Styling */
        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        /* Footer Styling */
        .footer {
         background: linear-gradient(to bottom, #000000 0%, #1a1a1a 20%);
            color: #ffffff;
            padding: 3rem 0 1rem;
            font-family: 'Poppins', sans-serif;
            margin-top: 2rem;
        }
        .footer a {
            color: #d1d5db;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .footer a:hover {
            color: #007bff;
        }
        .footer h5 {
            font-weight: 600;
            font-size: 1.1rem;
            color: #ffffff;
            margin-bottom: 1.5rem;
        }
        .footer ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer ul li {
            margin-bottom: 0.5rem;
        }
        .footer .social-icons {
            gap: 15px;
        }
        .footer .social-btn {
            font-size: 1.5rem;
            color: #d1d5db;
        }
        .footer .social-btn:hover {
            color: #007bff;
            
        }
        .footer .newsletter-form .form-control {
            border-radius: 8px 0 0 8px;
            border: 1px solid #4b5563;
            background-color: #2d3748;
            color: #ffffff;
            font-size: 0.9rem;
        }
        .footer .newsletter-form .form-control::placeholder {
            color: #9ca3af;
        }
        .footer .newsletter-form .btn-primary {
            border-radius: 0 8px 8px 0;
            background-color: #007bff;
            border-color: #007bff;
            font-weight: 500;
        }
        .footer .newsletter-form .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .footer .divider {
            border-top: 1px solid #4b5563;
            margin: 2rem 0;
        }
        .footer .copyright {
            font-size: 0.85rem;
            color: #9ca3af;
        }
        /* Responsive Footer */
        @media (max-width: 767px) {
            .footer .col-md-3, .footer .col-md-6 {
                margin-bottom: 2rem;
            }
            .footer .social-icons {
                justify-content: center;
            }
            .footer .newsletter-form .input-group {
                flex-direction: column;
            }
            .footer .newsletter-form .form-control {
                border-radius: 8px;
                margin-bottom: 0.5rem;
            }
            .footer .newsletter-form .btn-primary {
                border-radius: 8px;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Primary Navbar -->
    <nav class="navbar navbar-primary">
        <div class="container d-flex align-items-center">
            <a class="navbar-brand" href="/"><img src="{{ asset('asset/logo/logo.png') }}" alt="Styler BD"></a>
            <form class="search-form" action="{{ route('shop') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter product name" value="{{ request('name') }}">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <div class="navbar-nav">
                @auth
                    <a class="cart-btn {{ request()->routeIs('cart.index') ? 'active' : '' }}" href="{{ route('cart.index') }}" title="Cart"><i class="bi bi-cart"></i></a>
                @endauth
                <a class="user-btn {{ request()->routeIs('login') || request()->routeIs('admin.dashboard') || request()->routeIs('user.dashboard') ? 'active' : '' }}" 
                   href="{{ auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard')) : route('login') }}" 
                   title="{{ auth()->check() ? (auth()->user()->role === 'admin' ? 'Admin Dashboard' : 'User Dashboard') : 'Login' }}">
                    <i class="bi bi-person-fill"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Secondary Navbar -->
    <nav class="navbar navbar-secondary">
        <div class="container">
            <div class="navbar-nav d-flex flex-row align-items-center w-100">
                <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Home</a>
                @php
                    $currentCategoryId = request()->segment(2) ? (int) request()->segment(2) : null;
                    $currentCategory = $currentCategoryId ? \App\Models\Category::find($currentCategoryId) : null;
                    $parentCategoryId = $currentCategory && $currentCategory->parent_id ? $currentCategory->parent_id : null;
                @endphp
                @foreach($categories as $category)
                    <div class="nav-item dropdown dropup">
                        <a class="nav-link {{ request()->is('products/' . $category->id) || $parentCategoryId == $category->id ? 'active' : '' }}" href="{{ route('products.category', $category->id) }}">{{ $category->name }}</a>
                        @if($category->subcategories->isNotEmpty())
                            <ul class="dropdown-menu">
                                @foreach($category->subcategories as $subcategory)
                                    <li><a class="dropdown-item {{ request()->is('products/' . $subcategory->id) ? 'active' : '' }}" href="{{ route('products.category', $subcategory->id) }}">{{ $subcategory->name }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
                <a class="dress-btn ms-auto {{ request()->routeIs('dressup') ? 'active' : '' }}" href="{{ route('dressup') }}">Dress Up</a>
                <div class="social-icons d-flex flex-row align-items-center">
                    <a class="social-btn" href="https://wa.me/1234567890" target="_blank" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <a class="social-btn" href="https://facebook.com" target="_blank" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a class="social-btn" href="https://instagram.com" target="_blank" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a class="social-btn" href="https://twitter.com" target="_blank" title="Twitter"><i class="bi bi-twitter"></i></a>
                    <a class="social-btn" href="https://linkedin.com" target="_blank" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <!-- Navigation Links -->
                <div class="col-md-3">
                    <h5>Explore</h5>
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        @foreach($categories as $category)
                            <li><a href="{{ route('products.category', $category->id) }}">{{ $category->name }}</a></li>
                        @endforeach
                        <li><a href="{{ route('dressup') }}">Dress Up</a></li>
                    </ul>
                </div>
                <!-- Social Media -->
                <div class="col-md-3">
                    <h5>Follow Us</h5>
                    <div class="social-icons d-flex">
                        <a class="social-btn" href="https://wa.me/1234567890" target="_blank" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <a class="social-btn" href="https://facebook.com" target="_blank" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a class="social-btn" href="https://instagram.com" target="_blank" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a class="social-btn" href="https://twitter.com" target="_blank" title="Twitter"><i class="bi bi-twitter"></i></a>
                        <a class="social-btn" href="https://linkedin.com" target="_blank" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="divider"></div>
            <div class="row">
                <div class="col-12 text-center">
                    <p class="copyright">
                        &copy; {{ date('Y') }} Styler BD. All rights reserved. 
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>