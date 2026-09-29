@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h3 class="mb-1">Admin Dashboard</h3>
                    <p class="text-muted mb-0">Overview of users and revenue.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Total Users</p>
                        <h3 class="mb-0">{{ number_format($stats['total_users'] ?? 0) }}</h3>
                    </div>
                    <div class="text-primary fs-1"><i class="bi bi-people-fill"></i></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Active Users</p>
                        <h3 class="mb-0">{{ number_format($stats['active_users'] ?? 0) }}</h3>
                    </div>
                    <div class="text-success fs-1"><i class="bi bi-person-check-fill"></i></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Inactive Users</p>
                        <h3 class="mb-0">{{ number_format($stats['inactive_users'] ?? 0) }}</h3>
                    </div>
                    <div class="text-warning fs-1"><i class="bi bi-person-dash-fill"></i></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Revenue</p>
                        <h3 class="mb-0">INR {{ number_format($stats['revenue'] ?? 0, 2) }}</h3>
                    </div>
                    <div class="text-info fs-1"><i class="bi bi-currency-rupee"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="search_by" class="form-label">Search By</label>
                    <select id="search_by" class="form-select">
                        <option value="name">Name</option>
                        <option value="email">Email</option>
                        <option value="phone">Phone</option>
                        <option value="city">City</option>
                    </select>
                </div>

                <div class="col-12 col-md-5">
                    <label for="search" class="form-label">Search Users</label>
                    <input type="text" id="search" class="form-control" placeholder="Type at least 2 characters">
                </div>

                <div class="col-12 col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-12 col-md-1">
                    <button type="button" id="resetFilters" class="btn btn-outline-secondary w-100" title="Reset filters">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div id="userTable">
                @include('admin.dashboard.users.partials.table')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dashboardUrl = @json(route('admin.dashboard'));
    const userTable = document.getElementById('userTable');
    const searchInput = document.getElementById('search');
    const searchByInput = document.getElementById('search_by');
    const statusInput = document.getElementById('status');
    const resetButton = document.getElementById('resetFilters');
    let timer = null;
    let controller = null;


    function getPerPage() {
        const perPageInput = document.getElementById('per_page');
        return perPageInput ? perPageInput.value : '50';
    }
    function fetchUsers(page = 1) {
        const searchTerm = searchInput.value.trim();

        if (searchTerm.length === 1) {
            return;
        }

        if (controller) {
            controller.abort();
        }

        controller = new AbortController();
        userTable.classList.add('opacity-50');

        const params = new URLSearchParams({
            page: page,
            search: searchTerm,
            search_by: searchByInput.value,
            status: statusInput.value,
            per_page: getPerPage()
        });

        fetch(dashboardUrl + '?' + params.toString(), {
            signal: controller.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                return response.text();
            })
            .then(function (html) {
                userTable.innerHTML = html;
            })
            .catch(function (error) {
                if (error.name !== 'AbortError') {
                    console.error(error);
                }
            })
            .finally(function () {
                userTable.classList.remove('opacity-50');
            });
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            fetchUsers();
        }, 500);
    });

    searchByInput.addEventListener('change', function () {
        fetchUsers();
    });

    statusInput.addEventListener('change', function () {
        fetchUsers();
    });

    resetButton.addEventListener('click', function () {
        searchInput.value = '';
        searchByInput.value = 'name';
        statusInput.value = '';
        fetchUsers();
    });


    userTable.addEventListener('change', function (event) {
        if (event.target && event.target.id === 'per_page') {
            fetchUsers(1);
        }
    });
    userTable.addEventListener('click', function (event) {
        const link = event.target.closest('.js-user-pagination a[href], .pagination a[href], nav[role="navigation"] a[href]');

        if (!link) {
            return;
        }

        event.preventDefault();
        const url = new URL(link.href);
        fetchUsers(url.searchParams.get('page') || 1);
    });
});
</script>
@endpush






