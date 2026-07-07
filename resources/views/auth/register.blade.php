@extends('users.layouts.website')
@section('content')
    <style>
        body { background: #f5f7fb; }
        .card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,.08); }
        .error-text { color: #dc3545; font-size: .875rem; margin-top: .25rem; }
    </style>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h3 class="mb-3">Create an Account</h3>
                <p class="text-muted">Register to continue.</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="registerForm" action="{{ route('user.register') }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        <div class="error-text" data-error-for="name"></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        <div class="error-text" data-error-for="email"></div>
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" required>
                        <div class="error-text" data-error-for="city"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="error-text" data-error-for="password"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        <div class="error-text" data-error-for="password_confirmation"></div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Register</button>
                </form>

                <div class="mt-3 text-center">
                    <a href="{{ route('user.login') }}">Already have an account?</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
$(function () {
    $('#registerForm').on('submit', function (e) {
        let valid = true;
        $('.error-text').text('');

        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const city = $('#city').val().trim();
        const password = $('#password').val();
        const confirmPassword = $('#password_confirmation').val();

        if (!name) {
            $('[data-error-for="name"]').text('Please enter your name.');
            valid = false;
        }
        if (!email) {
            $('[data-error-for="email"]').text('Please enter your email.');
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('[data-error-for="email"]').text('Please enter a valid email.');
            valid = false;
        }
        if (!city) {
            $('[data-error-for="city"]').text('Please enter your city.');
            valid = false;
        }
        if (!password) {
            $('[data-error-for="password"]').text('Please enter a password.');
            valid = false;
        } else if (password.length < 8) {
            $('[data-error-for="password"]').text('Password must be at least 8 characters.');
            valid = false;
        }
        if (!confirmPassword) {
            $('[data-error-for="password_confirmation"]').text('Please confirm your password.');
            valid = false;
        } else if (confirmPassword !== password) {
            $('[data-error-for="password_confirmation"]').text('Passwords do not match.');
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
});
</script>
@endpush

