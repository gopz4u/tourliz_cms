@extends('layouts.admin')

@section('title', 'Create Place')

@section('content')
<div class="page-header">
    <h1 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Place</h1>
    <p class="text-muted mb-0">Add a new tourist place</p>
</div>

<div class="card">
    <div class="card-body">
        <form id="place-form">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" placeholder="Auto-generated from name">
                        <small class="form-text text-muted">Leave empty to auto-generate</small>
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
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location">
                    </div>
                    
                    <div class="mb-3">
                        <label for="region" class="form-label">Region <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="region" name="region" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" min="0">
                    </div>
                    
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <select class="form-select" id="rating" name="rating">
                            <option value="0">No Rating</option>
                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="form-text text-muted">Upload an image file (JPG, PNG, GIF, WEBP - Max 5MB)</small>
                        <div id="image-preview-container" style="display:none; margin-top: 10px;"></div>
                        <input type="hidden" id="image_path" name="image_path">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="featured" name="featured">
                            <label class="form-check-label" for="featured">Featured</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                            <label class="form-check-label" for="status">Status (Active)</label>
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
                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="keyword1, keyword2, keyword3">
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.places.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Create Place
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
        
        // Auto-generate slug from name
        $('#name').on('input', function() {
            if (!$('#slug').val() || $('#slug').data('auto-generated')) {
                const slug = $(this).val().toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
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
                // Validate file size (5MB = 5242880 bytes)
                if (file.size > 5242880) {
                    alert('Image size must be less than 5MB');
                    $(this).val('');
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPG, PNG, GIF, or WEBP)');
                    $(this).val('');
                    return;
                }
                
                const formData = new FormData();
                formData.append('image', file);
                
                // Show loading indicator
                const previewContainer = $('#image-preview-container');
                previewContainer.html('<div class="text-center p-3"><div class="spinner-border spinner-border-sm" role="status"></div> Uploading...</div>').show();
                
                $.ajax({
                    url: '{{ route("admin.upload.image") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success && response.path) {
                            $('#image_path').val(response.path);
                            previewContainer.html(`
                                <img id="image-preview" class="image-preview" src="${response.url}" alt="Preview">
                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImagePreview()">
                                    <i class="bi bi-x-circle"></i> Remove Image
                                </button>
                            `);
                        } else {
                            alert(response.message || 'Upload failed');
                            previewContainer.hide();
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error uploading image';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMsg = errors.join('\n');
                            } else if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                        }
                        alert(errorMsg);
                        previewContainer.hide();
                        $('#image').val('');
                    }
                });
            }
        });
        
        // Form submission
        $('#place-form').on('submit', function(e) {
            e.preventDefault();
            
            // Get Quill content
            const descriptionContent = descriptionEditor ? descriptionEditor.root.innerHTML : '';
            $('#description').val(descriptionContent);
            
            const formData = {
                name: $('#name').val(),
                slug: $('#slug').val() || null,
                description: $('#description').val(),
                short_description: $('#short_description').val(),
                location: $('#location').val(),
                region: $('#region').val(),
                image: $('#image_path').val(),
                price: $('#price').val() ? parseFloat($('#price').val()) : null,
                rating: parseInt($('#rating').val()) || 0,
                featured: $('#featured').is(':checked') ? 1 : 0,
                status: $('#status').is(':checked') ? 1 : 0,
                meta_title: $('#meta_title').val(),
                meta_description: $('#meta_description').val(),
                meta_keywords: $('#meta_keywords').val()
            };
            
            $.ajax({
                url: '{{ route("admin.places.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    alert('Place created successfully!');
                    window.location.href = '{{ route("admin.places.index") }}';
                },
                error: function(xhr) {
                    let errorMsg = 'Error creating place';
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
        $('#image-preview-container').html('').hide();
    }
</script>
@endpush
@endsection

