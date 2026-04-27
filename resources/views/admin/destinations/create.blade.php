@extends('layouts.admin')

@section('title', 'Create Destination')

@section('content')
    <div class="page-header">
        <h1 class="mb-0"><i class="bi bi-plus-circle"></i> Add New Destination</h1>
        <p class="text-muted mb-0">Add a new destination (Country > Location > City)</p>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @endpush

    <div class="card">
        <div class="card-body">
            <form id="place-form">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Display Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Paris City Tour"
                                required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="country" class="form-label">Country <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="country" name="country" required>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location (State/Region) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="location" name="location"
                                        placeholder="e.g. Ile-de-France" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="e.g. Paris"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                placeholder="Auto-generated from name">
                            <small class="form-text text-muted">Leave empty to auto-generate</small>
                        </div>

                        <div class="mb-3">
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea class="form-control" id="short_description" name="short_description"
                                rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <div id="description-editor" style="height: 300px;"></div>
                            <textarea id="description" name="description" style="display:none;"></textarea>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="price" class="form-label">Starting Price</label>
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
                            <label for="image" class="form-label">Featured Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div id="image-preview-container" style="display:none; margin-top: 10px;"></div>
                            <input type="hidden" id="image_path" name="image_path">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="featured" name="featured">
                                <label class="form-check-label" for="featured">Featured Destination</label>
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
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                placeholder="keyword1, keyword2">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <textarea class="form-control" id="meta_description" name="meta_description" rows="2"></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.destinations.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Destination
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            var descriptionEditor;

            $(document).ready(function () {
                // Initialize Select2 for Country
                $('#country').select2({
                    theme: 'bootstrap-5',
                    tags: true,
                    placeholder: 'Select or type a country',
                    allowClear: true
                });

                // Load existing countries
                $.get('{{ route("admin.destinations.countries") }}', function (data) {
                    const select = $('#country');
                    data.forEach(function (country) {
                        select.append(new Option(country, country, false, false));
                    });
                });

                // Initialize Quill editor
                descriptionEditor = initQuillEditor('#description-editor', 300);

                // Auto-generate slug from name
                $('#name').on('input', function () {
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

                $('#slug').on('input', function () {
                    $(this).data('auto-generated', false);
                });

                // Handle image upload
                $('#image').on('change', function (e) {
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
                            success: function (response) {
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
                            error: function (xhr) {
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
                $('#place-form').on('submit', function (e) {
                    e.preventDefault();

                    // Get Quill content
                    const descriptionContent = descriptionEditor ? descriptionEditor.root.innerHTML : '';
                    $('#description').val(descriptionContent);

                    const formData = {
                        name: $('#name').val(),
                        slug: $('#slug').val() || null,
                        description: $('#description').val(),
                        short_description: $('#short_description').val(),
                        country: $('#country').val(),
                        location: $('#location').val(),
                        city: $('#city').val(),
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
                        url: '{{ route("admin.destinations.store") }}',
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            alert('Destination added successfully!');
                            window.location.href = '{{ route("admin.destinations.index") }}';
                        },
                        error: function (xhr) {
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