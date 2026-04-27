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
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="country_select" class="form-label"><i class="bi bi-geo-alt"></i>
                                        Country</label>
                                    <select class="form-select" id="country_select" name="country">
                                        <option value="">Select Country</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="location_select" class="form-label"><i class="bi bi-map"></i>
                                        Location</label>
                                    <select class="form-select" id="location_select" name="location">
                                        <option value="">Select Location</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="destination_id" class="form-label"><i class="bi bi-pin-map"></i>
                                        Primary City</label>
                                    <select class="form-select" id="destination_id" name="destination_id">
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted mb-3 d-block">Choose a primary destination to associate this package
                            with</small>

                        <div class="mb-3">
                            <label for="destination_ids" class="form-label"><i class="bi bi-geo"></i> Other Destinations Covered</label>
                            <select class="form-select" id="destination_ids" name="destination_ids[]" multiple>
                                <!-- Will be populated with all destinations -->
                            </select>
                            <small class="form-text text-muted">Select all cities/destinations covered in this package itinerary for better website display.</small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0"><i class="bi bi-shop"></i> Source Vendor(s)</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSupplierRow()">
                                    <i class="bi bi-plus"></i> Add Vendor
                                </button>
                            </div>
                            <div id="suppliers-container">
                            </div>
                            <small class="form-text text-muted">Select one or more 3rd party vendors</small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0"><i class="bi bi-tags"></i> Service Category</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCategoryRow()">
                                    <i class="bi bi-plus"></i> Add Category
                                </button>
                            </div>
                            <div id="categories-container">
                            </div>
                            <small class="form-text text-muted">Select one or more service categories</small>
                        </div>

                        <div class="mb-3">
                            <label for="package_category" class="form-label"><i class="bi bi-star"></i> Package
                                Category</label>
                            <select class="form-select" id="package_category" name="package_category">
                                <option value="">Select a package category (optional)</option>
                                <option value="Honeymoon">Honeymoon</option>
                                <option value="Budget">Budget</option>
                                <option value="Standard">Standard</option>
                                <option value="Premium">Premium</option>
                                <option value="Platinum">Platinum</option>
                            </select>
                            <small class="form-text text-muted">Select the package category (Honeymoon, Budget, Standard,
                                Premium, Platinum)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-airplane"></i> Flight Ticket</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="includes_flight" id="includes_flight_yes"
                                    value="1">
                                <label class="form-check-label" for="includes_flight_yes">
                                    With Flight Ticket
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="includes_flight" id="includes_flight_no"
                                    value="0">
                                <label class="form-check-label" for="includes_flight_no">
                                    Without Flight Ticket
                                </label>
                            </div>
                            <small class="form-text text-muted">Select whether this package includes flight tickets</small>
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

                        <!-- Cost Components Calculator -->
                        <div class="mb-4 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="bi bi-calculator"></i> Package Cost Components</h6>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addAmenity()">
                                    <i class="bi bi-plus"></i> Add Cost Component
                                </button>
                            </div>
                            <div id="addon-amenities-container">
                                <!-- Cost components will be added here dynamically -->
                            </div>
                            <small class="form-text text-muted">Add suppliers and select actual inventory (Rooms, Transport, Tickets) to dynamically calculate the package cost.</small>
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="net_price" class="form-label text-success"><i class="bi bi-tag-fill"></i>
                                        Net Price (Vendor)</label>
                                    <input type="number" step="0.01" class="form-control" id="net_price" name="net_price"
                                        placeholder="Vendor Cost">
                                    <small class="text-muted">Total cost from vendors</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="markup_percentage" class="form-label text-primary"><i
                                            class="bi bi-graph-up-arrow"></i> Markup (%)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" id="markup_percentage"
                                            name="markup_percentage" placeholder="Percentage" value="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="text-muted" id="markup-amount-display">Amount: 0.00</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price" class="form-label text-danger fw-bold"><i
                                            class="bi bi-cash-coin"></i> Selling Price (Adult)</label>
                                    <input type="number" step="0.01" class="form-control fw-bold" id="price" name="price"
                                        required>
                                    <small class="text-muted">Final displayed price</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Removed Original Price col since it is moved above -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="discount_price" class="form-label">Discount Price</label>
                                    <input type="number" step="0.01" class="form-control" id="discount_price"
                                        name="discount_price">
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
                        </div>

                        <div class="row">
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="announcement_date" class="form-label">Announcement Date</label>
                                    <input type="date" class="form-control" id="announcement_date" name="announcement_date">
                                    <small class="form-text text-muted">Date when package is announced/available</small>
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_days" class="form-label">Duration (Days)</label>
                                    <input type="number" class="form-control" id="duration_days" name="duration_days"
                                        min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_nights" class="form-label">Duration (Nights)</label>
                                    <input type="number" class="form-control" id="duration_nights" name="duration_nights"
                                        min="0">
                                </div>
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
                    <div>
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <a href="/admin/itineraries/{{ $id }}/edit" class="btn btn-info text-white">
                            <i class="bi bi-calendar3"></i> Manage Itinerary
                        </a>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Package
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            var descriptionEditor;
            // Gallery images array
            let galleryImages = [];

            $(document).ready(function () {
                // Initialize Quill editor
                descriptionEditor = initQuillEditor('#description-editor', 300);

                const packageId = {{ $id }};

                // Handle category change to show/hide amenities
                window.handleCategoryChange = function () {
                    const categories = [];
                    $('.category-select').each(function() {
                        const val = $(this).val();
                        if (val) categories.push(val);
                    });

                    $('.amenities-section').hide();

                    if (categories.includes('Hotels')) {
                        $('#hotel-amenities').show();
                    } 
                    if (categories.includes('Transport') || categories.includes('Airport Pickup') || categories.includes('Airport Drop')) {
                        $('#transport-amenities').show();
                    } 
                    if (categories.includes('Entry Tickets')) {
                        $('#ticket-amenities').show();
                    }
                };

                let cachedSuppliers = [];
                window.addSupplierRow = function(selectedValue = '') {
                    let options = '<option value="">Select a vendor</option>';
                    cachedSuppliers.forEach(function(s) {
                        options += `<option value="${s.id}" ${selectedValue == s.id ? 'selected' : ''}>${s.name} (${s.type})</option>`;
                    });
                    
                    const rowHtml = `
                    <div class="input-group mb-2 supplier-row">
                        <select class="form-select supplier-select" name="supplier_ids[]">
                            ${options}
                        </select>
                        <button class="btn btn-outline-danger" type="button" onclick="this.closest('.supplier-row').remove();"><i class="bi bi-trash"></i></button>
                    </div>`;
                    $('#suppliers-container').append(rowHtml);
                };

                window.addCategoryRow = function(selectedValue = '') {
                    const isSelected = (val) => selectedValue === val ? 'selected' : '';
                    const rowHtml = `
                    <div class="input-group mb-2 category-row">
                        <select class="form-select category-select" name="categories[]" onchange="handleCategoryChange()">
                            <option value="">Select a category</option>
                            <option value="Entry Tickets" ${isSelected("Entry Tickets")}>Entry Tickets</option>
                            <option value="Hotels" ${isSelected("Hotels")}>Hotels</option>
                            <option value="Transport" ${isSelected("Transport")}>Transport</option>
                            <option value="Airport Pickup" ${isSelected("Airport Pickup")}>Airport Pickup</option>
                            <option value="Airport Drop" ${isSelected("Airport Drop")}>Airport Drop</option>
                            <option value="Other Services" ${isSelected("Other Services")}>Other Services</option>
                        </select>
                        <button class="btn btn-outline-danger" type="button" onclick="this.closest('.category-row').remove(); handleCategoryChange();"><i class="bi bi-trash"></i></button>
                    </div>`;
                    $('#categories-container').append(rowHtml);
                };


                // Load existing data
                $.get('{{ route("admin.packages.show", $id) }}', function (pkg) {

                    // Load destinations and set selected value
                    loadDestinations(pkg.destination_id, pkg.destination_ids || []);
                    // Load suppliers and set selected value
                    loadSuppliers(pkg.supplier_ids || (pkg.supplier_id ? [pkg.supplier_id] : []));

                    $('#name').val(pkg.name);
                    $('#slug').val(pkg.slug);
                    $('#description').val(pkg.description || '');
                    if (descriptionEditor) descriptionEditor.root.innerHTML = pkg.description || '';
                    $('#short_description').val(pkg.short_description || '');
                    $('#net_price').val(pkg.net_price || '');
                    $('#markup_percentage').val(pkg.markup_percentage || 0);
                    $('#price').val(pkg.price);

                    // Trigger calculation to update amounts
                    calculateSellingPrice();

                    $('#discount_price').val(pkg.discount_price || '');
                    $('#price_2_6').val(pkg.price_2_6 || '');
                    $('#price_6_10').val(pkg.price_6_10 || '');
                    $('#currency').val(pkg.currency || 'INR');
                    $('#announcement_date').val(pkg.announcement_date ? pkg.announcement_date.split('T')[0] : '');
                    $('#total_pax').val(pkg.total_pax || '');

                    // Set category and amenities
                    const savedCategories = pkg.categories || (pkg.category ? [pkg.category] : []);
                    savedCategories.forEach(c => {
                        addCategoryRow(c);
                    });
                    if(savedCategories.length === 0) addCategoryRow('');
                    
                    $('#package_category').val(pkg.package_category || '');
                    // Set flight option
                    if (pkg.includes_flight) {
                        $('#includes_flight_yes').prop('checked', true);
                    } else {
                        $('#includes_flight_no').prop('checked', true);
                    }
                    $('#star_rating').val(pkg.star_rating || '');
                    $('#vehicle_type').val(pkg.vehicle_type || '');
                    $('#accommodation_type').val(pkg.accommodation_type || '');
                    $('#ticket_count').val(pkg.ticket_count || '');
                    $('#ticket_name').val(pkg.ticket_name || '');

                    // Load add-on amenities
                    if (pkg.addon_amenities && Array.isArray(pkg.addon_amenities)) {
                        pkg.addon_amenities.forEach(function (amenity) {
                            addAmenity(amenity);
                        });
                    }

                    // Show appropriate amenities section based on category
                    handleCategoryChange();

                    // Parse duration string to extract days and nights
                    if (pkg.duration) {
                        const durationStr = pkg.duration.toLowerCase();
                        const daysMatch = durationStr.match(/(\d+)\s*days?/);
                        const nightsMatch = durationStr.match(/(\d+)\s*nights?/);
                        if (daysMatch) $('#duration_days').val(daysMatch[1]);
                        if (nightsMatch) $('#duration_nights').val(nightsMatch[1]);
                    }

                    // Handle featured image
                    if (pkg.image) {
                        $('#image_path').val(pkg.image);
                        let imageUrl = pkg.image;
                        if (!imageUrl.startsWith('http')) {
                            imageUrl = imageUrl.startsWith('/') ? imageUrl : '/storage/' + imageUrl;
                        }
                        $('#image-preview').attr('src', imageUrl).show();
                        $('#image-preview-container').show();
                        $('#image-preview-container button').show();
                    }

                    // Load gallery images
                    if (pkg.gallery && Array.isArray(pkg.gallery) && pkg.gallery.length > 0) {
                        galleryImages = pkg.gallery;
                        pkg.gallery.forEach(function (imgPath) {
                            let imgUrl = imgPath;
                            if (!imgUrl.startsWith('http')) {
                                imgUrl = imgUrl.startsWith('/') ? imgUrl : '/storage/' + imgPath;
                            }
                            addGalleryImage(imgUrl, imgPath);
                        });
                        updateGalleryPaths();
                        $('#gallery-preview-container').show();
                    }

                    $('#is_featured').prop('checked', pkg.featured || false);
                    $('#is_active').prop('checked', pkg.status !== undefined ? pkg.status : true);
                    $('#meta_title').val(pkg.meta_title || '');
                    $('#meta_description').val(pkg.meta_description || '');
                    $('#meta_keywords').val(pkg.meta_keywords || '');
                }).fail(function () {
                    alert('Error loading package data');
                });

                // Destination Loading with cascading support
                function loadDestinations(selectedId = null, selectedIds = []) {
                    // Load all destinations for the multi-select first
                    $.get('{{ route("admin.destinations.index") }}', function (response) {
                        const allDestinations = response.data || response;
                        const multiSelect = $('#destination_ids');
                        multiSelect.empty();
                        if (Array.isArray(allDestinations)) {
                            allDestinations.forEach(function (dest) {
                                const selected = selectedIds.includes(dest.id) ? 'selected' : '';
                                multiSelect.append(`<option value="${dest.id}" ${selected}>${dest.city} (${dest.name}) - ${dest.country}</option>`);
                            });
                        }
                        // Initialize select2 if available
                        if ($.fn.select2) {
                            multiSelect.select2({
                                placeholder: "Select destinations",
                                allowClear: true,
                                width: '100%'
                            });
                        }
                    });

                    // Then load countries for the primary cascading select
                    $.get('{{ route("admin.destinations.countries") }}', function (countries) {
                        const countrySelect = $('#country_select');
                        countrySelect.find('option:not(:first)').remove();
                        countries.forEach(function (country) {
                            countrySelect.append(`<option value="${country}">${country}</option>`);
                        });

                        if (selectedId) {
                            // If we have a selectedId, we need to find its country and location first
                            $.get('{{ route("admin.destinations.show", "") }}/' + selectedId, function (dest) {
                                countrySelect.val(dest.country).trigger('change');
                                
                                // Wait for locations to load then set location
                                setTimeout(() => {
                                    $('#location_select').val(dest.location).trigger('change');
                                    
                                    // Wait for cities to load then set city
                                    setTimeout(() => {
                                        $('#destination_id').val(selectedId);
                                    }, 500);
                                }, 500);
                            });
                        }
                    });
                }

                $('#country_select').on('change', function () {
                    const country = $(this).val();
                    const locationSelect = $('#location_select');
                    const destinationSelect = $('#destination_id');

                    locationSelect.find('option:not(:first)').remove();
                    destinationSelect.find('option:not(:first)').remove();

                    if (country) {
                        $.get('{{ route("admin.destinations.locations") }}', { country: country }, function (locations) {
                            locations.forEach(function (location) {
                                locationSelect.append(`<option value="${location}">${location}</option>`);
                            });
                        });
                    }
                });

                $('#location_select').on('change', function () {
                    const country = $('#country_select').val();
                    const location = $(this).val();
                    const destinationSelect = $('#destination_id');

                    destinationSelect.find('option:not(:first)').remove();

                    if (location) {
                        $.get('{{ route("admin.destinations.cities") }}', { country: country, location: location }, function (cities) {
                            cities.forEach(function (city) {
                                destinationSelect.append(`<option value="${city.id}">${city.city} (${city.name})</option>`);
                            });
                        });
                    }
                });

                // Supplier Loading
                function loadSuppliers(selectedIds = []) {
                    $.get('{{ route("admin.suppliers.index") }}', function (data) {
                        const suppliers = data.data || data;
                        if (Array.isArray(suppliers)) {
                            cachedSuppliers = suppliers;
                            
                            if (selectedIds && selectedIds.length > 0) {
                                selectedIds.forEach(id => {
                                    addSupplierRow(id);
                                });
                            } else {
                                addSupplierRow('');
                            }
                        }
                    }).fail(function () {
                        console.error('Failed to load suppliers');
                    });
                }

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
                    // Create modal for image viewing
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
                const allSuppliers = @json($suppliers);

                window.addAmenity = function (data = {}) {
                    const container = $('#addon-amenities-container');
                    const amenityId = 'amenity-' + amenityCounter++;
                    
                    const supplierOptions = allSuppliers.map(s => `
                        <option value="${s.id}" data-type="${s.type}" ${data.supplier_id == s.id ? 'selected' : ''}>
                            ${s.name} (${s.type})
                        </option>
                    `).join('');

                    const amenityHtml = `
                        <div class="card mb-3 border-0 shadow-sm amenity-item" data-amenity-id="${amenityId}">
                            <div class="card-body p-3">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted mb-1">Supplier</label>
                                        <select class="form-select form-select-sm amenity-supplier" onchange="handleSupplierChange('${amenityId}', this)" required>
                                            <option value="">Select Supplier</option>
                                            ${supplierOptions}
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted mb-1 dynamic-asset-label">Item / Service Type</label>
                                        <select class="form-select form-select-sm amenity-asset" onchange="handleAssetChange('${amenityId}', this)" required ${!data.supplier_id ? 'disabled' : ''}>
                                            <option value="">Select Item</option>
                                            ${data.asset_id ? `<option value="${data.asset_id}" selected>Loading existing item...</option>` : ''}
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted mb-1">Name / Title</label>
                                        <input type="text" class="form-control form-control-sm amenity-name" value="${data.name || ''}" placeholder="Name" required>
                                        <input type="hidden" class="amenity-type" value="${data.type || ''}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small text-muted mb-1 text-end d-block">Component Total</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control text-end amenity-total-price fw-bold" value="${data.value || '0.00'}" step="0.01" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeAmenity('${amenityId}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row g-2 mt-2 calc-fields bg-light p-2 rounded" style="${data.supplier_id ? '' : 'display:none;'}">
                                    <!-- Dynamic fields will be shown here based on type -->
                                    <div class="col-md-2 calc-base-price">
                                        <label class="form-label small text-muted mb-1">Base Price</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-price" value="${data.price || '0.00'}" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    
                                    <!-- Hotel specific -->
                                    <div class="col-md-2 calc-days" style="${data.type === 'hotel' ? '' : 'display:none;'}">
                                        <label class="form-label small text-muted mb-1">Days/Nights</label>
                                        <input type="number" class="form-control form-control-sm amenity-days" value="${data.days || 1}" min="1" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    
                                    <!-- General Quantity -->
                                    <div class="col-md-2 calc-qty" style="${data.type && data.type !== 'activity' && data.type !== 'ticket' && data.type !== 'entry_tickets' ? '' : 'display:none;'}">
                                        <label class="form-label small text-muted mb-1">${data.type === 'hotel' ? 'Rooms Count' : (data.type === 'transport' ? 'Quantity/Hours' : 'Quantity')}</label>
                                        <input type="number" class="form-control form-control-sm amenity-qty" value="${data.quantity || 1}" min="1" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    
                                    <!-- Activity/Ticket specific -->
                                    <div class="col-md-2 calc-adult-price" style="${data.type === 'activity' || data.type === 'ticket' || data.type === 'entry_tickets' ? '' : 'display:none;'}">
                                        <label class="form-label small text-muted mb-1">Adult Price</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-adult-price" value="${data.adult_price || '0.00'}" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-adult-qty" style="${data.type === 'activity' || data.type === 'ticket' || data.type === 'entry_tickets' ? '' : 'display:none;'}">
                                        <label class="form-label small text-muted mb-1">Adult Count</label>
                                        <input type="number" class="form-control form-control-sm amenity-adult-qty" value="${data.adult_count || 1}" min="0" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-child-price" style="${data.type === 'activity' || data.type === 'ticket' || data.type === 'entry_tickets' ? '' : 'display:none;'}">
                                        <label class="form-label small text-muted mb-1">Child Price</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-child-price" value="${data.child_price || '0.00'}" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-child-qty" style="${data.type === 'activity' || data.type === 'ticket' || data.type === 'entry_tickets' ? '' : 'display:none;'}">
                                        <label class="form-label small text-muted mb-1">Child Count</label>
                                        <input type="number" class="form-control form-control-sm amenity-child-qty" value="${data.child_count || 0}" min="0" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(amenityHtml);

                    if (data.supplier_id) {
                        handleSupplierChange(amenityId, itemRowSelect(amenityId), data.asset_id);
                    }
                };

                const itemRowSelect = (id) => $(`.amenity-item[data-amenity-id="${id}"] .amenity-supplier`)[0];

                window.handleSupplierChange = function (amenityId, select, preSelectedAssetId = null) {
                    const supplierId = $(select).val();
                    const itemRow = $(`.amenity-item[data-amenity-id="${amenityId}"]`);
                    const assetSelect = itemRow.find('.amenity-asset');
                    const calcFieldsContainer = itemRow.find('.calc-fields');
                    const typeInput = itemRow.find('.amenity-type');
                    
                    if (!supplierId) {
                        assetSelect.html('<option value="">Select Item</option>').attr('disabled', true);
                        calcFieldsContainer.hide();
                        return;
                    }

                    const supplierType = $(select).find('option:selected').data('type').toLowerCase();
                    if (supplierType) typeInput.val(supplierType);
                    
                    calcFieldsContainer.show();
                    itemRow.find('.calc-base-price, .calc-days, .calc-qty, .calc-adult-price, .calc-adult-qty, .calc-child-price, .calc-child-qty').hide();
                    
                    let dynamicLabel = 'Item / Service Type';
                    
                    if (supplierType === 'hotel') {
                        dynamicLabel = 'Room Type';
                        itemRow.find('.calc-base-price').show();
                        itemRow.find('.calc-days').show();
                        itemRow.find('.calc-qty').show().find('label').text('Rooms Count');
                    } else if (supplierType === 'transport') {
                        dynamicLabel = 'Vehicle Type';
                        itemRow.find('.calc-base-price').show();
                        itemRow.find('.calc-qty').show().find('label').text('Quantity/Hours');
                    } else if (supplierType === 'activity' || supplierType === 'ticket' || supplierType === 'entry_tickets') {
                        dynamicLabel = 'Ticket / Activity';
                        itemRow.find('.calc-adult-price').show();
                        itemRow.find('.calc-adult-qty').show();
                        itemRow.find('.calc-child-price').show();
                        itemRow.find('.calc-child-qty').show();
                    } else {
                        itemRow.find('.calc-base-price').show();
                        itemRow.find('.calc-qty').show().find('label').text('Quantity');
                    }
                    
                    itemRow.find('.dynamic-asset-label').text(dynamicLabel);

                    // Load assets via API
                    if (!preSelectedAssetId) assetSelect.html('<option value="">Loading...</option>').attr('disabled', true);
                    
                    $.get(`/api/inventory/suppliers/${supplierId}/assets`, function (data) {
                        assetSelect.html('<option value="">Select Item</option>').removeAttr('disabled');
                        
                        if (data.type === 'hotel') {
                            data.assets.forEach(hotel => {
                                hotel.rooms.forEach(room => {
                                    const selected = preSelectedAssetId == room.id ? 'selected' : '';
                                    assetSelect.append(`<option value="${room.id}" ${selected} data-price="${room.base_price}" data-name="${hotel.name} - ${room.room_type}">${room.room_type} (${hotel.name}) - $${room.base_price}</option>`);
                                });
                            });
                        } else if (data.type === 'transport') {
                            data.assets.forEach(asset => {
                                const displayName = `${asset.vehicle_type} (${asset.name})`;
                                const selected = preSelectedAssetId == asset.id ? 'selected' : '';
                                assetSelect.append(`<option value="${asset.id}" ${selected} data-price="${asset.base_price || 0}" data-name="${displayName}">${displayName} - $${asset.base_price || 0}</option>`);
                            });
                        } else if (data.type === 'activity' || data.type === 'tickets' || data.type === 'entry_tickets') {
                            data.assets.forEach(asset => {
                                const name = asset.name || asset.attraction_name || 'Unnamed Item';
                                const adultPrice = asset.base_price || asset.adult_price || 0;
                                const childPrice = asset.child_price || 0;
                                const selected = preSelectedAssetId == asset.id ? 'selected' : '';
                                assetSelect.append(`<option value="${asset.id}" ${selected} data-adultprice="${adultPrice}" data-childprice="${childPrice}" data-name="${name}">${name} - Ad: $${adultPrice} / Ch: $${childPrice}</option>`);
                            });
                            if(data.extra_assets) {
                                data.extra_assets.forEach(asset => {
                                    const selected = preSelectedAssetId == asset.id ? 'selected' : '';
                                    assetSelect.append(`<option value="${asset.id}" ${selected} data-adultprice="${asset.adult_price || 0}" data-childprice="${asset.child_price || 0}" data-name="${asset.attraction_name}">${asset.attraction_name} - Ad: $${asset.adult_price || 0} / Ch: $${asset.child_price || 0}</option>`);
                                });
                            }
                        } else if (data.assets && Array.isArray(data.assets)) {
                            // Default handling for other types
                            data.assets.forEach(asset => {
                                const name = asset.name || asset.attraction_name || 'Unnamed Item';
                                const price = asset.base_price || asset.adult_price || asset.price || 0;
                                const selected = preSelectedAssetId == asset.id ? 'selected' : '';
                                assetSelect.append(`<option value="${asset.id}" ${selected} data-price="${price}" data-name="${name}">${name} - $${price}</option>`);
                            });
                        }
                    });
                };

                window.handleAssetChange = function (amenityId, select) {
                    const itemRow = $(`.amenity-item[data-amenity-id="${amenityId}"]`);
                    const selected = $(select).find('option:selected');
                    const name = selected.data('name');
                    
                    if (name) itemRow.find('.amenity-name').val(name);
                    
                    // Assign prices
                    const type = itemRow.find('.amenity-type').val();
                    if (type === 'activity' || type === 'ticket' || type === 'entry_tickets') {
                        const adPrice = selected.data('adultprice') || 0;
                        const chPrice = selected.data('childprice') || 0;
                        itemRow.find('.amenity-adult-price').val(adPrice);
                        itemRow.find('.amenity-child-price').val(chPrice);
                    } else {
                        const price = selected.data('price') || 0;
                        itemRow.find('.amenity-price').val(price);
                    }
                    
                    calcAmenityTotal(amenityId);
                };
                
                window.calcAmenityTotal = function(amenityId) {
                    const itemRow = $(`.amenity-item[data-amenity-id="${amenityId}"]`);
                    const type = itemRow.find('.amenity-type').val();
                    let total = 0;
                    
                    if (type === 'hotel') {
                        const price = parseFloat(itemRow.find('.amenity-price').val()) || 0;
                        const days = parseFloat(itemRow.find('.amenity-days').val()) || 1;
                        const qty = parseFloat(itemRow.find('.amenity-qty').val()) || 1;
                        total = price * days * qty;
                    } else if (type === 'activity' || type === 'ticket' || type === 'entry_tickets') {
                        const adPrice = parseFloat(itemRow.find('.amenity-adult-price').val()) || 0;
                        const adQty = parseFloat(itemRow.find('.amenity-adult-qty').val()) || 0;
                        const chPrice = parseFloat(itemRow.find('.amenity-child-price').val()) || 0;
                        const chQty = parseFloat(itemRow.find('.amenity-child-qty').val()) || 0;
                        total = (adPrice * adQty) + (chPrice * chQty);
                    } else {
                        const price = parseFloat(itemRow.find('.amenity-price').val()) || 0;
                        const qty = parseFloat(itemRow.find('.amenity-qty').val()) || 1;
                        total = price * qty;
                    }
                    
                    itemRow.find('.amenity-total-price').val(total.toFixed(2));
                    updateOverallPricing();
                };
                
                window.updateOverallPricing = function() {
                    let calcNetPrice = 0;
                    
                    // Compute from the new "Price Calculator" Components
                    $('.amenity-total-price').each(function() {
                        calcNetPrice += parseFloat($(this).val()) || 0;
                    });
                    
                    // Only update if we actually derived a number
                    if (calcNetPrice > 0) {
                        $('#net_price').val(calcNetPrice.toFixed(2)).trigger('input');
                    }
                }

                window.addAmenityWithData = function (type, name, value) {
                    addAmenity({ type, name, value });
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

                // Pricing Calculation Logic
                function calculateSellingPrice() {
                    const netPrice = parseFloat($('#net_price').val()) || 0;
                    const markupPct = parseFloat($('#markup_percentage').val()) || 0;

                    if (netPrice > 0) {
                        const markupAmount = (netPrice * markupPct) / 100;
                        const sellingPrice = netPrice + markupAmount;

                        $('#price').val(sellingPrice.toFixed(2));
                        $('#markup-amount-display').text('Amount: ' + markupAmount.toFixed(2));
                    }
                }

                $('#net_price, #markup_percentage').on('input', calculateSellingPrice);

                // Manual override of selling price adjusts markup ?? 
                // Currently keeping it simple: Net + Markup defines Price. 
                // If user edits Price directly, we could reverse calc, but simpler is unidirectional for now 
                // or just let them edit price and it acts as override. 
                // But for B2B/B2C consistency, let's keep it unidirectional: Net * Markup = Price.
                // If they edit price, maybe we should update markup? 
                // Let's implement Reverse Calculation if Price is edited manually
                $('#price').on('change', function () {
                    const sellingPrice = parseFloat($(this).val()) || 0;
                    const netPrice = parseFloat($('#net_price').val()) || 0;

                    if (netPrice > 0 && sellingPrice >= netPrice) {
                        const markupAmount = sellingPrice - netPrice;
                        const markupPct = (markupAmount / netPrice) * 100;
                        $('#markup_percentage').val(markupPct.toFixed(2));
                        $('#markup-amount-display').text('Amount: ' + markupAmount.toFixed(2));
                    }
                });


                $('#package-form').on('submit', function (e) {
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

                    // Get gallery paths
                    const galleryPaths = galleryImages.length > 0 ? galleryImages : null;

                    // Collect add-on amenities representing the package calculation
                    const addonAmenities = [];
                    $('.amenity-item').each(function () {
                        const type = $(this).find('.amenity-type').val();
                        const name = $(this).find('.amenity-name').val();
                        const supplier_id = $(this).find('.amenity-supplier').val();
                        const asset_id = $(this).find('.amenity-asset').val();
                        
                        const price = $(this).find('.amenity-price').val();
                        const adult_price = $(this).find('.amenity-adult-price').val();
                        const child_price = $(this).find('.amenity-child-price').val();
                        const quantity = $(this).find('.amenity-qty').val();
                        const days = $(this).find('.amenity-days').val();
                        const adult_count = $(this).find('.amenity-adult-qty').val();
                        const child_count = $(this).find('.amenity-child-qty').val();
                        const total = $(this).find('.amenity-total-price').val();

                        if (supplier_id && name) {
                            addonAmenities.push({
                                type: type || 'other',
                                name: name,
                                supplier_id: supplier_id || null,
                                asset_id: asset_id || null,
                                price: parseFloat(price) || 0,
                                adult_price: parseFloat(adult_price) || 0,
                                child_price: parseFloat(child_price) || 0,
                                quantity: parseFloat(quantity) || 0,
                                days: parseFloat(days) || 0,
                                adult_count: parseFloat(adult_count) || 0,
                                child_count: parseFloat(child_count) || 0,
                                value: total // Storing total in value fallback
                            });
                        }
                    });

                    const formData = {
                        destination_id: $('#destination_id').val() || null,
                        supplier_ids: $('.supplier-select').map(function() { return $(this).val(); }).get().filter(v => v),
                        categories: $('.category-select').map(function() { return $(this).val(); }).get().filter(v => v),
                        package_category: $('#package_category').val() || null,
                        includes_flight: $('input[name="includes_flight"]:checked').val() == '1' ? 1 : 0,
                        star_rating: $('#star_rating').val() ? parseInt($('#star_rating').val()) : null,
                        vehicle_type: $('#vehicle_type').val() || null,
                        accommodation_type: $('#accommodation_type').val() || null,
                        ticket_count: $('#ticket_count').val() ? parseInt($('#ticket_count').val()) : null,
                        ticket_name: $('#ticket_name').val() || null,
                        addon_amenities: addonAmenities.length > 0 ? addonAmenities : null,
                        name: $('#name').val(),
                        slug: $('#slug').val() || null,
                        description: $('#description').val(),
                        short_description: $('#short_description').val(),

                        net_price: $('#net_price').val() ? parseFloat($('#net_price').val()) : null,
                        markup_percentage: $('#markup_percentage').val() ? parseFloat($('#markup_percentage').val()) : 0,
                        markup_amount: (parseFloat($('#net_price').val() || 0) * (parseFloat($('#markup_percentage').val() || 0) / 100)),

                        price: parseFloat(priceValue),
                        discount_price: $('#discount_price').val() ? parseFloat($('#discount_price').val()) : null,
                        price_2_6: $('#price_2_6').val() ? parseFloat($('#price_2_6').val()) : null,
                        price_6_10: $('#price_6_10').val() ? parseFloat($('#price_6_10').val()) : null,
                        currency: $('#currency').val() || 'INR',
                        announcement_date: $('#announcement_date').val() || null,
                        total_pax: $('#total_pax').val() ? parseInt($('#total_pax').val()) : null,
                        duration_days: $('#duration_days').val() ? parseInt($('#duration_days').val()) : null,
                        duration_nights: $('#duration_nights').val() ? parseInt($('#duration_nights').val()) : null,
                        image: $('#image_path').val() || null,
                        gallery: galleryPaths,
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
                        success: function (response) {
                            alert('Package updated successfully!');
                            window.location.href = '{{ route("admin.packages.index") }}';
                        },
                        error: function (xhr) {
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