@extends('layouts.admin')

@section('title', 'Create Service')

@section('content')
<div class="page-header">
    <h1 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Service</h1>
    <p class="text-muted mb-0">Add a new tourism service</p>
</div>

<div class="card">
    <div class="card-body">
        <form id="service-form">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" placeholder="Auto-generated from name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control" id="short_description" name="short_description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency</label>
                                <input type="text" class="form-control" id="currency" name="currency" value="USD" maxlength="3">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category" name="category">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="image" class="form-label">Image URL</label>
                        <input type="text" class="form-control" id="image" name="image">
                    </div>
                    
                    <div class="mb-3">
                        <label for="icon" class="form-label">Icon</label>
                        <input type="text" class="form-control" id="icon" name="icon" placeholder="e.g., bi-hotel">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                            <label class="form-check-label" for="is_featured">Featured</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="0">
                    </div>
                </div>
            </div>
            
            <hr>
            
            <h5>SEO Settings</h5>
            <div class="mb-3">
                <label for="meta_title" class="form-label">Meta Title</label>
                <input type="text" class="form-control" id="meta_title" name="meta_title">
            </div>
            
            <div class="mb-3">
                <label for="meta_description" class="form-label">Meta Description</label>
                <textarea class="form-control" id="meta_description" name="meta_description" rows="2"></textarea>
            </div>
            
            <div class="mb-3">
                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords">
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Create Service
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#name').on('input', function() {
            if (!$('#slug').val() || $('#slug').data('auto-generated')) {
                const slug = $(this).val().toLowerCase().replace(/[^\w\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').trim();
                $('#slug').val(slug);
                $('#slug').data('auto-generated', true);
            }
        });
        
        $('#slug').on('input', function() {
            $(this).data('auto-generated', false);
        });
        
        $('#service-form').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                name: $('#name').val(),
                slug: $('#slug').val() || null,
                description: $('#description').val(),
                short_description: $('#short_description').val(),
                price: $('#price').val() ? parseFloat($('#price').val()) : null,
                currency: $('#currency').val() || 'USD',
                image: $('#image').val(),
                category: $('#category').val(),
                icon: $('#icon').val(),
                is_featured: $('#is_featured').is(':checked'),
                is_active: $('#is_active').is(':checked'),
                sort_order: parseInt($('#sort_order').val()) || 0,
                meta_title: $('#meta_title').val(),
                meta_description: $('#meta_description').val(),
                meta_keywords: $('#meta_keywords').val()
            };
            
            $.ajax({
                url: '{{ route("admin.services.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    alert('Service created successfully!');
                    window.location.href = '{{ route("admin.services.index") }}';
                },
                error: function(xhr) {
                    let errorMsg = 'Error creating service';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                }
            });
        });
    });
</script>
@endpush
@endsection

