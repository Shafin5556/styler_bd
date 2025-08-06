@extends('layouts.app')

@section('content')
    <div class="dashboard-section">
        <div class="container d-flex justify-content-center">
            <div class="dashboard-content">
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

                <div class="card">
                    <div class="card-body">
                        <h2 class="section-title">Edit Profile</h2>
                        <form action="{{ route('user.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture (PNG only)</label>
                                <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/png">
                                @if($user->profile_picture)
                                    <div class="mt-3 text-center">
                                        <img src="{{ asset($user->profile_picture) }}" alt="Current Profile Picture" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                        <p class="text-muted mt-2">Current Profile Picture</p>
                                    </div>
                                @else
                                    <div class="mt-3 text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; font-size: 0.9rem; color: #666;">
                                            No Image
                                        </div>
                                        <p class="text-muted mt-2">No Profile Picture</p>
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-save"></i> Update Profile
                                </button>
                                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary btn-sm">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .dashboard-section {
          
            background-color: #f8f9fa;
   
            display: flex;
            align-items: center;
        }
        .dashboard-content {
            max-width: 800px;
            width: 100%;
        }
        .section-title {
            font-size: 1.6rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 1.5rem;
            font-family: 'Poppins', sans-serif;
        }
        .card {
            border: none;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
        }
        .alert {
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            padding: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .alert ul {
            padding-left: 1.25rem;
        }
        .form-label {
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            color: #222;
        }
        .form-control {
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            padding: 0.75rem;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .text-muted {
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
        }
        .btn {
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn i {
            margin-right: 6px;
        }
        @media (max-width: 576px) {
            .section-title {
                font-size: 1.4rem;
            }
            .dashboard-content {
                margin: 0 1rem;
            }
            .form-label {
                font-size: 0.9rem;
            }
            .form-control {
                font-size: 0.85rem;
                padding: 0.5rem;
            }
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            .d-flex.gap-2 {
                flex-direction: column;
            }
        }
    </style>
@endsection