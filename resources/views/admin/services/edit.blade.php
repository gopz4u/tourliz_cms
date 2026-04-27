@extends('layouts.admin')

@section('title', 'Edit Service')

@section('content')
    <div class="page-header">
        <h1 class="mb-0"><i class="bi bi-pencil"></i> Edit Service</h1>
        <p class="text-muted mb-0">Update service information</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="service-form">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destination_id" class="form-label"><i class="bi bi-geo-alt"></i>
                                        Destination</label>
                                    <select class="form-select" id="destination_id" name="destination_id">
                                        <option value="">Select a destination (optional)</option>
                                    </select>
                                    <small class="form-text text-muted">Choose a destination to associate this service
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
                                    <small class="form-text text-muted">Choose a package to associate this service
                                        with</small>
                                </div>
                            </div>
                        </div>

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
                            <textarea class="form-control" id="short_description" name="short_description"
                                rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <div id="description-editor" style="height: 300px;"></div>
                            <textarea id="description" name="description" style="display:none;"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Adult Price</label>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="price_2_6" class="form-label">Kids Price (Age 2-6)</label>
                                    <input type="number" step="0.01" class="form-control" id="price_2_6" name="price_2_6">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="price_6_10" class="form-label">Kids Price (Age 6-10)</label>
                                    <input type="number" step="0.01" class="form-control" id="price_6_10" name="price_6_10">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency</label>
                                    <select class="form-select" id="currency" name="currency">
                                        <option value="INR">INR - Indian Rupee</option>
                                        <option value="USD">USD - US Dollar</option>
                                        <option value="MYR">MYR - Malaysian Ringgit</option>
                                        <option value="SGD">SGD - Singapore Dollar</option>
                                        <option value="AED">AED - UAE Dirham</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="announcement_date" class="form-label">Announcement Date</label>
                                    <input type="date" class="form-control" id="announcement_date" name="announcement_date">
                                    <small class="form-text text-muted">Date when service is announced/available</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total_pax" class="form-label">Total Pax</label>
                                    <input type="number" class="form-control" id="total_pax" name="total_pax" min="1"
                                        placeholder="Total passengers">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label"><i class="bi bi-tags"></i> Category <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select a category</option>
                                <option value="Entry Tickets">Entry Tickets</option>
                                <option value="Hotels">Hotels</option>
                                <option value="Transport">Transport</option>
                                <option value="Airport Pickup">Airport Pickup</option>
                                <option value="Airport Drop">Airport Drop</option>
                                <option value="Other Services">Other Services</option>
                            </select>
                            <small class="form-text text-muted">Select the service category</small>
                        </div>

                        <!-- Hotel Amenities -->
                        <div id="hotel-amenities" class="amenities-section" style="display: none;">
                            <h6 class="mb-3"><i class="bi bi-star"></i> Hotel Amenities</h6>
                            <div class="mb-3">
                                <label for="star_rating" class="form-label">Star Rating</label>
                                <select class="form-select" id="star_rating" name="star_rating">
                                    <option value="">Select star rating</option>
                                    <option value="1">1 Star</option>
                                    <option value="2">2 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="5">5 Stars</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="accommodation_type" class="form-label">Accommodation Type</label>
                                <input type="text" class="form-control" id="accommodation_type" name="accommodation_type"
                                    placeholder="e.g., Resort, Boutique Hotel, Villa">
                            </div>
                        </div>

                        <!-- Transportation Amenities -->
                        <div id="transport-amenities" class="amenities-section" style="display: none;">
                            <h6 class="mb-3"><i class="bi bi-car-front"></i> Transportation Amenities</h6>
                            <div class="mb-3">
                                <label for="vehicle_type" class="form-label">Vehicle Type</label>
                                <input type="text" class="form-control" id="vehicle_type" name="vehicle_type"
                                    placeholder="e.g., Sedan, SUV, Bus, Van, Motorcycle">
                            </div>
                        </div>

                        <!-- Entry Tickets Amenities -->
                        <div id="ticket-amenities" class="amenities-section" style="display: none;">
                            <h6 class="mb-3"><i class="bi bi-ticket-perforated"></i> Entry Ticket Amenities</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ticket_name" class="form-label">Ticket Name</label>
                                        <input type="text" class="form-control" id="ticket_name" name="ticket_name"
                                            placeholder="e.g., Adult Ticket, Child Ticket, VIP Pass">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ticket_count" class="form-label">Ticket Count</label>
                                        <input type="number" class="form-control" id="ticket_count" name="ticket_count"
                                            min="1" placeholder="Number of tickets">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add-On Amenities -->
                        <div class="mb-4 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="bi bi-plus-circle"></i> Add-On Amenities</h6>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addAmenity()">
                                    <i class="bi bi-plus"></i> Add Amenity
                                </button>
                            </div>
                            <div id="addon-amenities-container">
                                <!-- Add-on amenities will be added here dynamically -->
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="image" class="form-label">Featured Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="form-text text-muted">Main/Featured image (JPG, PNG, GIF, WEBP - Max 5MB)</small>
                            <div id="image-preview-container" style="display:none; margin-top: 10px;">
                                <img id="image-preview" class="image-preview" src="" alt="Preview" style="display:none;">
                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImagePreview()"
                                    style="display:none;">
                                    <i class="bi bi-x-circle"></i> Remove Image
                                </button>
                            </div>
                            <input type="hidden" id="image_path" name="image_path">
                        </div>

                        <div class="mb-3">
                            <label for="gallery_images" class="form-label"><i class="bi bi-images"></i> Gallery
                                Images</label>
                            <input type="file" class="form-control" id="gallery_images" name="gallery_images[]"
                                accept="image/*" multiple>
                            <small class="form-text text-muted">Upload multiple images for gallery (JPG, PNG, GIF, WEBP -
                                Max 5MB each)</small>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" id="upload-gallery-btn"
                                style="display:none;">
                                <i class="bi bi-upload"></i> Upload Selected Images
                            </button>
                        </div>

                        <div id="gallery-preview-container">
                            <label class="form-label">Gallery Preview</label>
                            <div id="gallery-grid" class="d-flex flex-wrap gap-2"
                                style="max-height: 300px; overflow-y: auto;">
                                <!-- Gallery images will be added here -->
                            </div>
                            <input type="hidden" id="gallery_paths" name="gallery_paths">
                        </div>

                        <div class="mb-3 mt-3">
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
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active">
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order">
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
                        <i class="bi bi-save"></i> Update Service
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
                const serviceId = {{ $id }};

                // Initialize Quill editor
                descriptionEditor = initQuillEditor('#description-editor', 300);

                // Load destinations
                loadDestinations();
                loadPackages();

                // Handle category change to show/hide amenities
                function toggleAmenities(category) {
                    $('.amenities-section').hide();

                    if (category === 'Hotels') {
                        $('#hotel-amenities').show();
                    } else if (category === 'Transport' || category === 'Airport Pickup' || category === 'Airport Drop') {
                        $('#transport-amenities').show();
                    } else if (category === 'Entry Tickets') {
                        $('#ticket-amenities').show();
                    }
                }

                $('#category').on('change', function () {
                    toggleAmenities($(this).val());
                });

                $.get(`/admin/services/${serviceId}`, function (service) {
                    // Set destination if exists (wait for destinations to load)
                    setTimeout(function () {
                        if (service.destination_id) {
                            $('#destination_id').val(service.destination_id || '');
                            $('#package_id').val(service.package_id || '');
                        }
                    }, 500);

                    $('#name').val(service.name);
                    $('#slug').val(service.slug);

                    // Set Quill editor content
                    if (descriptionEditor && service.description) {
                        descriptionEditor.root.innerHTML = service.description;
                    }
                    $('#description').val(service.description || '');

                    $('#short_description').val(service.short_description || '');
                    $('#price').val(service.price || '');
                    $('#price_2_6').val(service.price_2_6 || '');
                    $('#price_6_10').val(service.price_6_10 || '');
                    $('#currency').val(service.currency || 'INR');
                    $('#announcement_date').val(service.announcement_date || '');
                    $('#total_pax').val(service.total_pax || '');

                    // Handle featured image
                    if (service.image) {
                        $('#image_path').val(service.image);
                        let imageUrl = service.image;
                        if (!imageUrl.startsWith('http')) {
                            imageUrl = imageUrl.startsWith('/') ? imageUrl : '/storage/' + service.image;
                        }
                        $('#image-preview').attr('src', imageUrl).show();
                        $('#image-preview-container').show();
                        $('#image-preview-container button').show();
                    }

                    // Load gallery images
                    if (service.gallery && Array.isArray(service.gallery) && service.gallery.length > 0) {
                        galleryImages = service.gallery;
                        service.gallery.forEach(function (imgPath) {
                            let imgUrl = imgPath;
                            if (!imgUrl.startsWith('http')) {
                                imgUrl = imgUrl.startsWith('/') ? imgUrl : '/storage/' + imgPath;
                            }
                            addGalleryImage(imgUrl, imgPath);
                        });
                        updateGalleryPaths();
                        $('#gallery-preview-container').show();
                    }

                    $('#category').val(service.category || '');

                    // Set amenities fields
                    $('#star_rating').val(service.star_rating || '');
                    $('#vehicle_type').val(service.vehicle_type || '');
                    $('#accommodation_type').val(service.accommodation_type || '');
                    $('#ticket_count').val(service.ticket_count || '');
                    $('#ticket_name').val(service.ticket_name || '');

                    // Load add-on amenities
                    if (service.addon_amenities && Array.isArray(service.addon_amenities)) {
                        service.addon_amenities.forEach(function (amenity) {
                            addAmenityWithData(amenity.type, amenity.name, amenity.value);
                        });
                    }

                    // Show appropriate amenities section based on category
                    toggleAmenities(service.category || '');

                    $('#icon').val(service.icon || '');
                    $('#is_featured').prop('checked', service.is_featured);
                    $('#is_active').prop('checked', service.is_active);
                    $('#sort_order').val(service.sort_order || 0);
                    $('#meta_title').val(service.meta_title || '');
                    $('#meta_description').val(service.meta_description || '');
                    $('#meta_keywords').val(service.meta_keywords || '');
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
                                    $('#image-preview').attr('src', response.url).show();
                                    $('#image-preview-container').show();
                                    $('#image-preview-container button').show();
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
                        url: '/admin/upload/images',
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
                    const imageId = 'gallery-img-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                    const imageHtml = `
                        <div class="position-relative" data-path="${path}" style="width: 100px; height: 100px;">
                            <img src="${url}" alt="Gallery" class="img-thumbnail" style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;" onclick="viewGalleryImage('${url}')">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeGalleryImage('${imageId}', '${path}')" style="padding: 2px 6px; font-size: 10px;">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `;
                    galleryGrid.append(imageHtml);
                }

                // Remove gallery image
                window.removeGalleryImage = function (imageId, path) {
                    galleryImages = galleryImages.filter(p => p !== path);
                    $(`[data-path="${path}"]`).remove();
                    updateGalleryPaths();

                    if (galleryImages.length === 0) {
                        $('#gallery-preview-container').hide();
                    }
                };

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

                // Update gallery paths hidden input
                function updateGalleryPaths() {
                    $('#gallery_paths').val(JSON.stringify(galleryImages));
                }

                // Add-on Amenities Management
                let amenityCounter = 0;

                window.addAmenity = function (type = '', name = '', value = '') {
                    const container = $('#addon-amenities-container');
                    const amenityId = 'amenity-' + amenityCounter++;
                    const amenityHtml = `
                        <div class="card mb-2 amenity-item" data-amenity-id="${amenityId}">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <label class="form-label small">Type</label>
                                            <select class="form-select form-select-sm amenity-type" required>
                                                <option value="">Select Type</option>
                                                <option value="hotel" ${type === 'hotel' ? 'selected' : ''}>Hotel</option>
                                                <option value="transport" ${type === 'transport' ? 'selected' : ''}>Transport</option>
                                                <option value="ticket" ${type === 'ticket' ? 'selected' : ''}>Ticket</option>
                                                <option value="accommodation" ${type === 'accommodation' ? 'selected' : ''}>Accommodation</option>
                                                <option value="other" ${type === 'other' ? 'selected' : ''}>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="form-label small">Name</label>
                                            <input type="text" class="form-control form-control-sm amenity-name" value="${name}" placeholder="e.g., WiFi, Pool, Breakfast" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="form-label small">Value</label>
                                            <input type="text" class="form-control form-control-sm amenity-value" value="${value}" placeholder="e.g., Free, Included, 5 Stars">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="mb-2">
                                            <label class="form-label small">&nbsp;</label>
                                            <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeAmenity('${amenityId}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(amenityHtml);
                };

                window.addAmenityWithData = function (type, name, value) {
                    addAmenity(type, name, value || '');
                };

                window.removeAmenity = function (amenityId) {
                    $(`.amenity-item[data-amenity-id="${amenityId}"]`).remove();
                };

                // Clear featured image preview
                window.clearImagePreview = function () {
                    $('#image').val('');
                    $('#image_path').val('');
                    $('#image-preview').attr('src', '').hide();
                    $('#image-preview-container button').hide();
                };

                $('#service-form').on('submit', function (e) {
                    e.preventDefault();

                    // Get Quill content
                    const descriptionContent = descriptionEditor ? descriptionEditor.root.innerHTML : '';
                    $('#description').val(descriptionContent);

                    // Get gallery paths
                    const galleryPaths = galleryImages.length > 0 ? galleryImages : null;

                    // Collect add-on amenities
                    const addonAmenities = [];
                    $('.amenity-item').each(function () {
                        const type = $(this).find('.amenity-type').val();
                        const name = $(this).find('.amenity-name').val();
                        const value = $(this).find('.amenity-value').val();

                        if (type && name) {
                            addonAmenities.push({
                                type: type,
                                name: name,
                                value: value || null
                            });
                        }
                    });

                    const formData = {
                        destination_id: $('#destination_id').val() || null,
                        package_id: $('#package_id').val() || null,
                        name: $('#name').val(),
                        slug: $('#slug').val() || null,
                        description: $('#description').val(),
                        short_description: $('#short_description').val(),
                        price: $('#price').val() ? parseFloat($('#price').val()) : null,
                        price_2_6: $('#price_2_6').val() ? parseFloat($('#price_2_6').val()) : null,
                        price_6_10: $('#price_6_10').val() ? parseFloat($('#price_6_10').val()) : null,
                        currency: $('#currency').val() || 'INR',
                        announcement_date: $('#announcement_date').val() || null,
                        total_pax: $('#total_pax').val() ? parseInt($('#total_pax').val()) : null,
                        image: $('#image_path').val() || null,
                        gallery: galleryPaths,
                        category: $('#category').val(),
                        star_rating: $('#star_rating').val() ? parseInt($('#star_rating').val()) : null,
                        vehicle_type: $('#vehicle_type').val() || null,
                        accommodation_type: $('#accommodation_type').val() || null,
                        ticket_count: $('#ticket_count').val() ? parseInt($('#ticket_count').val()) : null,
                        ticket_name: $('#ticket_name').val() || null,
                        addon_amenities: addonAmenities.length > 0 ? addonAmenities : null,
                        icon: $('#icon').val(),
                        is_featured: $('#is_featured').is(':checked') ? 1 : 0,
                        is_active: $('#is_active').is(':checked') ? 1 : 0,
                        sort_order: parseInt($('#sort_order').val()) || 0,
                        meta_title: $('#meta_title').val(),
                        meta_description: $('#meta_description').val(),
                        meta_keywords: $('#meta_keywords').val(),
                        _method: 'PUT'
                    };

                    $.ajax({
                        url: `/admin/services/${serviceId}`,
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            alert('Service updated successfully!');
                            window.location.href = '{{ route("admin.services.index") }}';
                        },
                        error: function (xhr) {
                            alert('Error updating service');
                        }
                    });
                });

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

                function loadDestinations() {
                    $.ajax({
                        url: '{{ route("admin.destinations.index") }}?per_page=1000',
                        type: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function (response) {
                            const destinationSelect = $('#destination_id');
                            let destinations = [];

                            // Handle paginated response
                            if (response && response.data && Array.isArray(response.data)) {
                                destinations = response.data;
                            } else if (Array.isArray(response)) {
                                destinations = response;
                            }

                            if (destinations.length > 0) {
                                destinations.forEach(function (destination) {
                                    destinationSelect.append(`<option value="${destination.id}">${destination.name}</option>`);
                                });
                            } else {
                                console.warn('No destinations found');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Failed to load destinations:', xhr, status, error);
                            console.error('Response:', xhr.responseText);
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection