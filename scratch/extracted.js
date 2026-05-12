
            $(document).ready(function() {
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
                if (typeof initQuillEditor === 'function') {
                    window.descriptionEditor = initQuillEditor('#description-editor', 300);
                }

                // Initialize Select2 on cascading selects immediately with placeholders
                if ($.fn.select2) {
                    // Select2 for country will be initialized inside loadCountries
                    $('#location_select').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Select Location',
                        width: '100%',
                        allowClear: true
                    });
                    $('#destination_id').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Select City',
                        width: '100%',
                        allowClear: true
                    });
                }

                loadCountries();
                loadSuppliers();

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
                    const countrySelect = $('#country_select');
                    // Add a temporary loading state
                    countrySelect.html('<option value="">Loading countries...</option>').attr('disabled', true);
                    
                    if ($.fn.select2 && countrySelect.data('select2')) {
                        countrySelect.trigger('change.select2');
                    }

                    // Load countries for the primary cascading select
                    $.ajax({
                        url: '"placeholder"',
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            const countries = response.data || response;
                            countrySelect.empty().append('<option value="">Select Country</option>');
                            
                            if (Array.isArray(countries)) {
                                countries.forEach(function (country) {
                                    countrySelect.append(`<option value="${country}">${country}</option>`);
                                });
                            }
                            
                            countrySelect.attr('disabled', false);
                            
                            // Initialize or Resync select2
                            if ($.fn.select2) {
                                if (countrySelect.data('select2')) {
                                    countrySelect.trigger('change');
                                } else {
                                    countrySelect.select2({
                                        theme: 'bootstrap-5',
                                        placeholder: 'Select Country',
                                        width: '100%',
                                        allowClear: true
                                    });
                                }
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Failed to load countries:', error);
                            countrySelect.html('<option value="">Error loading countries</option>');
                        }
                    });

                    // Load all destinations for the multi-select
                    $.ajax({
                        url: '"placeholder"',
                        type: 'GET',
                        data: { per_page: 2000 },
                        dataType: 'json',
                        success: function (response) {
                            const destinations = response.data || [];
                            const multiSelect = $('#destination_ids');
                            multiSelect.empty();
                            if (Array.isArray(destinations)) {
                                destinations.forEach(function (dest) {
                                    multiSelect.append(`<option value="${dest.id}">${dest.city} (${dest.name}) - ${dest.country}</option>`);
                                });
                            }
                            // Initialize select2
                            if ($.fn.select2) {
                                multiSelect.select2({
                                    theme: 'bootstrap-5',
                                    placeholder: "Select destinations",
                                    allowClear: true,
                                    width: '100%'
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Failed to load destinations:', error);
                        }
                    });
                }

                let currentCountryDestinations = [];

                $('#country_select').on('change', function () {
                    const country = $(this).val();
                    const locationSelect = $('#location_select');
                    const destinationSelect = $('#destination_id');

                    locationSelect.find('option:not(:first)').remove();
                    destinationSelect.find('option:not(:first)').remove();
                    
                    if ($.fn.select2) {
                        locationSelect.prop('disabled', true).trigger('change');
                        destinationSelect.prop('disabled', true).trigger('change');
                    } else {
                        locationSelect.prop('disabled', true);
                        destinationSelect.prop('disabled', true);
                    }

                    if (country) {
                        $.ajax({
                            url: '"placeholder"',
                            type: 'GET',
                            data: { country: country },
                            dataType: 'json',
                            success: function (locations) {
                                if (Array.isArray(locations)) {
                                    locations.forEach(function (location) {
                                        locationSelect.append(`<option value="${location}">${location}</option>`);
                                    });
                                }
                                locationSelect.prop('disabled', false);
                                if ($.fn.select2) {
                                    locationSelect.trigger('change.select2');
                                }
                            }
                        });

                        // Fetch all destinations for this country for the Day-by-Day builder
                        $.ajax({
                            url: '"placeholder"',
                            type: 'GET',
                            data: { country: country, per_page: 2000 },
                            dataType: 'json',
                            success: function (res) {
                                currentCountryDestinations = res.data || [];
                                updateAllItineraryDestinations();
                            }
                        });

                        loadSuppliers();
                    } else {
                        currentCountryDestinations = [];
                        updateAllItineraryDestinations();
                        loadSuppliers();
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

                    destinationSelect.find('option:not(:first)').remove();
                    if ($.fn.select2) {
                        destinationSelect.prop('disabled', true).trigger('change');
                    } else {
                        destinationSelect.prop('disabled', true);
                    }

                    if (location) {
                        $.ajax({
                            url: '"placeholder"',
                            type: 'GET',
                            data: { country: country, location: location },
                            dataType: 'json',
                            success: function (cities) {
                                if (Array.isArray(cities)) {
                                    cities.forEach(function (city) {
                                        destinationSelect.append(`<option value="${city.id}">${city.city} (${city.name})</option>`);
                                    });
                                }
                                destinationSelect.prop('disabled', false);
                                if ($.fn.select2) {
                                    destinationSelect.trigger('change.select2');
                                }
                            }
                        });
                    }
                });

                function loadSuppliers(destinationId = null) {
                    const params = {};
                    if (destinationId) params.destination_id = destinationId;
                    
                    $.get('"placeholder"', params, function (data) {
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
                            url: '"placeholder"',
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
                const allSuppliers = {};

                window.addAmenity = function () {
                    const container = $('#addon-amenities-container');
                    const amenityId = 'amenity-' + amenityCounter++;
                    
                    const supplierOptions = allSuppliers.map(s => `<option value="${s.id}" data-type="${s.type}">${s.name} (${s.type})</option>`).join('');

                    const amenityHtml = `
                        <div class="card mb-3 border-0 shadow-sm amenity-item" data-amenity-id="${amenityId}">
                            <div class="card-body p-3">
                                <div class="row g-2 align-items-end">
                                    <div class="col">
                                        <label class="form-label small text-muted mb-1">Supplier</label>
                                        <select class="form-select form-select-sm amenity-supplier" onchange="handleSupplierChange('${amenityId}', this)" required>
                                            <option value="">Select Supplier</option>
                                            ${supplierOptions}
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label class="form-label small text-muted mb-1">Pricing Logic</label>
                                        <select class="form-select form-select-sm amenity-logic" onchange="handleLogicChange('${amenityId}', this)">
                                            <option value="fixed">Fixed Cost (Total)</option>
                                            <option value="per_pax">Variable (Per Pax)</option>
                                            <option value="sharing">Sharing (Hotel)</option>
                                            <option value="tiered_transport">Tiered Transport (Pax Sensitive)</option>
                                        </select>
                                    </div>
                                    <div class="col amenity-hotel-container" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Hotel</label>
                                        <select class="form-select form-select-sm amenity-hotel" onchange="handleHotelChange('${amenityId}', this)">
                                            <option value="">Select Hotel</option>
                                        </select>
                                    </div>
                                    <div class="col amenity-asset-container">
                                        <label class="form-label small text-muted mb-1 dynamic-asset-label">Item / Service Type</label>
                                        <select class="form-select form-select-sm amenity-asset" onchange="handleAssetChange('${amenityId}', this)" required disabled>
                                            <option value="">Select Item</option>
                                        </select>
                                    </div>
                                    <div class="col">
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
                                    <!-- Common Fields -->
                                    <div class="col-md-2 calc-base-price">
                                        <label class="form-label small text-muted mb-1">Base Price</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-price" value="0.00" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-1 calc-days" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Days</label>
                                        <input type="number" class="form-control form-control-sm amenity-days" value="1" min="1" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-1 calc-qty" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Qty</label>
                                        <input type="number" class="form-control form-control-sm amenity-qty" value="1" min="1" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>

                                    <!-- Variable Pricing Fields -->
                                    <div class="col-md-2 calc-adult-price" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Adult Rate</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-adult-price" value="0.00" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-child-price" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Child Rate</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-child-price" value="0.00" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>

                                    <!-- Sharing Based Fields (Hotel) -->
                                    <div class="col-md-2 calc-sharing-double" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Double Sharing</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-double-price" value="0.00" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-sharing-single" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Single Supp.</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-single-price" value="0.00" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-sharing-triple" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Triple Sharing</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-triple-price" value="0.00" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-sharing-extrabed" style="display:none;">
                                        <label class="form-label small text-muted mb-1">Extra Bed</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-extrabed-price" value="0.00" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>

                                    <!-- Tiered Transport Fields -->
                                    <div class="col-12 calc-tiered-transport" style="display:none;">
                                        <div class="p-2 border rounded bg-white mt-1">
                                            <label class="form-label small fw-bold mb-1">Available Vehicles (Cheapest fit will be used)</label>
                                            <div class="tiered-vehicles-list d-flex flex-wrap gap-2">
                                                <!-- Dynamic vehicle checkboxes -->
                                            </div>
                                        </div>
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
                    const hotelContainer = itemRow.find('.amenity-hotel-container');
                    const hotelSelect = itemRow.find('.amenity-hotel');
                    const calcFieldsContainer = itemRow.find('.calc-fields');
                    const typeInput = itemRow.find('.amenity-type');
                    
                    if (!supplierId) {
                        assetSelect.html('<option value="">Select Item</option>').attr('disabled', true);
                        hotelContainer.hide();
                        calcFieldsContainer.hide();
                        return;
                    }

                    const rawType = ($(select).find('option:selected').attr('data-type') || '').toLowerCase();
                    let supplierType = rawType;
                    
                    if (rawType.includes('hotel') || rawType.includes('accommodation')) {
                        supplierType = 'hotel';
                    } else if (rawType.includes('transport')) {
                        supplierType = 'transport';
                    } else if (rawType.includes('activity') || rawType.includes('ticket') || rawType.includes('entry_tickets')) {
                        supplierType = 'activity';
                    }
                    
                    typeInput.val(supplierType);
                    
                    // Default logic based on type
                    const logicSelect = itemRow.find('.amenity-logic');
                    if (supplierType === 'hotel') {
                        logicSelect.val('sharing');
                    } else if (supplierType === 'transport') {
                        logicSelect.val('fixed');
                    } else {
                        logicSelect.val('per_pax');
                    }
                    
                    handleLogicChange(amenityId, logicSelect[0]);

                    // Load assets via API
                    assetSelect.html('<option value="">Loading...</option>').attr('disabled', true);
                    
                    $.get(`/api/inventory/suppliers/${supplierId}/assets`, function (data) {
                        itemRow.data('inventory', data);
                        
                        if (data.type === 'hotel') {
                            hotelSelect.html('<option value="">Select Hotel</option>').removeAttr('disabled');
                            assetSelect.html('<option value="">Select Room Type</option>').attr('disabled', true);
                            
                            let hotelList = Array.isArray(data.assets) ? data.assets : Object.values(data.assets);
                            
                            if (hotelList.length > 0) {
                                hotelList.forEach(hotel => {
                                    hotelSelect.append(`<option value="${hotel.id}">${hotel.name}</option>`);
                                });
                                
                                // Auto-select if only one hotel
                                if (hotelList.length === 1) {
                                    hotelSelect.val(hotelList[0].id).trigger('change');
                                    handleHotelChange(amenityId, hotelSelect[0]);
                                }
                            } else {
                                hotelSelect.html('<option value="">No Hotels Linked to this Supplier</option>').attr('disabled', true);
                            }
                        } else {
                            assetSelect.html('<option value="">Select Item</option>').removeAttr('disabled');
                            
                            let assetList = Array.isArray(data.assets) ? data.assets : Object.values(data.assets);
                            
                            if (data.type === 'transport') {
                                assetList.forEach(asset => {
                                    const displayName = `${asset.vehicle_type} (${asset.name})`;
                                    assetSelect.append(`<option value="${asset.id}" data-price="${asset.base_price || 0}" data-name="${displayName}">${displayName} - $${asset.base_price || 0}</option>`);
                                });
                            } else if (data.type === 'activity') {
                                assetList.forEach(asset => {
                                    const name = asset.name || asset.attraction_name || 'Unnamed Item';
                                    const adultPrice = asset.base_price || asset.adult_price || 0;
                                    const childPrice = asset.child_price || 0;
                                    assetSelect.append(`<option value="${asset.id}" data-adultprice="${adultPrice}" data-childprice="${childPrice}" data-name="${name}">${name} - Ad: $${adultPrice} / Ch: $${childPrice}</option>`);
                                });
                                if(data.extra_assets) {
                                    let extraList = Array.isArray(data.extra_assets) ? data.extra_assets : Object.values(data.extra_assets);
                                    extraList.forEach(asset => {
                                        assetSelect.append(`<option value="${asset.id}" data-adultprice="${asset.adult_price || 0}" data-childprice="${asset.child_price || 0}" data-name="${asset.attraction_name}">${asset.attraction_name} - Ad: $${asset.adult_price || 0} / Ch: $${asset.child_price || 0}</option>`);
                                    });
                                }
                            } else if (assetList.length > 0) {
                                assetList.forEach(asset => {
                                    const name = asset.name || asset.attraction_name || 'Unnamed Item';
                                    const price = asset.base_price || asset.price || asset.adult_price || 0;
                                    assetSelect.append(`<option value="${asset.id}" data-price="${price}" data-name="${name}">${name} - $${price}</option>`);
                                    });
                                }
                            }
                            
                            // Also populate tiered transport list
                            const tieredList = itemRow.find('.tiered-vehicles-list');
                            tieredList.empty();
                            if (data.type === 'transport') {
                                assetList.forEach(asset => {
                                    const label = `${asset.vehicle_type} (${asset.capacity} Pax) - $${asset.base_price}`;
                                    tieredList.append(`
                                        <div class="form-check form-check-inline small">
                                            <input class="form-check-input vehicle-tier-check" type="checkbox" 
                                                value="${asset.id}" 
                                                data-capacity="${asset.capacity}" 
                                                data-price="${asset.base_price}"
                                                onchange="calcAmenityTotal('${amenityId}')">
                                            <label class="form-check-label">${label}</label>
                                        </div>
                                    `);
                                });
                            }
                        });
                };

                window.handleLogicChange = function (amenityId, select) {
                    const logic = $(select).val();
                    const itemRow = $(`.amenity-item[data-amenity-id="${amenityId}"]`);
                    const type = itemRow.find('.amenity-type').val();
                    const hotelContainer = itemRow.find('.amenity-hotel-container');
                    const calcFields = itemRow.find('.calc-fields');
                    
                    calcFields.show();
                    // Hide all first
                    itemRow.find('.calc-base-price, .calc-days, .calc-qty, .calc-adult-price, .calc-child-price, .calc-sharing-double, .calc-sharing-single, .calc-sharing-triple, .calc-sharing-extrabed, .calc-tiered-transport').hide();

                    if (type === 'hotel') hotelContainer.show();
                    else hotelContainer.hide();

                    if (logic === 'fixed') {
                        itemRow.find('.calc-base-price, .calc-days, .calc-qty').show();
                    } else if (logic === 'per_pax') {
                        itemRow.find('.calc-adult-price, .calc-child-price').show();
                    } else if (logic === 'sharing') {
                        itemRow.find('.calc-days, .calc-sharing-double, .calc-sharing-single, .calc-sharing-triple, .calc-sharing-extrabed').show();
                    } else if (logic === 'tiered_transport') {
                        itemRow.find('.calc-days, .calc-tiered-transport').show();
                    }
                    
                    calcAmenityTotal(amenityId);
                };

                window.handleHotelChange = function (amenityId, select) {
                    const itemRow = $(`.amenity-item[data-amenity-id="${amenityId}"]`);
                    const assetSelect = itemRow.find('.amenity-asset');
                    const hotelId = $(select).val();
                    const data = itemRow.data('inventory');
                    
                    assetSelect.html('<option value="">Select Room Type</option>');
                    if (!hotelId || !data || data.type !== 'hotel') {
                        assetSelect.attr('disabled', true);
                        return;
                    }
                    
                    assetSelect.removeAttr('disabled');
                    let hotelList = Array.isArray(data.assets) ? data.assets : Object.values(data.assets);
                    const hotel = hotelList.find(h => h.id == hotelId);
                    
                    if (hotel && hotel.rooms) {
                        let roomList = Array.isArray(hotel.rooms) ? hotel.rooms : Object.values(hotel.rooms);
                        if (roomList.length > 0) {
                            roomList.forEach(room => {
                                assetSelect.append(`<option value="${room.id}" data-price="${room.base_price}" data-name="${hotel.name} - ${room.room_type}">${room.room_type} - $${room.base_price}</option>`);
                            });
                        } else {
                            assetSelect.html('<option value="">No Rooms Added Yet</option>').attr('disabled', true);
                        }
                    } else {
                        assetSelect.html('<option value="">No Rooms Added Yet</option>').attr('disabled', true);
                    }
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
                    const logic = itemRow.find('.amenity-logic').val();
                    const pax = parseInt($('#total_pax').val()) || 2; // Default to 2 for estimation
                    
                    let total = 0;
                    
                    if (logic === 'fixed') {
                        const price = parseFloat(itemRow.find('.amenity-price').val()) || 0;
                        const days = parseFloat(itemRow.find('.amenity-days').val()) || 1;
                        const qty = parseFloat(itemRow.find('.amenity-qty').val()) || 1;
                        total = price * days * qty;
                    } else if (logic === 'per_pax') {
                        const adPrice = parseFloat(itemRow.find('.amenity-adult-price').val()) || 0;
                        const chPrice = parseFloat(itemRow.find('.amenity-child-price').val()) || 0;
                        // For calculation in CMS, we might assume 1 adult / 1 child if not specified, 
                        // but usually it's better to calculate "Unit Price" here and multiply in preview.
                        // For now, let's just do a simple sum for the 'Net Price' field.
                        total = adPrice + chPrice; 
                    } else if (logic === 'sharing') {
                        const days = parseFloat(itemRow.find('.amenity-days').val()) || 1;
                        const doublePrice = parseFloat(itemRow.find('.amenity-double-price').val()) || 0;
                        // Use Double Sharing as the base total for estimation
                        total = (doublePrice / 2) * days * pax; 
                    } else if (logic === 'tiered_transport') {
                        const days = parseFloat(itemRow.find('.amenity-days').val()) || 1;
                        const vehicles = [];
                        itemRow.find('.vehicle-tier-check:checked').each(function() {
                            vehicles.push({
                                capacity: parseInt($(this).data('capacity')),
                                price: parseFloat($(this).data('price'))
                            });
                        });

                        if (vehicles.length > 0) {
                            // Find cheapest combination for 'pax'
                            total = calculateCheapestTransport(pax, vehicles) * days;
                        }
                    }
                    
                    itemRow.find('.amenity-total-price').val(total.toFixed(2));
                    updateOverallPricing();
                    updateDayVendorDropdowns();
                };

                $('#total_pax').on('input', function() {
                    $('.amenity-item').each(function() {
                        const id = $(this).data('amenity-id');
                        calcAmenityTotal(id);
                    });
                });

                // Cheapest Transport Fit Algorithm with Memoization
                const transportMemo = {};
                function calculateCheapestTransport(pax, vehicles) {
                    if (pax <= 0) return 0;
                    
                    const memoKey = pax + '-' + vehicles.map(v => v.price).join(',');
                    if (transportMemo[memoKey]) return transportMemo[memoKey];
                    
                    let minCost = Infinity;
                    
                    for (let v of vehicles) {
                        let cost = v.price + calculateCheapestTransport(pax - v.capacity, vehicles);
                        if (cost < minCost) minCost = cost;
                    }
                    
                    transportMemo[memoKey] = (minCost === Infinity ? 0 : minCost);
                    return transportMemo[memoKey];
                }

                window.updateDayVendorDropdowns = function() {
                    const hotels = [];
                    const transports = [];
                    const activities = [];

                    $('.amenity-item').each(function() {
                        const row = $(this);
                        const type = row.find('.amenity-type').val();
                        const id = row.attr('data-amenity-id');
                        const name = row.find('.amenity-name').val() || 'Unnamed Item';
                        
                        if (type === 'hotel') hotels.push({id, name});
                        else if (type === 'transport') transports.push({id, name});
                        else if (type === 'activity') activities.push({id, name});
                    });

                    $('.itinerary-day').each(function() {
                        const dayRow = $(this);
                        
                        // Update Hotels
                        const hotelSelect = dayRow.find('.day-hotel');
                        const currentHotel = hotelSelect.val();
                        hotelSelect.html('<option value="">— Select hotel from components —</option>');
                        hotels.forEach(h => hotelSelect.append(`<option value="${h.id}">${h.name}</option>`));
                        if (currentHotel && hotelSelect.find(`option[value="${currentHotel}"]`).length) hotelSelect.val(currentHotel);
                        
                        // Update Transport
                        const transportSelect = dayRow.find('.day-transport');
                        const currentTransport = transportSelect.val();
                        transportSelect.html('<option value="">— Select transport from components —</option>');
                        transports.forEach(t => transportSelect.append(`<option value="${t.id}">${t.name}</option>`));
                        if (currentTransport && transportSelect.find(`option[value="${currentTransport}"]`).length) transportSelect.val(currentTransport);
                        
                        // Update Activities
                        const activitySelect = dayRow.find('.day-activities');
                        const currentActivities = activitySelect.val() || [];
                        activitySelect.html('');
                        activities.forEach(a => activitySelect.append(`<option value="${a.id}">${a.name}</option>`));
                        const validActivities = currentActivities.filter(id => activitySelect.find(`option[value="${id}"]`).length);
                        activitySelect.val(validActivities).trigger('change');
                    });
                };

                // Keep amenity names in sync
                $(document).on('input', '.amenity-name', function() {
                    updateDayVendorDropdowns();
                });
                
                window.updateOverallPricing = function() {
                    let calcNetPrice = 0;
                    
                    // Compute from the new "Price Calculator" Components
                    $('.amenity-total-price').each(function() {
                        calcNetPrice += parseFloat($(this).val()) || 0;
                    });

                    // Only update if we actually derived a number
                    if (calcNetPrice > 0) {
                        $('#net_price').val(calcNetPrice.toFixed(2));
                        calculateSellingPrice();
                        updatePriceMatrix();
                    }
                };

                function updatePriceMatrix() {
                    const tiers = [2, 4, 6, 10];
                    const markupPct = parseFloat($('#markup_percentage').val()) || 0;
                    
                    tiers.forEach(paxCount => {
                        let totalNet = 0;
                        $('.amenity-item').each(function() {
                            const row = $(this);
                            const logic = row.find('.amenity-logic').val();
                            const days = parseFloat(row.find('.amenity-days').val()) || 1;
                            const qty = parseFloat(row.find('.amenity-qty').val()) || 1;
                            
                            if (logic === 'fixed') {
                                const price = parseFloat(row.find('.amenity-price').val()) || 0;
                                totalNet += price * days * qty;
                            } else if (logic === 'per_pax') {
                                const adPrice = parseFloat(row.find('.amenity-adult-price').val()) || 0;
                                totalNet += adPrice * paxCount; // Matrix assumes all adults for simplicity
                            } else if (logic === 'sharing') {
                                const doublePrice = parseFloat(row.find('.amenity-double-price').val()) || 0;
                                totalNet += (doublePrice / 2) * days * paxCount;
                            } else if (logic === 'tiered_transport') {
                                const vehicles = [];
                                row.find('.vehicle-tier-check:checked').each(function() {
                                    vehicles.push({
                                        capacity: parseInt($(this).data('capacity')),
                                        price: parseFloat($(this).data('price'))
                                    });
                                });
                                if (vehicles.length > 0) {
                                    totalNet += calculateCheapestTransport(paxCount, vehicles) * days;
                                }
                            }
                        });

                        const sellingTotal = totalNet + (totalNet * markupPct / 100);
                        const perPax = sellingTotal / paxCount;
                        $(`#matrix-pax-${paxCount}`).text('$' + perPax.toFixed(2));
                    });
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
                        const logic = $(this).find('.amenity-logic').val();
                        const name = $(this).find('.amenity-name').val();
                        const supplier_id = $(this).find('.amenity-supplier').val();
                        const asset_id = $(this).find('.amenity-asset').val();
                        const hotel_id = $(this).find('.amenity-hotel').val();
                        
                        const price = $(this).find('.amenity-price').val();
                        const adult_price = $(this).find('.amenity-adult-price').val();
                        const child_price = $(this).find('.amenity-child-price').val();
                        const quantity = $(this).find('.amenity-qty').val();
                        const days = $(this).find('.amenity-days').val();
                        const total = $(this).find('.amenity-total-price').val();

                        // Hotel Sharing rates
                        const double_price = $(this).find('.amenity-double-price').val();
                        const single_price = $(this).find('.amenity-single-price').val();
                        const triple_price = $(this).find('.amenity-triple-price').val();
                        const extrabed_price = $(this).find('.amenity-extrabed-price').val();

                        // Tiered Transport vehicles
                        const selectedVehicles = [];
                        $(this).find('.vehicle-tier-check:checked').each(function() {
                            selectedVehicles.push({
                                id: $(this).val(),
                                capacity: $(this).data('capacity'),
                                price: $(this).data('price')
                            });
                        });

                        if (supplier_id && name) {
                            addonAmenities.push({
                                type: type || 'other',
                                pricing_logic: logic || 'fixed',
                                name: name,
                                supplier_id: supplier_id || null,
                                hotel_id: hotel_id || null,
                                asset_id: asset_id || null,
                                price: parseFloat(price) || 0,
                                adult_price: parseFloat(adult_price) || 0,
                                child_price: parseFloat(child_price) || 0,
                                double_price: parseFloat(double_price) || 0,
                                single_price: parseFloat(single_price) || 0,
                                triple_price: parseFloat(triple_price) || 0,
                                extrabed_price: parseFloat(extrabed_price) || 0,
                                vehicles: selectedVehicles,
                                quantity: parseFloat(quantity) || 0,
                                days: parseFloat(days) || 0,
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
                        url: '"placeholder"',
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
        