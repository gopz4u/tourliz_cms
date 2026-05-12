
            var descriptionEditor;
            // Gallery images array
            let galleryImages = [];

            $(document).ready(function () {
                // Initialize Quill editor
                descriptionEditor = initQuillEditor('#description-editor', 300);

                // Currency Rates for Preview
                let cachedRates = [];
                $.get('/api/v1/currency/rates', function(response) {
                    if(response.success) cachedRates = response.rates;
                    updateCurrencyPreview();
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
                    const currentRateObj = cachedRates.find(r => r.code === currentCurrency) || { exchange_rate: 1 };
                    const currentRate = parseFloat(currentRateObj.exchange_rate);
                    const priceInMYR = price * currentRate;

                    cachedRates.forEach(rate => {
                        if (rate.code === currentCurrency) return;
                        
                        const convertedPrice = priceInMYR / parseFloat(rate.exchange_rate);
                        const symbol = getCurrencySymbol(rate.code);
                        
                        container.append(`
                            <div class="flex-shrink-0">
                                <div class="text-uppercase text-muted" style="font-size: 10px; font-weight: 800;">${rate.code}</div>
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

                const packageId = "placeholder";

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
                $.get('"placeholder"', function (pkg) {

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
                    $('#currency').val(pkg.currency || 'MYR');
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
                    $.ajax({
                        url: '"placeholder"',
                        type: 'GET',
                        data: { per_page: 2000 },
                        dataType: 'json',
                        success: function (response) {
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
                        },
                        error: function (xhr, status, error) {
                            console.error('Failed to load destinations:', error);
                        }
                    });

                    // Then load countries for the primary cascading select
                    $.ajax({
                        url: '"placeholder"',
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            const countries = response.data || response;
                            const countrySelect = $('#country_select');
                            countrySelect.find('option:not(:first)').remove();
                            if (Array.isArray(countries)) {
                                countries.forEach(function (country) {
                                    countrySelect.append(`<option value="${country}">${country}</option>`);
                                });
                            }
                            
                            // Initialize select2 for country
                            if ($.fn.select2) {
                                countrySelect.select2({
                                    theme: 'bootstrap-5',
                                    placeholder: 'Select Country',
                                    width: '100%',
                                    allowClear: true
                                });
                            }

                            if (selectedId) {
                                // If we have a selectedId, we need to find its country and location first
                                $.ajax({
                                    url: '"placeholder"/' + selectedId,
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function (dest) {
                                        countrySelect.val(dest.country).trigger('change');
                                        
                                        // Wait for locations to load then set location
                                        setTimeout(() => {
                                            $('#location_select').val(dest.location).trigger('change');
                                            
                                            // Wait for cities to load then set city
                                            setTimeout(() => {
                                                $('#destination_id').val(selectedId).trigger('change');
                                            }, 500);
                                        }, 500);
                                    }
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Failed to load countries:', error);
                        }
                    });
                }

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
                                    locationSelect.select2({
                                        theme: 'bootstrap-5',
                                        placeholder: 'Select Location',
                                        width: '100%',
                                        allowClear: true
                                    }).trigger('change');
                                }
                            }
                        });
                    }
                });

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
                                    destinationSelect.select2({
                                        theme: 'bootstrap-5',
                                        placeholder: 'Select City',
                                        width: '100%',
                                        allowClear: true
                                    }).trigger('change');
                                }
                            }
                        });
                    }
                });


                // Supplier Loading
                function loadSuppliers(selectedIds = []) {
                    $.get('"placeholder"', function (data) {
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
                            url: '"placeholder"',
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
                const allSuppliers = {};

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
                                            <option value="fixed" ${data.pricing_logic == 'fixed' ? 'selected' : ''}>Fixed Cost (Total)</option>
                                            <option value="per_pax" ${data.pricing_logic == 'per_pax' ? 'selected' : ''}>Variable (Per Pax)</option>
                                            <option value="sharing" ${data.pricing_logic == 'sharing' ? 'selected' : ''}>Sharing (Hotel)</option>
                                            <option value="tiered_transport" ${data.pricing_logic == 'tiered_transport' ? 'selected' : ''}>Tiered Transport</option>
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
                                        <select class="form-select form-select-sm amenity-asset" onchange="handleAssetChange('${amenityId}', this)" required ${!data.supplier_id ? 'disabled' : ''}>
                                            <option value="">Select Item</option>
                                            ${data.asset_id ? `<option value="${data.asset_id}" selected>Loading existing item...</option>` : ''}
                                        </select>
                                    </div>
                                    <div class="col">
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
                                    <!-- Common Fields -->
                                    <div class="col-md-2 calc-base-price">
                                        <label class="form-label small text-muted mb-1">Base Price</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-price" value="${data.price || '0.00'}" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-1 calc-days">
                                        <label class="form-label small text-muted mb-1">Days</label>
                                        <input type="number" class="form-control form-control-sm amenity-days" value="${data.days || 1}" min="1" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-1 calc-qty">
                                        <label class="form-label small text-muted mb-1">Qty</label>
                                        <input type="number" class="form-control form-control-sm amenity-qty" value="${data.quantity || 1}" min="1" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>

                                    <!-- Variable Pricing Fields -->
                                    <div class="col-md-2 calc-adult-price">
                                        <label class="form-label small text-muted mb-1">Adult Rate</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-adult-price" value="${data.adult_price || '0.00'}" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-child-price">
                                        <label class="form-label small text-muted mb-1">Child Rate</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-child-price" value="${data.child_price || '0.00'}" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>

                                    <!-- Sharing Based Fields (Hotel) -->
                                    <div class="col-md-2 calc-sharing-double">
                                        <label class="form-label small text-muted mb-1">Double Sharing</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-double-price" value="${data.double_price || '0.00'}" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-sharing-single">
                                        <label class="form-label small text-muted mb-1">Single Supp.</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-single-price" value="${data.single_price || '0.00'}" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-sharing-triple">
                                        <label class="form-label small text-muted mb-1">Triple Sharing</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-triple-price" value="${data.triple_price || '0.00'}" oninput="calcAmenityTotal('${amenityId}')">
                                    </div>
                                    <div class="col-md-2 calc-sharing-extrabed">
                                        <label class="form-label small text-muted mb-1">Extra Bed</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm amenity-extrabed-price" value="${data.extrabed_price || '0.00'}" oninput="calcAmenityTotal('${amenityId}')">
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

                    if (data.supplier_id) {
                        handleSupplierChange(amenityId, itemRowSelect(amenityId), data.asset_id, data.vehicles);
                        handleLogicChange(amenityId, itemRow.find('.amenity-logic')[0]);
                    }
                };

                const itemRowSelect = (id) => $(`.amenity-item[data-amenity-id="${id}"] .amenity-supplier`)[0];
                window.handleSupplierChange = function (amenityId, select, preSelectedAssetId = null) {
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
                    
                    // Logic is handled by handleLogicChange called from addAmenity for existing data
                    // For new supplier selection, we can default it
                    if (!itemRow.find('.amenity-logic').val()) {
                        const logicSelect = itemRow.find('.amenity-logic');
                        if (supplierType === 'hotel') logicSelect.val('sharing');
                        else if (supplierType === 'transport') logicSelect.val('fixed');
                        else logicSelect.val('per_pax');
                    }

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
                            } else {
                                hotelSelect.html('<option value="">No Hotels Linked to this Supplier</option>').attr('disabled', true);
                            }
 
                            if (preSelectedAssetId) {
                                const hotelWithRoom = hotelList.find(h => {
                                    if (!h.rooms) return false;
                                    let roomList = Array.isArray(h.rooms) ? h.rooms : Object.values(h.rooms);
                                    return roomList.some(r => r.id == preSelectedAssetId);
                                });
                                if (hotelWithRoom) {
                                    hotelSelect.val(hotelWithRoom.id);
                                    handleHotelChange(amenityId, hotelSelect[0], preSelectedAssetId);
                                }
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

                            // Also populate tiered transport list
                            const tieredList = itemRow.find('.tiered-vehicles-list');
                            tieredList.empty();
                            if (data.type === 'transport') {
                                assetList.forEach(asset => {
                                    const isChecked = preSelectedVehicles && preSelectedVehicles.some(v => v.id == asset.id);
                                    const label = `${asset.vehicle_type} (${asset.capacity} Pax) - $${asset.base_price}`;
                                    tieredList.append(`
                                        <div class="form-check form-check-inline small">
                                            <input class="form-check-input vehicle-tier-check" type="checkbox" 
                                                value="${asset.id}" 
                                                data-capacity="${asset.capacity}" 
                                                data-price="${asset.base_price}"
                                                ${isChecked ? 'checked' : ''}
                                                onchange="calcAmenityTotal('${amenityId}')">
                                            <label class="form-check-label">${label}</label>
                                        </div>
                                    `);
                                });
                            }
                        }
                    }).fail(function(xhr) {
                        console.error("Failed to load assets for supplier " + supplierId, xhr);
                        hotelSelect.html('<option value="">Error loading hotels</option>');
                        assetSelect.html('<option value="">Error loading items</option>');
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
 
                window.handleHotelChange = function (amenityId, select, preSelectedAssetId = null) {
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
 
                    if (preSelectedAssetId) {
                        assetSelect.val(preSelectedAssetId);
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
                        total = adPrice + chPrice; 
                    } else if (logic === 'sharing') {
                        const days = parseFloat(itemRow.find('.amenity-days').val()) || 1;
                        const doublePrice = parseFloat(itemRow.find('.amenity-double-price').val()) || 0;
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
                    $('.amenity-total-price').each(function() {
                        calcNetPrice += parseFloat($(this).val()) || 0;
                    });
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
                                totalNet += adPrice * paxCount;
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
                        updatePriceMatrix();
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
                                value: total
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
                        currency: $('#currency').val() || 'MYR',
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
                            window.location.href = '"placeholder"';
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
        