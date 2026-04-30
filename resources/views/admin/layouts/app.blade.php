<!doctype html>
<html lang="en">

@include('admin.layouts.header')

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
      @include('admin.layouts.navbar')
      @include('admin.layouts.sidebar')
      <main class="app-main">
        @yield('content')
      </main>
      @include('admin.layouts.footer')
    </div>
</body>
@include('admin.layouts.scripts')
@yield('scripts')
</html>
