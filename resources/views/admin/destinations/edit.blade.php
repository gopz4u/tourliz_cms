@extends('layouts.admin')

@section('title', 'Edit Destination')

@section('content')
    <div class="page-header">
        <h1 class="mb-0"><i class="bi bi-pencil"></i> Edit Destination</h1>
        <p class="text-muted mb-0">Update destination information (Country > Location > City)</p>
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
                            <input type="text" class="form-control" id="slug" name="slug">
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
                            <div id="image-preview-container" style="margin-top: 10px;">
                                <img id="image-preview" class="image-preview" src="" alt="Preview" style="display:none;">
                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImagePreview()"
                                    style="display:none;">
                                    <i class="bi bi-x-circle"></i> Remove Image
                                </button>
                            </div>
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
                                <input class="form-check-input" type="checkbox" id="status" name="status">
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
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords">
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
                        <i class="bi bi-save"></i> Update Destination
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
                const destinationId = {{ $id }};

                // Initialize Select2 for Country
                $('#country').select2({
                    theme: 'bootstrap-5',
                    tags: true,
                    placeholder: 'Select or type a country',
                    allowClear: true
                });

                // Initialize Quill editor
                descriptionEditor = initQuillEditor('#description-editor', 300);

                // Load existing countries
                $.get('{{ route("admin.destinations.countries") }}', function (countries) {
                    const select = $('#country');
                    countries.forEach(function (country) {
                        select.append(new Option(country, country, false, false));
                    });

                    // Load destination data
                    $.get(`/admin/destinations/${destinationId}`, function (destination) {
                        $('#name').val(destination.name);
                        $('#slug').val(destination.slug);
                        if (descriptionEditor && destination.description) {
                            descriptionEditor.root.innerHTML = destination.description;
                        }
                        $('#short_description').val(destination.short_description || '');

                        // Set country and trigger select2
                        const existingCountry = destination.country || '';
                        if (existingCountry && !select.find(`option[value="${existingCountry}"]`).length) {
                            select.append(new Option(existingCountry, existingCountry, true, true));
                        } else {
                            select.val(existingCountry);
                        }
                        select.trigger('change');
                        $('#location').val(destination.location || '');
                        $('#city').val(destination.city || '');
                        if (destination.image) {
                            $('#image_path').val(destination.image);
                            let imageUrl = destination.image;
                            if (!imageUrl.startsWith('http')) {
                                imageUrl = destination.image.startsWith('/') ? destination.image : '/storage/' + destination.image;
                            }
                            $('#image-preview').attr('src', imageUrl).show();
                            $('#image-preview-container button').show();
                        }
                        $('#price').val(destination.price || '');
                        $('#rating').val(destination.rating || 0);
                        $('#featured').prop('checked', destination.featured);
                        $('#status').prop('checked', destination.status);
                        $('#meta_title').val(destination.meta_title || '');
                        $('#meta_description').val(destination.meta_description || '');
                        $('#meta_keywords').val(destination.meta_keywords || '');
                    });
                });

                // Handle image upload
                $('#image').on('change', function (e) {
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
                            success: function (response) {
                                if (response.success && response.path) {
                                    $('#image_path').val(response.path);
                                    $('#image-preview').attr('src', response.url).show();
                                    $('#image-preview-container button').show();
                                }
                            },
                            error: function () {
                                alert('Error uploading image');
                            }
                        });
                    }
                });

                // Form submission
                $('#place-form').on('submit', function (e) {
                    e.preventDefault();

                    const descriptionContent = descriptionEditor ? descriptionEditor.root.innerHTML : '';
                    if ($('#description').length) {
                        $('#description').val(descriptionContent);
                    }

                    const formData = {
                        name: $('#name').val(),
                        slug: $('#slug').val() || null,
                        description: descriptionContent,
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
                        meta_keywords: $('#meta_keywords').val(),
                        _method: 'PUT'
                    };

                    $.ajax({
                        url: `/admin/destinations/${destinationId}`,
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            alert('Destination updated successfully!');
                            window.location.href = '{{ route("admin.destinations.index") }}';
                        },
                        error: function (xhr) {
                            let errorMsg = 'Error updating destination';
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

            function clearImagePreview() {
                $('#image').val('');
                $('#image_path').val('');
                $('#image-preview').attr('src', '').hide();
                $('#image-preview-container button').hide();
            }
        </script>
    @endpush
@endsection