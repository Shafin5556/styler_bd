@extends('layouts.app')

@section('content')
    <div class="auth-section">
        <div class="container d-flex justify-content-center">
            <div class="card auth-card">
                <div class="card-body">
                    <h1 class="auth-title">Verify OTP</h1>

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

                    <form action="{{ route('password.verify.otp') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ $email }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="otp" class="form-label">OTP</label>
                            <input type="text" name="otp" id="otp" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Verify OTP
                        </button>
                    </form>
                    <p class="mt-3 text-center text-muted">
                        Back to <a href="{{ route('password.request') }}">Forgot Password</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection