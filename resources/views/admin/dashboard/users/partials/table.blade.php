<table class="table table-bordered">

    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>City</th>
            <th>Status</th>
        </tr>
    </thead>

    <p>Query Time: {{ $time }} seconds</p>
    <tbody>

    @forelse($users as $user)

        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->phone }}</td>
            <td>{{ $user->city }}</td>
            <td>{{ $user->status }}</td>
        </tr>

    @empty

        <tr>
            <td colspan="6">No users found</td>
        </tr>

    @endforelse

    </tbody>

</table>

<div class="mt-3">
    {{ $users->links() }}
</div>