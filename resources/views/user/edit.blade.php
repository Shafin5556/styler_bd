@extends('layouts.app')

@section('content')
    <h1>Edit Profile</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
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
            <input type="file" name="profile_picture" id="profile_picture" class="form-control-file" accept="image/png">
            @if($user->profile_picture)
                <div class="mt-2">
                    <img src="{{ asset($user->profile_picture) }}" alt="Profile Picture" style="width: 100px; height: auto;">
                    <p>Current Profile Picture</p>
                </div>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
        <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection