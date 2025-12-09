@extends('layouts.admin')

@section('title', 'Create Package')

@section('content')
<div class="page-header">
    <h1 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Package</h1>
    <p class="text-muted mb-0">Add a new tour package</p>
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
                        <input type="text" class="form-control" id="slug" name="slug" placeholder="Auto-generated from name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control" id="short_description" name="short_description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <div id="description-editor" style="height: 300px;"></div>
                        <textarea id="description" name="description" style="display:none;"></textarea>
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
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="form-text text-muted">Upload an image file (JPG, PNG, GIF, WEBP - Max 5MB)</small>
                        <div id="image-preview-container" style="display:none; margin-top: 10px;">
                            <img id="image-preview" class="image-preview" src="" alt="Preview">
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImagePreview()">
                                <i class="bi bi-x-circle"></i> Remove Image
                            </button>
                        </div>
                        <input type="hidden" id="image_path" name="image_path">
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
                    <i class="bi bi-save"></i> Create Package
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    var descriptionEditor;
    
    $(document).ready(function() {
        // Initialize Quill editor
        descriptionEditor = initQuillEditor('#description-editor', 300);
        
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
        
        // Handle image upload
        $('#image').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('image', file);
                
                $.ajax({
                    url: '{{ route("admin.upload.image") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success && response.path) {
                            $('#image_path').val(response.path);
                            $('#image-preview').attr('src', response.url);
                            $('#image-preview-container').show();
                        }
                    },
                    error: function() {
                        alert('Error uploading image');
                    }
                });
            }
        });
        
        $('#package-form').on('submit', function(e) {
            e.preventDefault();
            
            // Get Quill content
            const descriptionContent = descriptionEditor ? descriptionEditor.root.innerHTML : '';
            $('#description').val(descriptionContent);
            
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
                image: $('#image_path').val() || null,
                is_featured: $('#is_featured').is(':checked') ? 1 : 0,
                is_active: $('#is_active').is(':checked') ? 1 : 0,
                meta_title: $('#meta_title').val() || null,
                meta_description: $('#meta_description').val() || null,
                meta_keywords: $('#meta_keywords').val() || null
            };
            
            $.ajax({
                url: '{{ route("admin.packages.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    alert('Package created successfully!');
                    window.location.href = '{{ route("admin.packages.index") }}';
                },
                error: function(xhr) {
                    let errorMsg = 'Error creating package';
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
    
    function clearImagePreview() {
        $('#image').val('');
        $('#image_path').val('');
        $('#image-preview').attr('src', '');
        $('#image-preview-container').hide();
    }
</script>
@endpush
@endsection

