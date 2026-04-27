@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Site Users</h4>
        <small class="text-muted">Manage users from the public users table</small>
    </div>
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search name or email">
        <select name="status" class="form-select form-select-sm">
            <option value="">All</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
        </select>
        <button class="btn btn-sm btn-primary" type="submit">Filter</button>
    </form>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->is_banned)
                                    <span class="badge bg-secondary">Banned</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.site-users.ban', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="is_banned" value="{{ $user->is_banned ? 0 : 1 }}">
                                    <button type="submit" class="btn btn-sm {{ $user->is_banned ? 'btn-success' : 'btn-warning' }}">
                                        {{ $user->is_banned ? 'Unban' : 'Ban' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.site-users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
@endsection

