@extends('users.layouts.website')
@section('content')
<style>
        body { background: #f5f7fb; }
        .card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,.08); }
        .error-text { color: #dc3545; font-size: .875rem; margin-top: .25rem; }
    </style>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4">
                <h3 class="mb-3">User Login</h3>
                <p class="text-muted">Sign in to your account.</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="loginForm" action="{{ route('user.login.submit') }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        <div class="error-text" data-error-for="email"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="error-text" data-error-for="password"></div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <div class="mt-3 text-center">
                    <a href="{{ route('user.register') }}">Create an account</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
$(function () {
    $('#loginForm').on('submit', function (e) {
        let valid = true;
        $('.error-text').text('');

        const email = $('#email').val().trim();
        const password = $('#password').val();

        if (!email) {
            $('[data-error-for="email"]').text('Please enter your email.');
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('[data-error-for="email"]').text('Please enter a valid email.');
            valid = false;
        }

        if (!password) {
            $('[data-error-for="password"]').text('Please enter your password.');
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
});
</script>
@endpush