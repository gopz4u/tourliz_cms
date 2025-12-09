@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="page-header">
    <h1 class="mb-0"><i class="bi bi-pencil"></i> Edit User</h1>
    <p class="text-muted mb-0">Update user information</p>
</div>

<div class="card">
    <div class="card-body">
        <form id="user-form">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="8">
                        <small class="form-text text-muted">Leave blank to keep current password. Minimum 8 characters if changing.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="8">
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update User
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const userId = {{ $id }};
    
    $(document).ready(function() {
        // Load user data
        $.get(`/admin/users/${userId}`, function(user) {
            $('#name').val(user.name);
            $('#email').val(user.email);
        }).fail(function() {
            alert('Error loading user data');
            window.location.href = '{{ route("admin.users.index") }}';
        });
        
        $('#user-form').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                name: $('#name').val(),
                email: $('#email').val(),
                password: $('#password').val() || null,
                password_confirmation: $('#password_confirmation').val() || null
            };
            
            // Remove password fields if password is empty
            if (!formData.password) {
                delete formData.password;
                delete formData.password_confirmation;
            }
            
            $.ajax({
                url: `/admin/users/${userId}`,
                type: 'PUT',
                data: formData,
                success: function(response) {
                    alert('User updated successfully!');
                    window.location.href = '{{ route("admin.users.index") }}';
                },
                error: function(xhr) {
                    let errorMsg = 'Error updating user';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMsg = 'Validation errors:\n' + errors.join('\n');
                        } else if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                    }
                    alert(errorMsg);
                }
            });
        });
    });
</script>
@endpush
@endsection

