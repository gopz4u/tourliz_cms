@extends('layouts.admin')

@section('title', 'Edit Place')

@section('content')
<div class="page-header">
    <h1 class="mb-0"><i class="bi bi-pencil"></i> Edit Place</h1>
    <p class="text-muted mb-0">Update place information</p>
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
                        <input type="text" class="form-control" id="slug" name="slug">
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
                        <div id="image-preview-container" style="margin-top: 10px;">
                            <img id="image-preview" class="image-preview" src="" alt="Preview" style="display:none;">
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImagePreview()" style="display:none;">
                                <i class="bi bi-x-circle"></i> Remove Image
                            </button>
                        </div>
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
                            <input class="form-check-input" type="checkbox" id="status" name="status">
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
                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords">
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.places.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update Place
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    var descriptionEditor;
    
    $(document).ready(function() {
        const placeId = {{ $id }};
        
        // Initialize Quill editor
        descriptionEditor = initQuillEditor('#description-editor', 300);
        
        // Load place data
        $.get(`/admin/places/${placeId}`, function(place) {
            $('#name').val(place.name);
            $('#slug').val(place.slug);
            if (descriptionEditor && place.description) {
                descriptionEditor.root.innerHTML = place.description;
            }
            $('#short_description').val(place.short_description || '');
            $('#location').val(place.location || '');
            $('#region').val(place.region || '');
            if (place.image) {
                $('#image_path').val(place.image);
                $('#image-preview').attr('src', place.image.startsWith('http') ? place.image : '/storage/' + place.image).show();
                $('#image-preview-container button').show();
            }
            $('#price').val(place.price || '');
            $('#rating').val(place.rating || 0);
            $('#featured').prop('checked', place.featured);
            $('#status').prop('checked', place.status);
            $('#meta_title').val(place.meta_title || '');
            $('#meta_description').val(place.meta_description || '');
            $('#meta_keywords').val(place.meta_keywords || '');
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
                            $('#image-preview').attr('src', response.url).show();
                            $('#image-preview-container button').show();
                        }
                    },
                    error: function() {
                        alert('Error uploading image');
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
                meta_keywords: $('#meta_keywords').val(),
                _method: 'PUT'
            };
            
            $.ajax({
                url: `/admin/places/${placeId}`,
                type: 'POST',
                data: formData,
                success: function(response) {
                    alert('Place updated successfully!');
                    window.location.href = '{{ route("admin.places.index") }}';
                },
                error: function(xhr) {
                    let errorMsg = 'Error updating place';
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
        $('#image-preview').attr('src', '').hide();
        $('#image-preview-container button').hide();
    }
</script>
@endpush
@endsection

