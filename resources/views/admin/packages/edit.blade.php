@extends('layouts.admin')

@section('title', 'Edit Package')

@section('content')
<div class="page-header">
    <h1 class="mb-0"><i class="bi bi-pencil"></i> Edit Package</h1>
    <p class="text-muted mb-0">Update package information</p>
</div>

<div class="card">
    <div class="card-body">
        <form id="package-form">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug">
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
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_price" class="form-label">Discount Price</label>
                                <input type="number" step="0.01" class="form-control" id="discount_price" name="discount_price">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duration_days" class="form-label">Duration (Days)</label>
                                <input type="number" class="form-control" id="duration_days" name="duration_days" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duration_nights" class="form-label">Duration (Nights)</label>
                                <input type="number" class="form-control" id="duration_nights" name="duration_nights" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="image" class="form-label">Image URL</label>
                        <input type="text" class="form-control" id="image" name="image">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                            <label class="form-check-label" for="is_featured">Featured</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active">
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
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
                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update Package
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        const packageId = {{ $id }};
        
        $.get(`/admin/packages/${packageId}`, function(pkg) {
            $('#name').val(pkg.name);
            $('#slug').val(pkg.slug);
            $('#description').val(pkg.description || '');
            $('#short_description').val(pkg.short_description || '');
            $('#price').val(pkg.price);
            $('#discount_price').val(pkg.discount_price || '');
            
            // Parse duration string to extract days and nights
            if (pkg.duration) {
                const durationStr = pkg.duration.toLowerCase();
                const daysMatch = durationStr.match(/(\d+)\s*days?/);
                const nightsMatch = durationStr.match(/(\d+)\s*nights?/);
                if (daysMatch) $('#duration_days').val(daysMatch[1]);
                if (nightsMatch) $('#duration_nights').val(nightsMatch[1]);
            }
            
            $('#image').val(pkg.image || '');
            $('#is_featured').prop('checked', pkg.featured || false);
            $('#is_active').prop('checked', pkg.status !== undefined ? pkg.status : true);
            $('#meta_title').val(pkg.meta_title || '');
            $('#meta_description').val(pkg.meta_description || '');
            $('#meta_keywords').val(pkg.meta_keywords || '');
        });
        
        $('#package-form').on('submit', function(e) {
            e.preventDefault();
            
            // Validate price
            const priceValue = $('#price').val();
            if (!priceValue || isNaN(parseFloat(priceValue)) || parseFloat(priceValue) < 0) {
                alert('Please enter a valid price (must be a number >= 0)');
                return;
            }
            
            const formData = {
                name: $('#name').val(),
                slug: $('#slug').val() || null,
                description: $('#description').val(),
                short_description: $('#short_description').val(),
                price: parseFloat(priceValue),
                discount_price: $('#discount_price').val() ? parseFloat($('#discount_price').val()) : null,
                duration_days: $('#duration_days').val() ? parseInt($('#duration_days').val()) : null,
                duration_nights: $('#duration_nights').val() ? parseInt($('#duration_nights').val()) : null,
                image: $('#image').val() || null,
                is_featured: $('#is_featured').is(':checked') ? 1 : 0,
                is_active: $('#is_active').is(':checked') ? 1 : 0,
                meta_title: $('#meta_title').val() || null,
                meta_description: $('#meta_description').val() || null,
                meta_keywords: $('#meta_keywords').val() || null,
                _method: 'PUT'
            };
            
            $.ajax({
                url: `/admin/packages/${packageId}`,
                type: 'POST',
                data: formData,
                success: function(response) {
                    alert('Package updated successfully!');
                    window.location.href = '{{ route("admin.packages.index") }}';
                },
                error: function(xhr) {
                    let errorMsg = 'Error updating package';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            // Handle validation errors
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

