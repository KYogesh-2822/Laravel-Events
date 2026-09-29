<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h5 class="mb-0">Users List</h5>
    <span class="text-muted small">Query Time: {{ $time }} ms</span>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>{{ $user->city ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $user->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ ucfirst($user->status ?? 'inactive') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No users found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-2">
        <label for="per_page" class="form-label mb-0 text-muted small">Show</label>
        <select id="per_page" class="form-select form-select-sm" style="width: auto;">
            @foreach([10, 50, 100, 250] as $size)
                <option value="{{ $size }}" @selected(($perPage ?? 10) === $size)>{{ $size }}</option>
            @endforeach
        </select>
        <span class="text-muted small">users</span>
    </div>

    <div class="js-user-pagination">
        {{ $users->links() }}
    </div>
</div>


