@extends('layouts.app')

@section('content')
    <div class="auth-section">
        <div class="container d-flex justify-content-center">
            <div class="card auth-card">
                <div class="card-body">
                    <h1 class="auth-title">Register</h1>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-person-plus"></i> Register
                        </button>
                    </form>
                    <p class="mt-3 text-center text-muted">Already have an account? <a href="{{ route('login') }}">Login</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .auth-section {
      
            background-color: #f8f9fa;
      
            display: flex;
            align-items: center;
        }
        .auth-card {
            max-width: 400px;
            width: 100%;
            border: none;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .auth-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
        }
        .auth-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #222;
            text-align: center;
            margin-bottom: 1.5rem;
            font-family: 'Poppins', sans-serif;
        }
        .alert {
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            padding: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            color: #222;
            font-size: 0.95rem;
        }
        .form-control {
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            padding: 0.75rem;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 6px rgba(0, 123, 255, 0.2);
        }
        .btn-primary {
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            background-color: #007bff;
            border: none;
            padding: 0.75rem;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn i {
            margin-right: 6px;
        }
        .text-muted {
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
        }
        @media (max-width: 576px) {
            .auth-title {
                font-size: 1.5rem;
            }
            .auth-card {
                margin: 0 1rem;
            }
        }
    </style>
@endsection