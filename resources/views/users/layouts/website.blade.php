<!DOCTYPE html>
<html lang="en">
    @include('users.layouts.header')
<body>
    @include('users.layouts.navbar')
    <main>
       @yield('content')
    </main>

    <!-- Footer Section -->
    @include('users.layouts.footer')
    @include('users.layouts.scripts')
    @stack('scripts')
</body>
</html>