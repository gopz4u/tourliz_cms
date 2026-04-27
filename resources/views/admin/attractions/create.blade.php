@extends('layouts.admin')

@section('title', 'Create Attraction')

@section('content')
    <div class="page-header">
        <h1 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Attraction</h1>
        <p class="text-muted mb-0">Add a new tourist attraction</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="attraction-form">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Attraction Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                placeholder="Auto-generated from name">
                        </div>

                        <div class="mb-3">
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea class="form-control" id="short_description" name="short_description" rows="3"
                                placeholder="Brief description of the attraction"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Detail Description</label>
                            <div id="description-editor" style="height: 300px;"></div>
                            <textarea id="description" name="description" style="display:none;"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destination_id" class="form-label"><i class="bi bi-geo-alt"></i> Destination
                                        (City)</label>
                                    <select class="form-select" id="destination_id" name="destination_id">
                                        <option value="">Select a city (optional)</option>
                                    </select>
                                    <small class="form-text text-muted">Choose a destination to associate this attraction
                                        with</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="package_id" class="form-label"><i class="bi bi-briefcase"></i>
                                        Package</label>
                                    <select class="form-select" id="package_id" name="package_id">
                                        <option value="">Select a package (optional)</option>
                                    </select>
                                    <small class="form-text text-muted">Choose a package to associate this attraction
                                        with</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Adult Price</label>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price"
                                        placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="offer_price" class="form-label">Offer Price</label>
                                    <input type="number" step="0.01" class="form-control" id="offer_price"
                                        name="offer_price" placeholder="0.00">
                                    <small class="form-text text-muted">Discounted/offer price (optional)</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="price_2_6" class="form-label">Kids Price (Age 2-6)</label>
                                    <input type="number" step="0.01" class="form-control" id="price_2_6" name="price_2_6"
                                        placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="price_6_10" class="form-label">Kids Price (Age 6-10)</label>
                                    <input type="number" step="0.01" class="form-control" id="price_6_10" name="price_6_10"
                                        placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency</label>
                                    <select class="form-select" id="currency" name="currency">
                                        <option value="INR" selected>INR - Indian Rupee</option>
                                        <option value="USD">USD - US Dollar</option>
                                        <option value="MYR">MYR - Malaysian Ringgit</option>
                                        <option value="SGD">SGD - Singapore Dollar</option>
                                        <option value="AED">AED - UAE Dirham</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="announcement_date" class="form-label">Announcement Date</label>
                                    <input type="date" class="form-control" id="announcement_date" name="announcement_date">
                                    <small class="form-text text-muted">Date when attraction is announced/available</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_pax" class="form-label">Total Pax</label>
                                    <input type="number" class="form-control" id="total_pax" name="total_pax" min="1"
                                        placeholder="Total passengers">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="image" class="form-label">Feature Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="form-text text-muted">Main/Featured image (JPG, PNG, GIF, WEBP - Max 5MB)</small>
                            <div id="image-preview-container" style="display:none; margin-top: 10px;">
                                <img id="image-preview" class="image-preview" src="" alt="Preview">
                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImagePreview()">
                                    <i class="bi bi-x-circle"></i> Remove Image
                                </button>
                            </div>
                            <input type="hidden" id="image_path" name="image_path">
                        </div>

                        <div class="mb-3">
                            <label for="gallery_images" class="form-label"><i class="bi bi-images"></i> Image Album</label>
                            <input type="file" class="form-control" id="gallery_images" name="gallery_images[]"
                                accept="image/*" multiple>
                            <small class="form-text text-muted">Upload multiple images for gallery (JPG, PNG, GIF, WEBP -
                                Max 5MB each)</small>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" id="upload-gallery-btn"
                                style="display:none;">
                                <i class="bi bi-upload"></i> Upload Selected Images
                            </button>
                        </div>

                        <div id="gallery-preview-container" style="display:none;">
                            <label class="form-label">Gallery Preview</label>
                            <div id="gallery-grid" class="d-flex flex-wrap gap-2"
                                style="max-height: 300px; overflow-y: auto;">
                                <!-- Gallery images will be added here -->
                            </div>
                            <input type="hidden" id="gallery_paths" name="gallery_paths">
                        </div>

                        <div class="mb-3 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">Enable</label>
                            </div>
                            <small class="form-text text-muted">Enable or disable this attraction</small>
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
                    <a href="{{ route('admin.attractions.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Create Attraction
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Gallery images array
            let galleryImages = [];
            var descriptionEditor;

            $(document).ready(function () {
                // Initialize Quill editor
                descriptionEditor = initQuillEditor('#description-editor', 300);

                // Load destinations
                loadDestinations();

                // Load packages
                loadPackages();

                $('#name').on('input', function () {
                    if (!$('#slug').val() || $('#slug').data('auto-generated')) {
                        const slug = $(this).val().toLowerCase().replace(/[^\w\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').trim();
                        $('#slug').val(slug);
                        $('#slug').data('auto-generated', true);
                    }
                });

                $('#slug').on('input', function () {
                    $(this).data('auto-generated', false);
                });

                // Handle featured image upload
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
                                    $('#image-preview').attr('src', response.url);
                                    $('#image-preview-container').show();
                                }
                            },
                            error: function () {
                                alert('Error uploading image');
                            }
                        });
                    }
                });

                // Handle gallery images selection
                $('#gallery_images').on('change', function (e) {
                    const files = e.target.files;
                    if (files && files.length > 0) {
                        $('#upload-gallery-btn').show();
                    } else {
                        $('#upload-gallery-btn').hide();
                    }
                });

                // Upload gallery images
                $('#upload-gallery-btn').on('click', function () {
                    const files = $('#gallery_images')[0].files;
                    if (!files || files.length === 0) {
                        alert('Please select images to upload');
                        return;
                    }

                    const formData = new FormData();
                    for (let i = 0; i < files.length; i++) {
                        formData.append('images[]', files[i]);
                    }

                    $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Uploading...');

                    $.ajax({
                        url: '{{ route("admin.upload.images") }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success && response.images) {
                                response.images.forEach(function (img) {
                                    galleryImages.push(img.path);
                                    addGalleryImage(img.url, img.path);
                                });
                                updateGalleryPaths();
                                $('#gallery-preview-container').show();
                                $('#gallery_images').val('');
                                $('#upload-gallery-btn').hide().prop('disabled', false).html('<i class="bi bi-upload"></i> Upload Selected Images');
                            }
                        },
                        error: function (xhr) {
                            let errorMsg = 'Error uploading images';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            alert(errorMsg);
                            $('#upload-gallery-btn').prop('disabled', false).html('<i class="bi bi-upload"></i> Upload Selected Images');
                        }
                    });
                });

                // Add gallery image to preview
                function addGalleryImage(url, path) {
                    const galleryGrid = $('#gallery-grid');
                    const imageHtml = `
                                <div class="position-relative" data-path="${path}" style="width: 100px; height: 100px;">
                                    <img src="${url}" alt="Gallery" class="img-thumbnail" style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;" onclick="viewGalleryImage('${url}')">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeGalleryImage('${path}')" style="padding: 2px 6px; font-size: 10px;">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            `;
                    galleryGrid.append(imageHtml);
                }

                // Remove gallery image
                window.removeGalleryImage = function (path) {
                    galleryImages = galleryImages.filter(p => p !== path);
                    $(`[data-path="${path}"]`).remove();
                    updateGalleryPaths();

                    if (galleryImages.length === 0) {
                        $('#gallery-preview-container').hide();
                    }
                };

                // Update gallery paths hidden input
                function updateGalleryPaths() {
                    $('#gallery_paths').val(JSON.stringify(galleryImages));
                }

                // View gallery image in modal
                window.viewGalleryImage = function (url) {
                    if ($('#gallery-modal').length === 0) {
                        $('body').append(`
                                    <div class="modal fade" id="gallery-modal" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Gallery Image</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img id="gallery-modal-img" src="" class="img-fluid" style="max-height: 70vh;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                    }
                    $('#gallery-modal-img').attr('src', url);
                    new bootstrap.Modal(document.getElementById('gallery-modal')).show();
                };

                // Clear featured image preview
                window.clearImagePreview = function () {
                    $('#image').val('');
                    $('#image_path').val('');
                    $('#image-preview').attr('src', '');
                    $('#image-preview-container').hide();
                };

                function loadDestinations() {
                    $.get('{{ route("admin.destinations.index") }}', function (data) {
                        const destinations = data.data || data;
                        const destinationSelect = $('#destination_id');

                        if (Array.isArray(destinations)) {
                            destinations.forEach(function (dest) {
                                destinationSelect.append(`<option value="${dest.id}">${dest.city} (${dest.name || dest.country})</option>`);
                            });
                        }
                    }).fail(function () {
                        console.error('Failed to load destinations');
                    });
                }

                function loadPackages() {
                    $.get('{{ route("admin.packages.index") }}', function (data) {
                        const packages = data.data || data;
                        const packageSelect = $('#package_id');

                        if (Array.isArray(packages)) {
                            packages.forEach(function (pkg) {
                                packageSelect.append(`<option value="${pkg.id}">${pkg.name}</option>`);
                            });
                        }
                    }).fail(function () {
                        console.error('Failed to load packages');
                    });
                }

                $('#attraction-form').on('submit', function (e) {
                    e.preventDefault();

                    // Get Quill content
                    const descriptionContent = descriptionEditor ? descriptionEditor.root.innerHTML : '';
                    $('#description').val(descriptionContent);

                    // Get gallery paths
                    const galleryPaths = galleryImages.length > 0 ? galleryImages : null;

                    const formData = {
                        name: $('#name').val(),
                        slug: $('#slug').val() || null,
                        short_description: $('#short_description').val(),
                        description: $('#description').val(),
                        destination_id: $('#destination_id').val() || null,
                        package_id: $('#package_id').val() || null,
                        price: $('#price').val() ? parseFloat($('#price').val()) : null,
                        offer_price: $('#offer_price').val() ? parseFloat($('#offer_price').val()) : null,
                        price_2_6: $('#price_2_6').val() ? parseFloat($('#price_2_6').val()) : null,
                        price_6_10: $('#price_6_10').val() ? parseFloat($('#price_6_10').val()) : null,
                        currency: $('#currency').val() || 'INR',
                        announcement_date: $('#announcement_date').val() || null,
                        total_pax: $('#total_pax').val() ? parseInt($('#total_pax').val()) : null,
                        image: $('#image_path').val() || null,
                        gallery: galleryPaths,
                        is_active: $('#is_active').is(':checked') ? 1 : 0,
                        meta_title: $('#meta_title').val(),
                        meta_description: $('#meta_description').val(),
                        meta_keywords: $('#meta_keywords').val()
                    };

                    $.ajax({
                        url: '{{ route("admin.attractions.store") }}',
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            alert('Attraction created successfully!');
                            window.location.href = '{{ route("admin.attractions.index") }}';
                        },
                        error: function (xhr) {
                            let errorMsg = 'Error creating attraction';
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