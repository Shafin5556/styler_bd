<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styler BD</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="/">Styler BD</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <a class="nav-link" href="/">Home</a>
                    @foreach($categories as $category)
                        <a class="nav-link" href="{{ route('products.category', $category->id) }}">{{ $category->name }}</a>
                    @endforeach
                    <a class="nav-link" href="/cart">Cart</a>
                </div>
                <div class="navbar-nav ms-auto">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                        @else
                            <a class="nav-link" href="{{ route('user.dashboard') }}">User Dashboard</a>
                        @endif
                        <form action="/logout" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn">Logout</button>
                        </form>
                    @else
                        <a class="nav-link" href="/login">Login</a>
                        <a class="nav-link" href="/register">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
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