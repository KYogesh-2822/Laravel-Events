<div class="card">

    <div class="card-header">

        <div class="row">

    <div class="col-md-3">
        <select id="search_by" class="form-control">
            <option value="name">Name</option>
            <option value="email">Email</option>
            <option value="phone">Phone</option>
            <option value="city">City</option>
        </select>
    </div>

    <div class="col-md-5">
        <input type="text"
               id="search"
               class="form-control"
               placeholder="Search selected field">
    </div>

    <div class="col-md-4">
        <select id="status" class="form-control">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

</div>

    </div>

    <div class="card-body">

        <div id="userTable">
            @include('admin.dashboard.users.partials.table')
        </div>

    </div>

</div>
@push('scripts')
<script>
    let timer = null;
    let controller = null;
 function fetchUsers(page = 1) {

    // if (controller) {
    //     controller.abort();
    // }
const dashboardUrl = "{{ route('dashboard') }}";
    controller = new AbortController();

    const search = document.getElementById('search').value.trim();
    const searchBy = document.getElementById('search_by').value;
    const status = document.getElementById('status').value;

    const url =
        dashboardUrl +
        '?page=' + page +
        '&search=' + encodeURIComponent(search) +
        '&search_by=' + encodeURIComponent(searchBy) +
        '&status=' + encodeURIComponent(status);

    fetch(url, {
        signal: controller.signal,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('userTable').innerHTML = html;
    })
    .catch(error => {
        if (error.name !== 'AbortError') {
            console.error(error);
        }
    });
}

document.getElementById('search').addEventListener('input', function () {
    clearTimeout(timer);

    timer = setTimeout(function () {
        fetchUsers();
    }, 500);
});

document.getElementById('search_by').addEventListener('change', function () {
    fetchUsers();
});

document.getElementById('status').addEventListener('change', function () {
    fetchUsers();
});
    
</script>
@endpush