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
        /* Navbar Styling */
        .navbar {
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
        .navbar-nav .nav-link {
            color: #333;
            font-weight: 500;
            margin: 0 15px;
            transition: color 0.3s ease;
        }
        .navbar-nav .nav-link:hover {
            color: #007bff;
        }
        /* Cart Button Styling */
        .cart-btn {
            font-size: 1.2rem;
            padding: 0.5rem 1rem;
            color: #333;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .cart-btn:hover {
            color: #007bff;
            transform: scale(1.1);
        }
        /* Category Dropdown Styling */
        .nav-item.dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0;
        }
        .dropdown-menu {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
        /* Alert Styling */
        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/"><img src="{{ asset('asset/logo/logo.png') }}" alt="Styler BD"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <a class="nav-link" href="/">Home</a>
                    @foreach($categories as $category)
                        <div class="nav-item dropdown">
                            <a class="nav-link" href="{{ route('products.category', $category->id) }}">{{ $category->name }}</a>
                            @if($category->subcategories->isNotEmpty())
                                <ul class="dropdown-menu">
                                    @foreach($category->subcategories as $subcategory)
                                        <li><a class="dropdown-item" href="{{ route('products.category', $subcategory->id) }}">{{ $subcategory->name }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="navbar-nav ms-auto">
                    <a class="cart-btn" href="{{ route('cart.index') }}" title="Cart"><i class="bi bi-cart"></i></a>
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                        @else
                            <a class="nav-link" href="{{ route('user.dashboard') }}">User Dashboard</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn">Logout</button>
                        </form>
                    @else
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    @endauth
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>