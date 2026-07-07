@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="mb-1">Admin Dashboard</h3>
                    <p class="text-muted mb-0">You are signed in as an admin.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Users</h5>
                    <p class="text-muted">The user list is available here.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
