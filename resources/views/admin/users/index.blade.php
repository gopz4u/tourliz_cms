@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-0"><i class="bi bi-people"></i> Users</h1>
        <p class="text-muted mb-0">Manage system users</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New User
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        loadUsers();
    });
    
    function loadUsers() {
        $.get('{{ route("admin.users.index") }}', function(data) {
            const users = data.data || data;
            const tbody = $('#users-table tbody');
            tbody.empty();
            
            if (users.length === 0) {
                tbody.append('<tr><td colspan="5" class="text-center">No users found</td></tr>');
                return;
            }
            
            users.forEach(function(user) {
                const row = `
                    <tr>
                        <td>${user.id}</td>
                        <td><strong>${user.name}</strong></td>
                        <td>${user.email}</td>
                        <td>${new Date(user.created_at).toLocaleDateString()}</td>
                        <td>
                            <a href="/admin/users/${user.id}/edit" class="btn btn-sm btn-primary btn-action">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="deleteUser(${user.id})" class="btn btn-sm btn-danger btn-action" ${user.id == {{ auth()->id() }} ? 'disabled title="Cannot delete your own account"' : ''}>
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }).fail(function() {
            $('#users-table tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading users</td></tr>');
        });
    }
    
    function deleteUser(id) {
        if (!confirm('Are you sure you want to delete this user?')) return;
        
        $.ajax({
            url: `/admin/users/${id}`,
            type: 'DELETE',
            success: function() {
                loadUsers();
                alert('User deleted successfully');
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Error deleting user';
                alert(message);
            }
        });
    }
</script>
@endpush
@endsection

