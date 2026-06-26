<div class="card">

    <div class="card-header">

        <div class="row">

            <div class="col-md-8">
                <input type="text"
                       id="search"
                       class="form-control"
                       placeholder="Search name, email, phone, city">
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
    const dashboardUrl = "{{ route('dashboard') }}";

let timer = null;
let controller = null;

function fetchUsers(page = 1) {

    if (controller) {
        controller.abort();
    }

    controller = new AbortController();

    const search = document.getElementById('search').value.trim();
    const status = document.getElementById('status').value;

    const url =
        dashboardUrl +
        '?page=' + page +
        '&search=' + encodeURIComponent(search) +
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

// Search
document.getElementById('search').addEventListener('input', function () {

    clearTimeout(timer);

    timer = setTimeout(function () {
        fetchUsers();
    }, 400);

});

// Status
document.getElementById('status').addEventListener('change', function () {
    fetchUsers();
});

// AJAX Pagination
document.addEventListener('click', function (e) {

    const link = e.target.closest('.pagination a');

    if (!link) return;

    e.preventDefault();

    const url = new URL(link.href);
    const page = url.searchParams.get('page');

    fetchUsers(page);

});

    // const dashboardUrl = "{{ route('dashboard') }}";

    // let timer;

    // function fetchUsers() {
    //     let search = document.getElementById('search').value;
    //     let status = document.getElementById('status').value;

    //     fetch(dashboardUrl + "?search=" + search + "&status=" + status, {
    //         headers: { 'X-Requested-With': 'XMLHttpRequest' }
    //     })
    //     .then(res => res.text())
    //     .then(html => {
    //         document.getElementById('userTable').innerHTML = html;
    //     });
    // }

    // document.getElementById('search').addEventListener('keyup', function () {
    //     clearTimeout(timer);
    //     timer = setTimeout(() => fetchUsers(), 300);
    // });

    // document.getElementById('status').addEventListener('change', function () {
    //     fetchUsers();
    // });
</script>
@endpush