@extends('layouts.master')

@section('title', 'Login')

@section('content')
<div class="form-container">
    <h2 class="text-center mb-4">Login</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>

        <div class="text-center mt-3">
            <p>Don't have an account?</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('register.freelancer') }}" class="btn btn-outline-primary">Register as Freelancer</a>
                <a href="{{ route('register.client') }}" class="btn btn-outline-primary">Register as Client</a>
            </div>
        </div>
    </form>
</div>
@endsection 