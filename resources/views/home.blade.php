@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Discover Your Style</h1>
                <p>Explore the latest trends and create your perfect outfit with Styler BD.</p>
                <a href="{{ route('shop') }}" class="hero-btn">Shop Now</a>
            </div>
            <img src="{{ asset('asset/logo/shop_now.gif') }}" alt="Shop Now" class="hero-gif">
        </div>
    </div>
@endsection

<style>
    /* Hero Section Styling */
    .hero-section {
        position: relative;
        background: linear-gradient(135deg, #007bff 0%, #00ddeb 100%);
        color: white;
        padding: 60px 0;
        border-radius: 12px;
        margin-bottom: 2rem;
        overflow: hidden;
    }
    .hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .hero-text h1 {
        font-size: 2.5rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .hero-text p {
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
    }
    .hero-btn {
        background-color: #ffffff;
        color: #007bff;
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 25px;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .hero-btn:hover {
        background-color: #007bff;
        color: #ffffff;
    }
    .hero-gif {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 150px;
        height: auto;
        opacity: 0.9;
    }
    @media (max-width: 768px) {
        .hero-content {
            flex-direction: column;
            text-align: center;
        }
        .hero-gif {
            position: static;
            margin-top: 1rem;
            width: 100px;
        }
    }
</style>