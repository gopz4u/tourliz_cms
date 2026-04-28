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
                                    <select class="form-select" id="location_select" name="location" disabled>
                                        <option value="">Select Location</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="destination_id" class="form-label"><i class="bi bi-pin-map"></i>
                                        Primary City</label>
                                    <select class="form-select" id="destination_id" name="destination_id" disabled>
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
                                    value="0" checked>
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
                            <input type="text" class="form-control" id="slug" name="slug"
                                placeholder="Auto-generated from name">
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

                        <!-- Real-time Multi-currency Preview -->
                        <div class="row mb-4" id="currency-preview-row" style="display: none;">
                            <div class="col-12">
                                <div class="bg-light p-3 rounded-3 border">
                                    <div class="d-flex gap-4 overflow-auto pb-1" id="multi-currency-previews">
                                        <!-- Will be populated by JS -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="net_price" class="form-label text-success"><i class="bi bi-tag-fill"></i>
                                        Net Price (Vendor)</label>
                                    <input type="number" step="0.01" class="form-control" id="net_price" name="net_price"
                                        placeholder="Auto-calculated from itinerary">
                                    <small class="text-muted">Auto-sum from itinerary days below</small>
                                    <div id="itinerary-cost-breakdown" class="mt-2" style="display:none;">
                                        <div class="card border-success">
                                            <div class="card-body p-2">
                                                <p class="mb-1 small fw-bold text-success"><i class="bi bi-calculator"></i> Itinerary Cost Breakdown</p>
                                                <div id="cost-breakdown-rows" class="small text-muted"></div>
                                                <hr class="my-1">
                                                <p class="mb-0 small fw-bold">Total: <span id="cost-breakdown-total" class="text-success">0.00</span></p>
                                            </div>
                                        </div>
                                    </div>
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
                            <div class="col-md-4">
                                <!-- Discount Price moved here to align -->
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
                                <img id="image-preview" class="image-preview" src="" alt="Preview">
                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImagePreview()">
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

                        <div id="gallery-preview-container" style="display:none;">
                            <label class="form-label">Gallery Preview</label>
                            <div id="gallery-grid" class="d-flex flex-wrap gap-2"
                                style="max-height: 300px; overflow-y: auto;">
                                <!-- Gallery images will be added here -->
                            </div>
                            <input type="hidden" id="gallery_paths" name="gallery_paths">
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

                <h5><i class="bi bi-calendar-range"></i> Day-by-Day Itinerary</h5>
                <p class="text-muted small">Select a Country first to load available destinations for your daily flow.</p>
                <div id="itinerary-days-container"></div>
                <div class="mb-4">
                    <button type="button" class="btn btn-outline-primary" onclick="addItineraryDay()">
                        <i class="bi bi-plus-circle"></i> Add Day
                    </button>
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

    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endpush

    @push('scripts')
        <!-- Quill Editor JS -->
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script>
                // Fetch exchange rates for real-time preview
                let cachedRates = [];
                $.get('/api/v1/currency/rates', function(response) {
                    if (response.success) {
                        cachedRates = response.rates;
                        updateCurrencyPreview();
                    }
                });

                function updateCurrencyPreview() {
                    const price = parseFloat($('#price').val()) || 0;
                    const currentCurrency = $('#currency').val();
                    const container = $('#multi-currency-previews');
                    
                    if (price <= 0 || cachedRates.length === 0) {
                        $('#currency-preview-row').hide();
                        return;
                    }

                    $('#currency-preview-row').show();
                    container.empty();

                    // Find rate for current currency
                    const currentRateObj = cachedRates.find(r => (r.code || r.currency_code) === currentCurrency) || { exchange_rate: 1 };
                    const currentRate = parseFloat(currentRateObj.exchange_rate || currentRateObj.rate_to_inr);
                    const priceInMYR = price * currentRate;

                    cachedRates.forEach(rate => {
                        const code = rate.code || rate.currency_code;
                        if (code === currentCurrency) return;
                        
                        const convertedPrice = priceInMYR / parseFloat(rate.exchange_rate || rate.rate_to_inr);
                        const symbol = getCurrencySymbol(code);
                        
                        container.append(`
                            <div class="flex-shrink-0">
                                <div class="text-uppercase text-muted" style="font-size: 10px; font-weight: 800;">${code}</div>
                                <div class="fw-bold text-dark">${symbol} ${convertedPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                            </div>
                        `);
                    });
                }

                function getCurrencySymbol(code) {
                    const symbols = { 'INR': '₹', 'USD': '$', 'MYR': 'RM', 'SGD': 'S$', 'AED': 'AED' };
                    return symbols[code] || code;
                }

                $('#price, #currency').on('input change', updateCurrencyPreview);

                // Initialize Quill editor
                descriptionEditor = initQuillEditor('#description-editor', 300);

                // Load countries for dropdown
                loadCountries();
                // Load suppliers for dropdown
                loadSuppliers();

                // Handle category change to show/hide amenities
                window.handleCategoryChange = function () {
                    const categories = [];
                    $('.category-select').each(function() {
                        const val = $(this).val();
                        if (val) categories.push(val);
                    });

                    $('.amenities-section').hide();
                    $('.amenities-section input, .amenities-section select').val('');

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
                let suppliersLoaded = false;
                window.addSupplierRow = function() {
                    const typeColors = { Hotel:'primary', Transport:'warning', Activity:'success', Agent:'info', Other:'secondary', Meal:'danger' };
                    let options = '<option value="">— Select vendor —</option>';
                    const byType = {};
                    cachedSuppliers.forEach(function(s) {
                        if (!byType[s.type]) byType[s.type] = [];
                        byType[s.type].push(s);
                    });
                    Object.keys(byType).sort().forEach(function(type) {
                        options += `<optgroup label="${type}">`;
                        byType[type].forEach(function(s) {
                            options += `<option value="${s.id}" data-type="${s.type}">${s.name}</option>`;
                        });
                        options += '</optgroup>';
                    });
                    
                    const rowHtml = `
                    <div class="input-group mb-2 supplier-row">
                        <select class="form-select supplier-select" name="supplier_ids[]">
                            ${options}
                        </select>
                        <button class="btn btn-outline-danger" type="button" onclick="this.closest('.supplier-row').remove();" title="Remove Vendor"><i class="bi bi-trash"></i></button>
                    </div>`;
                    $('#suppliers-container').append(rowHtml);
                };

                window.addCategoryRow = function() {
                    const rowHtml = `
                    <div class="input-group mb-2 category-row">
                        <select class="form-select category-select" name="categories[]" onchange="handleCategoryChange()">
                            <option value="">Select a category</option>
                            <option value="Entry Tickets">Entry Tickets</option>
                            <option value="Hotels">Hotels</option>
                            <option value="Transport">Transport</option>
                            <option value="Airport Pickup">Airport Pickup</option>
                            <option value="Airport Drop">Airport Drop</option>
                            <option value="Other Services">Other Services</option>
                        </select>
                        <button class="btn btn-outline-danger" type="button" onclick="this.closest('.category-row').remove(); handleCategoryChange();"><i class="bi bi-trash"></i></button>
                    </div>`;
                    $('#categories-container').append(rowHtml);
                };


                function loadCountries() {
                    // Load all destinations for the multi-select first
                    $.get('{{ route("admin.destinations.index") }}', function (response) {
                        const allDestinations = response.data || response;
                        const multiSelect = $('#destination_ids');
                        multiSelect.empty();
                        if (Array.isArray(allDestinations)) {
                            allDestinations.forEach(function (dest) {
                                multiSelect.append(`<option value="${dest.id}">${dest.city} (${dest.name}) - ${dest.country}</option>`);
                            });
                        }
                        // Initialize select2
                        if ($.fn.select2) {
                            multiSelect.select2({
                                placeholder: "Select destinations",
                                allowClear: true,
                                width: '100%'
                            });
                        }
                    });

                    $.get('{{ route("admin.destinations.countries") }}', function (countries) {
                        const countrySelect = $('#country_select');
                        countrySelect.find('option:not(:first)').remove();
                        countries.forEach(function (country) {
                            countrySelect.append(`<option value="${country}">${country}</option>`);
                        });
                    });
                }

                let currentCountryDestinations = [];

                $('#country_select').on('change', function () {
                    const country = $(this).val();
                    const locationSelect = $('#location_select');
                    const destinationSelect = $('#destination_id');

                    locationSelect.find('option:not(:first)').remove().prop('disabled', true);
                    destinationSelect.find('option:not(:first)').remove().prop('disabled', true);

                    if (country) {
                        $.get('{{ route("admin.destinations.locations") }}', { country: country }, function (locations) {
                            locations.forEach(function (location) {
                                locationSelect.append(`<option value="${location}">${location}</option>`);
                            });
                            locationSelect.prop('disabled', false);
                        });

                        // Fetch all destinations for this country for the Day-by-Day builder
                        $.get('{{ route("admin.destinations.index") }}', { country: country, per_page: 2000 }, function (res) {
                            currentCountryDestinations = res.data || [];
                            updateAllItineraryDestinations();
                        });

                        // Reload suppliers filtered by this country (via destination)
                        // First get destinations in this country to find IDs
                        loadSuppliers(); // Load all for country initially; refine on city select
                    } else {
                        currentCountryDestinations = [];
                        updateAllItineraryDestinations();
                        loadSuppliers(); // Reload all suppliers
                    }
                });

                $('#destination_id').on('change', function() {
                    const destId = $(this).val();
                    loadSuppliers(destId || null);
                });

                let itineraryDayCount = 0;

                window.addItineraryDay = function () {
                    itineraryDayCount++;
                    const dayId = 'day-' + itineraryDayCount;
                    
                    let optionsHtml = '';
                    currentCountryDestinations.forEach(dest => {
                        optionsHtml += `<option value="${dest.id}">${dest.city} - ${dest.name}</option>`;
                    });

                    const html = `
                    <div class="card mb-3 shadow-sm itinerary-day" id="${dayId}" style="border-left: 4px solid #5a52e5; border-radius: 14px;">
                        <div class="card-header d-flex justify-content-between align-items-center py-2" style="background: linear-gradient(135deg, #f0efff, #fff);">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:28px;height:28px;border-radius:8px;background:var(--primary);display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-calendar2-day text-white" style="font-size:0.75rem;"></i>
                                </div>
                                <h6 class="mb-0 fw-bold" style="color:var(--primary);">Day <span class="day-number">${itineraryDayCount}</span></h6>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItineraryDay('${dayId}')" style="border-radius:8px;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="card-body pt-3">

                            <div class="row g-2 mb-2">
                                <div class="col-md-12">
                                    <label class="form-label small fw-semibold">Day Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm day-title" placeholder="e.g. Arrival & Sightseeing" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-semibold">Destinations</label>
                                    <select class="form-select form-select-sm day-destinations" multiple="multiple">
                                        ${optionsHtml}
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Meals Included</label>
                                    <select class="form-select form-select-sm day-meals" multiple="multiple">
                                        <option value="Breakfast">Breakfast</option>
                                        <option value="Lunch">Lunch</option>
                                        <option value="Dinner">Dinner</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-semibold">Day Notes / Description</label>
                                    <textarea class="form-control form-control-sm day-description" rows="2" placeholder="What happens on this day?"></textarea>
                                </div>
                            </div>

                            <hr class="my-2">
                            <p class="small fw-semibold mb-2" style="color:var(--primary);"><i class="bi bi-link-45deg"></i> Linked Services (from Cost Components)</p>

                            <!-- HOTEL ROW -->
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-primary-subtle text-primary" style="border-radius:8px;font-size:0.7rem;width:70px;text-align:center;"><i class="bi bi-building"></i> Hotel</span>
                                <select class="form-select form-select-sm flex-grow-1 day-hotel" name="itinerary[${itineraryDayCount}][hotel_id]">
                                    <option value="">— Select hotel from components —</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-primary flex-shrink-0" title="Add a custom hotel not in the system" onclick="toggleCustomHotel(this)" style="border-radius:8px;white-space:nowrap;">
                                    <i class="bi bi-plus"></i> Custom
                                </button>
                            </div>
                            <div class="custom-hotel-row mb-2" style="display:none;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="bi bi-building-add"></i></span>
                                    <input type="text" class="form-control form-control-sm day-custom-hotel" placeholder="e.g. The Marriott Resort, Genting Highlands">
                                    <span class="input-group-text text-muted" style="font-size:0.7rem;">Custom</span>
                                </div>
                            </div>

                            <!-- TRANSPORT ROW -->
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-warning-subtle text-warning" style="border-radius:8px;font-size:0.7rem;width:70px;text-align:center;"><i class="bi bi-truck"></i> Transport</span>
                                <select class="form-select form-select-sm flex-grow-1 day-transport" name="itinerary[${itineraryDayCount}][transport_id]">
                                    <option value="">— Select transport from components —</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-warning flex-shrink-0" title="Add a custom transport" onclick="toggleCustomTransport(this)" style="border-radius:8px;white-space:nowrap;">
                                    <i class="bi bi-plus"></i> Custom
                                </button>
                            </div>
                            <div class="custom-transport-row mb-2" style="display:none;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="bi bi-car-front"></i></span>
                                    <input type="text" class="form-control form-control-sm day-custom-transport" placeholder="e.g. Private Van Transfer, Airport Grab">
                                    <span class="input-group-text text-muted" style="font-size:0.7rem;">Custom</span>
                                </div>
                            </div>

                            <!-- ACTIVITIES ROW -->
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success-subtle text-success" style="border-radius:8px;font-size:0.7rem;width:70px;text-align:center;"><i class="bi bi-lightning"></i> Activities</span>
                                <select class="form-select form-select-sm flex-grow-1 day-activities" name="itinerary[${itineraryDayCount}][activity_ids][]" multiple="multiple">
                                </select>
                            </div>

                        </div>
                    </div>`;

                    
                    $('#itinerary-days-container').append(html);
                    
                    $(`#${dayId} .day-destinations`).select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Search and select destinations...',
                        allowClear: true
                    });
                    
                    $(`#${dayId} .day-activities`).select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Select activities / other vendors...',
                        allowClear: true
                    });
                    
                    $(`#${dayId} .day-meals`).select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Select meals...',
                        width: '100%'
                    });
                    
                    updateDayVendorDropdowns();
                };

                window.toggleCustomHotel = function(btn) {
                    const row = $(btn).closest('.card-body').find('.custom-hotel-row');
                    row.toggle();
                    if (row.is(':visible')) {
                        row.find('input').focus();
                        $(btn).addClass('btn-primary').removeClass('btn-outline-primary').html('<i class="bi bi-x"></i> Cancel');
                    } else {
                        row.find('input').val('');
                        $(btn).removeClass('btn-primary').addClass('btn-outline-primary').html('<i class="bi bi-plus"></i> Custom');
                    }
                };

                window.toggleCustomTransport = function(btn) {
                    const row = $(btn).closest('.card-body').find('.custom-transport-row');
                    row.toggle();
                    if (row.is(':visible')) {
                        row.find('input').focus();
                        $(btn).addClass('btn-warning').removeClass('btn-outline-warning').html('<i class="bi bi-x"></i> Cancel');
                    } else {
                        row.find('input').val('');
                        $(btn).removeClass('btn-warning').addClass('btn-outline-warning').html('<i class="bi bi-plus"></i> Custom');
                    }
                };

                window.removeItineraryDay = function (dayId) {
                    $(`#${dayId}`).remove();
                    reindexDays();
                    updateDayVendorDropdowns();
                };

                // Recalculate total vendor cost from all days
                window.recalculateVendorCost = function () {
                    let grandTotal = 0;
                    const breakdown = [];
                    $('.itinerary-day').each(function(index) {
                        const dayNum = index + 1;
                        breakdown.push({ day: dayNum, total: 0, parts: [] });
                    });
                    // Day costs are now handled by Cost Components exclusively.
                    
                    $('#itinerary_breakdown').val(JSON.stringify(breakdown));
                };
                
                window.updateDayVendorDropdowns = function() {
                    const hotels = [];
                    const transports = [];
                    const others = [];
                    
                    $('.amenity-item').each(function() {
                        const type = $(this).find('.amenity-type').val();
                        const name = $(this).find('.amenity-name').val();
                        const assetId = $(this).find('.amenity-asset').val();
                        // use combination of type+asset ID as unique value
                        if(name && assetId) {
                            const val = type + '_' + assetId;
                            const text = name;
                            if(type === 'hotel') hotels.push({val, text});
                            else if(type === 'transport') transports.push({val, text});
                            else others.push({val, text});
                        }
                    });
                    
                    $('.itinerary-day').each(function() {
                        const dayHotel = $(this).find('.day-hotel');
                        const dayTrans = $(this).find('.day-transport');
                        const dayActs = $(this).find('.day-activities');
                        
                        const curHotel = dayHotel.val();
                        const curTrans = dayTrans.val();
                        const curActs = dayActs.val() || [];
                        
                        dayHotel.html('<option value="">None</option>');
                        hotels.forEach(h => dayHotel.append(`<option value="${h.val}">${h.text}</option>`));
                        dayHotel.val(curHotel);
                        
                        dayTrans.html('<option value="">None</option>');
                        transports.forEach(t => dayTrans.append(`<option value="${t.val}">${t.text}</option>`));
                        dayTrans.val(curTrans);
                        
                        dayActs.empty();
                        others.forEach(o => dayActs.append(`<option value="${o.val}">${o.text}</option>`));
                        dayActs.val(curActs).trigger('change');
                    });
                };

                function updateAllItineraryDestinations() {
                    $('.day-destinations').each(function() {
                        let selected = $(this).val() || [];
                        $(this).empty();
                        
                        currentCountryDestinations.forEach(dest => {
                            let isSelected = selected.includes(dest.id.toString()) ? 'selected' : '';
                            $(this).append(`<option value="${dest.id}" ${isSelected}>${dest.city} - ${dest.name}</option>`);
                        });
                        
                        // Resync select2
                        if($(this).hasClass('select2-hidden-accessible')) {
                            $(this).trigger('change');
                        }
                    });
                }

                function reindexDays() {
                    itineraryDayCount = 0;
                    $('.itinerary-day').each(function() {
                        itineraryDayCount++;
                        $(this).find('.day-number').text(itineraryDayCount);
                    });
                }

                $('#location_select').on('change', function () {
                    const country = $('#country_select').val();
                    const location = $(this).val();
                    const destinationSelect = $('#destination_id');

                    destinationSelect.find('option:not(:first)').remove().prop('disabled', true);

                    if (location) {
                        $.get('{{ route("admin.destinations.cities") }}', { country: country, location: location }, function (cities) {
                            cities.forEach(function (city) {
                                destinationSelect.append(`<option value="${city.id}">${city.city} (${city.name})</option>`);
                            });
                            destinationSelect.prop('disabled', false);
                        });
                    }
                });

                function loadSuppliers(destinationId = null) {
                    const params = {};
                    if (destinationId) params.destination_id = destinationId;
                    
                    $.get('{{ route("admin.suppliers.index") }}', params, function (data) {
                        const suppliers = data.data || data;
                        if (Array.isArray(suppliers)) {
                            cachedSuppliers = suppliers;
                            if (!suppliersLoaded) {
                                suppliersLoaded = true;
                                addSupplierRow(); // Only add the first row once
                            } else {
                                // Refresh all existing supplier dropdowns
                                $('.supplier-select').each(function() {
                                    const curVal = $(this).val();
                                    $(this).find('optgroup, option:not(:first)').remove();
                                    const byType = {};
                                    cachedSuppliers.forEach(function(s) {
                                        if (!byType[s.type]) byType[s.type] = [];
                                        byType[s.type].push(s);
                                    });
                                    Object.keys(byType).sort().forEach(function(type) {
                                        let grp = `<optgroup label="${type}">`;
                                        byType[type].forEach(function(s) {
                                            grp += `<option value="${s.id}" data-type="${s.type}">${s.name}</option>`;
                                        });
                                        grp += '</optgroup>';
                                        $(this).append(grp);
                                    }.bind(this));
                                    $(this).val(curVal);
                                });
                            }
                        }
                    }).fail(function () {
                        console.error('Failed to load suppliers');
                    });
                }
                
                // Add an initial category row
                addCategoryRow();

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

                // Gallery images array
                let galleryImages = [];

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

                // Update gallery paths hidden input
                function updateGalleryPaths() {
                    $('#gallery_paths').val(JSON.stringify(galleryImages));
                }

                // Add-on Amenities Management
                let amenityCounter = 0;
                const allSuppliers = @json($suppliers);

                window.addAmenity = function () {
                    const container = $('#addon-amenities-container');
                    const amenityId = 'amenity-' + amenityCounter++;
                    
                    const supplierOptions = allSuppliers.map(s => `<option value="${s.id}" data-type="${s.type}">${s.name} (${s.type})</option>`).join('');

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
                                        <select class="form-select form-select-sm amenity-asset" onchange="handleAssetChange('${amenityId}', this)" required disabled>
                                            <option value="">Select Item</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted mb-1">Name / Title</label>
                                        <input type="text" class="form-control form-control-sm amenity-name" placeholder="Name" required>
                                        <input type="hidden" class="amenity-type">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small text-muted mb-1 text-end d-block">Component Total</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control text-end amenity-total-price fw-bold" value="0.00" step="0.01" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeAmenity('${amenityId}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row g-2 mt-2 calc-fields bg-light p-2 rounded" style="display:none;">
                                    <!-- Dynamic fields will be shown here based on type -->
                                    <div class="col-md-2 calc-base-price">
                                        <label class="form-label small text-muted mb-1">Base Price</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-price" value="0.00" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    
                                    <!-- Hotel specific -->
                                    <div class="col-md-2 calc-days" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Days/Nights</label>
                                        <input type="number" class="form-control form-control-sm amenity-days" value="1" min="1" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    
                                    <!-- General Quantity -->
                                    <div class="col-md-2 calc-qty" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Quantity/Hours</label>
                                        <input type="number" class="form-control form-control-sm amenity-qty" value="1" min="1" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    
                                    <!-- Activity/Ticket specific -->
                                    <div class="col-md-2 calc-adult-price" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Adult Price</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-adult-price" value="0.00" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-adult-qty" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Adult Count</label>
                                        <input type="number" class="form-control form-control-sm amenity-adult-qty" value="1" min="0" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-child-price" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Child Price</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-child-price" value="0.00" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-child-qty" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Child Count</label>
                                        <input type="number" class="form-control form-control-sm amenity-child-qty" value="0" min="0" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(amenityHtml);
                };

                window.handleSupplierChange = function (amenityId, select) {
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
                    typeInput.val(supplierType);
                    
                    // Setup UI based on type
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
                    assetSelect.html('<option value="">Loading...</option>').attr('disabled', true);
                    
                    $.get(`/api/inventory/suppliers/${supplierId}/assets`, function (data) {
                        assetSelect.html('<option value="">Select Item</option>').removeAttr('disabled');
                        
                        if (data.type === 'hotel') {
                            data.assets.forEach(hotel => {
                                hotel.rooms.forEach(room => {
                                    assetSelect.append(`<option value="${room.id}" data-price="${room.base_price}" data-name="${hotel.name} - ${room.room_type}">${room.room_type} (${hotel.name}) - $${room.base_price}</option>`);
                                });
                            });
                        } else if (data.type === 'transport') {
                            data.assets.forEach(asset => {
                                const displayName = `${asset.vehicle_type} (${asset.name})`;
                                assetSelect.append(`<option value="${asset.id}" data-price="${asset.base_price || 0}" data-name="${displayName}">${displayName} - $${asset.base_price || 0}</option>`);
                            });
                        } else if (data.type === 'activity' || data.type === 'tickets' || data.type === 'entry_tickets') {
                            data.assets.forEach(asset => {
                                const name = asset.name || asset.attraction_name || 'Unnamed Item';
                                const adultPrice = asset.base_price || asset.adult_price || 0;
                                const childPrice = asset.child_price || 0;
                                assetSelect.append(`<option value="${asset.id}" data-adultprice="${adultPrice}" data-childprice="${childPrice}" data-name="${name}">${name} - Ad: $${adultPrice} / Ch: $${childPrice}</option>`);
                            });
                            if(data.extra_assets) {
                                data.extra_assets.forEach(asset => {
                                    assetSelect.append(`<option value="${asset.id}" data-adultprice="${asset.adult_price || 0}" data-childprice="${asset.child_price || 0}" data-name="${asset.attraction_name}">${asset.attraction_name} - Ad: $${asset.adult_price || 0} / Ch: $${asset.child_price || 0}</option>`);
                                });
                            }
                        } else if (data.assets && Array.isArray(data.assets)) {
                            // Default handling for other types
                            data.assets.forEach(asset => {
                                const name = asset.name || asset.attraction_name || 'Unnamed Item';
                                const price = asset.base_price || asset.price || asset.adult_price || 0;
                                assetSelect.append(`<option value="${asset.id}" data-price="${price}" data-name="${name}">${name} - $${price}</option>`);
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
                    updateDayVendorDropdowns();
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

                window.removeAmenity = function (amenityId) {
                    $(`.amenity-item[data-amenity-id="${amenityId}"]`).remove();
                    updateOverallPricing();
                    updateDayVendorDropdowns();
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

                // Reverse Calculation if Price is edited manually
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

                    const itineraryArr = [];
                    $('.itinerary-day').each(function() {
                        const dayCard = $(this);
                        const customHotel = dayCard.find('.day-custom-hotel').val() || '';
                        const customTransport = dayCard.find('.day-custom-transport').val() || '';
                        itineraryArr.push({
                            day: parseInt(dayCard.find('.day-number').text()),
                            title: dayCard.find('.day-title').val(),
                            destinations: dayCard.find('.day-destinations').val() || [],
                            hotel: dayCard.find('.day-hotel').val() || null,
                            custom_hotel: customHotel,
                            transport: dayCard.find('.day-transport').val() || null,
                            custom_transport: customTransport,
                            activities: dayCard.find('.day-activities').val() || [],
                            meals: dayCard.find('.day-meals').val() || [],
                            description: dayCard.find('.day-description').val(),
                        });
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
                        itinerary: itineraryArr.length > 0 ? itineraryArr : null,
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
                        success: function (response) {
                            // Redirect to the Manage Itinerary editor for this package
                            window.location.href = '/admin/itineraries/' + response.id + '/edit';
                        },
                        error: function (xhr) {
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
        </script>
    @endpush
@endsection