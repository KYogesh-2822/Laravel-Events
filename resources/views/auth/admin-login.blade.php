<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #111827; color: #f9fafb; }
        .card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,.2); }
        .error-text { color: #fca5a5; font-size: .875rem; margin-top: .25rem; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card bg-dark p-4">
                <h3 class="mb-3">Admin Login</h3>
                <p class="text-light-emphasis">Only admins can access this area.</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="adminLoginForm" action="{{ route('admin.login.submit') }}" method="POST" novalidate>
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
                    <button type="submit" class="btn btn-danger w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $('#adminLoginForm').on('submit', function (e) {
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
</body>
</html>
