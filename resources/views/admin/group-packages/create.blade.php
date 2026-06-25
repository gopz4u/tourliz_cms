@extends('layouts.admin')

@section('title', 'Create Group Package')

@push('extra_css')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0052cc 0%, #00a3ff 100%);
            --glass-bg: rgba(255, 255, 255, 0.98);
            --glass-border: rgba(0, 0, 0, 0.08);
            --card-radius: 24px;
            --app-bg: #f4f7fe;
        }

        body {
            background-color: var(--app-bg);
        }

        .wizard-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Stepper Styling */
        .wizard-stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3.5rem;
            position: relative;
            padding: 0 50px;
        }

        .wizard-stepper::before {
            content: '';
            position: absolute;
            top: 24px;
            left: 100px;
            right: 100px;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            background: transparent;
            text-align: center;
            width: 100px;
        }

        .step-circle {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            background: white;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-weight: 800;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            color: #94a3b8;
        }

        .step-item.active .step-circle {
            background: var(--primary-gradient);
            border-color: transparent;
            color: white;
            box-shadow: 0 10px 25px rgba(0, 82, 204, 0.3);
            transform: scale(1.1);
        }

        .step-item.completed .step-circle {
            background: #36b37e;
            border-color: transparent;
            color: white;
        }

        .step-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: #94a3b8;
            transition: color 0.3s;
        }

        .step-item.active .step-title {
            color: #0052cc;
        }

        /* Content Area */
        .wizard-card {
            background: white;
            border-radius: var(--card-radius);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--glass-border);
            overflow: hidden;
        }

        .wizard-body {
            padding: 4rem;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: slideIn 0.5s ease-out forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* UI Components */
        .premium-label {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .premium-input {
            background: #f8fafc;
            border: 1.5px solid #f1f5f9;
            border-radius: 16px;
            padding: 0.9rem 1.25rem;
            transition: all 0.3s;
        }

        .premium-input:focus {
            background: white;
            border-color: #0052cc;
            box-shadow: 0 0 0 5px rgba(0, 82, 204, 0.08);
        }

        .itinerary-day-card {
            border-radius: 24px;
            border: 1px solid #f1f5f9;
            background: white;
            margin-bottom: 2.5rem;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        .itinerary-day-card:hover {
            border-color: #0052cc;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.05);
        }

        .day-header {
            padding: 1.5rem 2rem;
            background: #f8fafc;
            border-radius: 24px 24px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .day-count {
            background: var(--primary-gradient);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
        }

        /* Modern Dropzone */
        .dropzone-box {
            border: 2px dashed #cbd5e1;
            border-radius: 24px;
            padding: 3rem;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s;
        }

        .dropzone-box:hover {
            border-color: #0052cc;
            background: #eff6ff;
        }

        /* Sticky Footer */
        .wizard-footer {
            padding: 1.5rem 4rem;
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            bottom: 0;
            z-index: 100;
        }

        .price-tag {
            background: #f1f5f9;
            padding: 0.75rem 1.5rem;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-publish {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 1rem 3rem;
            border-radius: 50px;
            font-weight: 800;
            box-shadow: 0 10px 20px rgba(0, 82, 204, 0.2);
        }
    </style>
@endpush

@section('content')
    <div class="wizard-container py-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h1 class="fw-black text-dark tracking-tight mb-1"><i class="bi bi-people-fill"></i> New Group Package</h1>
                <p class="text-muted mb-0">Crafting collaborative group tours & events</p>
            </div>
            <div class="text-end">
                <span class="badge bg-white text-primary border rounded-pill px-3 py-2 fw-bold">
                    <i class="bi bi-clock-history me-1"></i> Auto-saving...
                </span>
            </div>
        </div>

        <!-- Stepper -->
        <div class="wizard-stepper mb-5">
            <div class="step-item active" data-step="1">
                <div class="step-circle">01</div>
                <div class="step-title text-decoration-none">Fundamentals</div>
            </div>
            <div class="step-item" data-step="2">
                <div class="step-circle">02</div>
                <div class="step-title">Journey Map</div>
            </div>
            <div class="step-item" data-step="3">
                <div class="step-circle">03</div>
                <div class="step-title">Commercials</div>
            </div>
            <div class="step-item" data-step="4">
                <div class="step-circle">04</div>
                <div class="step-title">Compliance</div>
            </div>
        </div>

        <form id="group-package-wizard-form" enctype="multipart/form-data">
            <div class="wizard-card">
                <div class="wizard-body p-4 p-md-5">

                    <!-- STEP 1: FUNDAMENTALS -->
                    <div class="form-step active" id="step-1">
                        <div class="mb-5">
                            <label class="premium-label fs-5">Group Package Title <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-control premium-input form-control-lg fs-4 fw-bold"
                                placeholder="Ex: Grand Europe Scenic Group Tour" required>
                            <p class="small text-muted mt-2">This is the public name of your group tour package.</p>
                        </div>

                        <div class="row g-5">
                            <div class="col-md-8">
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <label class="premium-label">Country <span class="text-danger">*</span></label>
                                        <select name="country" id="country_select" class="form-select select2" required>
                                            <option value="">Select Country</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="premium-label">Location <span class="text-danger">*</span></label>
                                        <select name="location" id="location_select" class="form-select select2" disabled
                                            required>
                                            <option value="">Select Location</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="premium-label">Primary City <span class="text-danger">*</span></label>
                                        <select name="destination_id" id="destination_id" class="form-select select2"
                                            disabled required>
                                            <option value="">Select City</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="premium-label">Additional Countries Covered</label>
                                        <div class="row g-2" id="multi_country_section"
                                            style="max-height: 120px; overflow-y: auto;">
                                            <p class="text-muted small">Countries will load here. Use the Country dropdown
                                                above first.</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="premium-label">Service Categories <span
                                                class="text-danger">*</span></label>
                                        <select name="categories[]" id="categories" class="form-select select2" multiple
                                            required data-placeholder="Select Categories">
                                            <option value="Hotels">Hotels</option>
                                            <option value="Transport">Transport</option>
                                            <option value="Entry Tickets">Entry Tickets</option>
                                            <option value="Airport Pickup">Airport Pickup</option>
                                            <option value="Airport Drop">Airport Drop</option>
                                            <option value="Other Services">Other Services</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="premium-label">Package Category</label>
                                        <select name="package_category" id="package_category"
                                            class="form-select premium-input">
                                            <option value="">Select Category</option>
                                            <option value="Honeymoon">Honeymoon</option>
                                            <option value="Budget">Budget</option>
                                            <option value="Standard">Standard</option>
                                            <option value="Premium">Premium</option>
                                            <option value="Platinum">Platinum</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="premium-label">Source Vendors (Suppliers) <span
                                                class="text-danger">*</span></label>
                                        <select name="supplier_ids[]" id="supplier_ids" class="form-select select2" multiple
                                            required data-placeholder="Search Vendors">
                                        </select>
                                        <small class="text-muted">Select all vendors providing services for this
                                            package.</small>
                                    </div>
                                </div>

                                <!-- Dynamic Amenities Sections -->
                                <div class="row g-4 mt-1 border-top pt-4">
                                    <!-- Hotel Specific Amenities -->
                                    <div id="hotel-amenities" class="col-12 amenities-section" style="display: none;">
                                        <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-building"></i> Hotel Details
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="premium-label">Star Rating</label>
                                                <select class="form-select premium-input" id="star_rating"
                                                    name="star_rating">
                                                    <option value="">Select star rating</option>
                                                    <option value="1">1 Star</option>
                                                    <option value="2">2 Stars</option>
                                                    <option value="3">3 Stars</option>
                                                    <option value="4">4 Stars</option>
                                                    <option value="5">5 Stars</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="premium-label">Accommodation Type</label>
                                                <input type="text" class="form-control premium-input"
                                                    id="accommodation_type" name="accommodation_type"
                                                    placeholder="Resort, Villa, Boutique Hotel">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Transport Specific Amenities -->
                                    <div id="transport-amenities" class="col-12 amenities-section" style="display: none;">
                                        <h6 class="fw-bold mb-3 text-warning"><i class="bi bi-car-front"></i> Transportation
                                            Details</h6>
                                        <div>
                                            <label class="premium-label">Vehicle Type</label>
                                            <input type="text" class="form-control premium-input" id="vehicle_type"
                                                name="vehicle_type" placeholder="Sedan, SUV, Coaster Bus, Van">
                                        </div>
                                    </div>

                                    <!-- Tickets Specific Amenities -->
                                    <div id="ticket-amenities" class="col-12 amenities-section" style="display: none;">
                                        <h6 class="fw-bold mb-3 text-success"><i class="bi bi-ticket-perforated"></i> Entry
                                            Tickets Details</h6>
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="premium-label">Ticket Name</label>
                                                <input type="text" class="form-control premium-input" id="ticket_name"
                                                    name="ticket_name" placeholder="VIP Pass, Full Admission, Combo Entry">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="premium-label">Ticket Count</label>
                                                <input type="number" class="form-control premium-input" id="ticket_count"
                                                    name="ticket_count" min="1" placeholder="Qty">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4 mt-3">
                                    <div class="col-md-6">
                                        <label class="premium-label">Slug</label>
                                        <input type="text" name="slug" id="slug" class="form-control premium-input"
                                            placeholder="Auto-generated slug">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="premium-label">Flight Ticket Included?</label>
                                        <div class="d-flex gap-3 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="includes_flight"
                                                    id="includes_flight_yes" value="1">
                                                <label class="form-check-label small fw-bold"
                                                    for="includes_flight_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="includes_flight"
                                                    id="includes_flight_no" value="0" checked>
                                                <label class="form-check-label small fw-bold"
                                                    for="includes_flight_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-5">
                                    <label class="premium-label">Hero Cover Image</label>
                                    <label for="hero-img" class="dropzone-box w-100 mb-0" id="cover-upload-text">
                                        <i class="bi bi-image text-primary fs-1"></i>
                                        <p class="small text-muted mt-2">Click to upload cover photo</p>
                                    </label>
                                    <input type="file" id="hero-img" class="d-none" accept="image/*"
                                        onchange="uploadCoverImage(this)">
                                    <input type="hidden" id="image_path" name="image">
                                    <div id="hero-preview" class="mt-3 d-none position-relative">
                                        <img src="" class="img-fluid rounded-4 shadow-sm border"
                                            style="max-height: 200px; width: 100%; object-fit: cover;">
                                        <button type="button"
                                            class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-2"
                                            onclick="clearHeroPreview()">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="premium-label">Gallery Images</label>
                                    <label for="gallery-img" class="dropzone-box py-3 w-100 mb-0">
                                        <i class="bi bi-images text-primary fs-3"></i>
                                        <p class="small text-muted mb-0">Select photos to upload</p>
                                    </label>
                                    <input type="file" id="gallery-img" class="d-none" multiple accept="image/*"
                                        onchange="$('#upload-gallery-btn').toggle($(this)[0].files.length > 0)">
                                    <button type="button" class="btn btn-sm btn-dark w-100 mt-2" id="upload-gallery-btn"
                                        style="display:none;" onclick="uploadGallery(this)">
                                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload Selected
                                    </button>
                                    <input type="hidden" id="gallery_paths" name="gallery_paths">
                                    <div id="gallery-previews" class="d-flex flex-wrap mt-2"></div>
                                </div>

                                <div class="bg-light p-4 rounded-4 border">
                                    <h6 class="fw-bold mb-3">Attributes</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                            checked>
                                        <label class="form-check-label small fw-bold" for="is_featured">Mark as
                                            Featured</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                            checked>
                                        <label class="form-check-label small fw-bold" for="is_active">Live Status</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: JOURNEY MAP (COMPONENTS + DAYS) -->
                    <div class="form-step" id="step-2">
                        <div class="bg-white border rounded-4 p-4 p-md-5 mb-5 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 class="fw-black text-dark mb-1"><i class="bi bi-cpu text-primary"></i> Package Cost
                                        Components</h4>
                                    <p class="text-muted small mb-0">Add suppliers, rooms, transport, or tickets to
                                        dynamically build the base cost.</p>
                                </div>
                                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="addAmenity()">
                                    <i class="bi bi-plus-lg me-2"></i> Add Component
                                </button>
                            </div>
                            <div id="addon-amenities-container">
                                <!-- Dynamic Cost Component rows -->
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-5">
                            <div>
                                <h4 class="fw-black text-dark mb-1"><i class="bi bi-calendar-range text-primary"></i>
                                    Day-by-Day Itinerary</h4>
                                <p class="text-muted small mb-0">Total Duration: <span id="dur-count"
                                        class="fw-bold text-primary">0 Days</span></p>
                            </div>
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4"
                                onclick="addItineraryDay()">
                                <i class="bi bi-plus-lg me-2"></i> Add New Day
                            </button>
                        </div>

                        <div id="itinerary-timeline">
                            <!-- Dynamic Day Cards -->
                        </div>
                    </div>

                    <!-- STEP 3: COMMERCIALS -->
                    <div class="form-step" id="step-3">
                        <div class="row g-5">
                            <div class="col-md-7">
                                <div class="bg-white border rounded-4 p-4 p-md-5 shadow-sm mb-4">
                                    <h5 class="fw-black mb-4">Net Cost Aggregate</h5>
                                    <div id="cost-breakdown" class="mb-4">
                                        <!-- Dynamic breakdown of Cost Components -->
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                                        <span class="fw-bold text-muted">Total Vendor Cost</span>
                                        <span class="fs-2 fw-black text-dark" id="net-total">RM 0.00</span>
                                    </div>
                                </div>

                                <div class="row g-4 mt-2">
                                    <div class="col-md-6">
                                        <label class="premium-label">Net Price (Vendor Cost) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="net_price" id="net_price"
                                            class="form-control premium-input text-success fw-bold" readonly required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="premium-label">Service Markup (%)</label>
                                        <input type="number" step="0.01" name="markup_percentage" id="markup_percentage"
                                            class="form-control premium-input" value="10">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="premium-label">Selling Price (Adult) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="price" id="price"
                                            class="form-control premium-input form-control-lg fw-black text-primary"
                                            required>
                                        <small class="text-muted">Formula: Vendor Cost + Markup Amount</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="premium-label">Discount Price</label>
                                        <input type="number" step="0.01" name="discount_price" id="discount_price"
                                            class="form-control premium-input">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="premium-label">Kids Price (Age 2-6)</label>
                                        <input type="number" step="0.01" name="price_2_6" id="price_2_6"
                                            class="form-control premium-input">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="premium-label">Kids Price (Age 6-10)</label>
                                        <input type="number" step="0.01" name="price_6_10" id="price_6_10"
                                            class="form-control premium-input">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="bg-light border rounded-4 p-4 mb-4">
                                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-globe"></i> Details & Matrix</h6>
                                    <div class="mb-3">
                                        <label class="premium-label">Currency</label>
                                        <select class="form-select premium-input" id="currency" name="currency">
                                            <option value="MYR" selected>MYR - Malaysian Ringgit</option>
                                            <option value="USD">USD - US Dollar</option>
                                            <option value="SGD">SGD - Singapore Dollar</option>
                                            <option value="AED">AED - UAE Dirham</option>
                                            <option value="INR">INR - Indian Rupee</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="premium-label">Announcement Date</label>
                                        <input type="date" class="form-control premium-input" id="announcement_date"
                                            name="announcement_date">
                                    </div>
                                    <div class="mb-3">
                                        <label class="premium-label">Total Pax (Passenger Limit)</label>
                                        <input type="number" class="form-control premium-input" id="total_pax"
                                            name="total_pax" min="1" placeholder="Total passenger limit">
                                    </div>
                                </div>

                                <div class="card border-0 bg-primary text-white rounded-4 shadow-lg p-4">
                                    <div class="card-body">
                                        <h5 class="fw-bold mb-4">Global Conversion Previews</h5>
                                        <div id="fx-matrix" class="space-y-3">
                                            <!-- FX Matrix Previews -->
                                        </div>
                                        <div class="mt-4 p-3 bg-white bg-opacity-10 rounded-3 text-center">
                                            <p class="small mb-0 opacity-75">Rates based on daily market average</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: COMPLIANCE -->
                    <div class="form-step" id="step-4">
                        <div class="mb-4">
                            <label class="premium-label fs-5">Main Description</label>
                            <div id="description-editor"
                                style="height: 250px; border-radius: 16px; background: #f8fafc; border: 1px solid #f1f5f9;">
                            </div>
                            <input type="hidden" name="description" id="description">
                        </div>

                        <div class="row g-4 mt-3">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="premium-label mb-0">Package Inclusions</label>
                                    <button type="button" class="btn btn-sm btn-outline-success"
                                        onclick="addInclusionRow()">
                                        <i class="bi bi-plus-lg"></i> Add
                                    </button>
                                </div>
                                <div id="inclusions-container" class="pe-2" style="max-height: 300px; overflow-y: auto;">
                                    <!-- Dynamic Inclusion rows -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="premium-label mb-0">Package Exclusions</label>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="addExclusionRow()">
                                        <i class="bi bi-plus-lg"></i> Add
                                    </button>
                                </div>
                                <div id="exclusions-container" class="pe-2" style="max-height: 300px; overflow-y: auto;">
                                    <!-- Dynamic Exclusion rows -->
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mt-4">
                            <div class="col-md-12">
                                <label class="premium-label">Short Highlights (Meta Summary)</label>
                                <textarea name="short_description" id="short_description" class="form-control premium-input"
                                    rows="3" placeholder="Brief highlights of this package..."></textarea>
                            </div>
                        </div>

                        <div class="row g-4 mt-5">
                            <div class="col-12">
                                <div class="bg-light p-4 rounded-4 border">
                                    <h6 class="fw-bold mb-4 text-primary"><i class="bi bi-search me-2"></i> Search Engine
                                        Optimization (SEO)</h6>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="premium-label small">Meta Title</label>
                                            <input type="text" name="meta_title" id="meta_title"
                                                class="form-control premium-input" placeholder="SEO optimized title">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label small">Meta Keywords</label>
                                            <input type="text" name="meta_keywords" id="meta_keywords"
                                                class="form-control premium-input"
                                                placeholder="travel, maldives, luxury, tour">
                                        </div>
                                        <div class="col-12">
                                            <label class="premium-label small">Meta Description</label>
                                            <textarea name="meta_description" id="meta_description"
                                                class="form-control premium-input" rows="3"
                                                placeholder="Brief summary for Google search results..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Controls -->
                <div class="wizard-footer">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" id="btn-prev"
                        style="display:none;" onclick="step(-1)">
                        <i class="bi bi-chevron-left me-2"></i> Previous
                    </button>
                    <div class="ms-auto d-flex align-items-center gap-4">
                        <div class="price-tag d-none d-lg-flex">
                            <span class="small fw-bold text-muted">LIVE QUOTE:</span>
                            <span class="fw-black text-primary fs-5" id="foot-price">RM 0.00</span>
                        </div>
                        <button type="button" class="btn btn-success rounded-pill px-5 py-3 fw-bold" id="btn-save-immediate"
                            onclick="submitPackageForm()">
                            Save Package <i class="bi bi-check-circle ms-2"></i>
                        </button>
                        <button type="button" class="btn btn-dark rounded-pill px-5 py-3 fw-bold" id="btn-next"
                            onclick="step(1)">
                            Next Step <i class="bi bi-chevron-right ms-2"></i>
                        </button>
                        <button type="button" class="btn btn-publish px-5 py-3" id="btn-publish" style="display:none;"
                            onclick="submitPackageForm()">
                            FINISH & PUBLISH <i class="bi bi-rocket-takeoff ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Template: Day Card -->
    <template id="day-tpl">
        <div class="itinerary-day-card itinerary-day" id="day-{N}" data-day="{N}">
            <div class="day-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="day-count">{N}</div>
                    <input type="text" class="form-control border-0 bg-transparent fw-black fs-5 day-title"
                        placeholder="Day Theme (e.g. Arrival & Sunset Cruise)" style="width: 400px; outline:none;" required>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm border-0 rounded-circle"
                    onclick="removeItineraryDay('day-{N}')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="premium-label"><i class="bi bi-geo-alt-fill text-danger me-2"></i>
                            Destinations</label>
                        <select class="form-select select2-spots-multi day-destinations" multiple="multiple"
                            style="width: 100%;">
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="premium-label"><i class="bi bi-egg-fried text-warning me-2"></i> Meals
                            Included</label>
                        <select class="form-select select2-spots-multi day-meals" multiple="multiple" style="width: 100%;">
                            <option value="Breakfast">Breakfast</option>
                            <option value="Lunch">Lunch</option>
                            <option value="Dinner">Dinner</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="premium-label">Experience Narration</label>
                    <textarea class="form-control premium-input day-description" rows="3"
                        placeholder="Describe the day's journey..."></textarea>
                </div>

                <div class="bg-light p-4 rounded-4 border mt-4">
                    <p class="small fw-bold mb-3 text-primary"><i class="bi bi-link-45deg"></i> LINKED SERVICES (FROM COST
                        COMPONENTS)</p>

                    <!-- HOTEL ROW -->
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-md-2">
                            <span class="badge bg-primary-subtle text-primary py-2 px-3 rounded-pill w-100"><i
                                    class="bi bi-building"></i> Hotel</span>
                        </div>
                        <div class="col-md-8">
                            <select class="form-select premium-input day-hotel">
                                <option value="">— Select hotel from components —</option>
                            </select>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary w-100"
                                onclick="toggleCustomHotel(this)" style="border-radius:12px;">
                                <i class="bi bi-plus"></i> Custom
                            </button>
                        </div>
                        <div class="col-12 custom-hotel-row" style="display:none;">
                            <input type="text" class="form-control premium-input mt-1 day-custom-hotel"
                                placeholder="Enter custom hotel name (e.g. Hilton Resort, Langkawi)">
                        </div>
                    </div>

                    <!-- TRANSPORT ROW -->
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-md-2">
                            <span class="badge bg-warning-subtle text-warning py-2 px-3 rounded-pill w-100"><i
                                    class="bi bi-car-front"></i> Transport</span>
                        </div>
                        <div class="col-md-8">
                            <select class="form-select premium-input day-transport">
                                <option value="">— Select transport from components —</option>
                            </select>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-outline-warning w-100"
                                onclick="toggleCustomTransport(this)" style="border-radius:12px;">
                                <i class="bi bi-plus"></i> Custom
                            </button>
                        </div>
                        <div class="col-12 custom-transport-row" style="display:none;">
                            <input type="text" class="form-control premium-input mt-1 day-custom-transport"
                                placeholder="Enter custom transport mode (e.g. Private SUV Transfer)">
                        </div>
                    </div>

                    <!-- ACTIVITIES ROW -->
                    <div class="row g-3 align-items-center">
                        <div class="col-md-2">
                            <span class="badge bg-success-subtle text-success py-2 px-3 rounded-pill w-100"><i
                                    class="bi bi-lightning-charge"></i> Activities</span>
                        </div>
                        <div class="col-md-10">
                            <select class="form-select select2-spots-multi day-activities" multiple="multiple"
                                style="width: 100%;">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Template: Service/Amenity Component Row -->
    <template id="amenity-tpl">
        <div class="card mb-3 border-0 shadow-sm amenity-item" data-amenity-id="amenity-{ID}"
            style="border-left: 4px solid #0052cc !important; border-radius: 16px;">
            <div class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">Supplier</label>
                        <select class="form-select premium-input amenity-supplier"
                            onchange="handleSupplierChange('amenity-{ID}', this)" required>
                            <option value="">Select Supplier</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1 dynamic-asset-label">Item / Service Type</label>
                        <select class="form-select premium-input amenity-asset"
                            onchange="handleAssetChange('amenity-{ID}', this)" required disabled>
                            <option value="">Select Item</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">Name / Title</label>
                        <input type="text" class="form-control premium-input amenity-name"
                            placeholder="Enter component name" required>
                        <input type="hidden" class="amenity-type">
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold text-muted mb-1 text-end d-block">Component Total (RM)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"
                                style="border-radius:16px 0 0 16px; font-weight:700;">RM</span>
                            <input type="number"
                                class="form-control text-end amenity-total-price fw-black border-0 bg-light"
                                style="border-radius:0 16px 16px 0; font-size:1.1rem;" value="0.00" step="0.01" readonly>
                        </div>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-outline-danger border-0 p-2"
                            onclick="removeAmenity('amenity-{ID}')" style="border-radius:12px;">
                            <i class="bi bi-trash fs-5"></i>
                        </button>
                    </div>
                </div>

                <div class="row g-3 mt-3 calc-fields bg-light p-3 rounded-4" style="display:none;">
                    <div class="col-md-3 calc-base-price">
                        <label class="small fw-bold text-muted mb-1">Base Price (RM)</label>
                        <input type="number" step="0.01" class="form-control premium-input amenity-price" value="0.00"
                            oninput="calcAmenityTotal('amenity-{ID}')">
                    </div>

                    <!-- Hotel specific -->
                    <div class="col-md-3 calc-days" style="display:none;">
                        <label class="small fw-bold text-muted mb-1">Nights</label>
                        <input type="number" class="form-control premium-input amenity-days" value="1" min="1"
                            oninput="calcAmenityTotal('amenity-{ID}')">
                    </div>

                    <!-- General Quantity -->
                    <div class="col-md-3 calc-qty" style="display:none;">
                        <label class="small fw-bold text-muted mb-1">Quantity</label>
                        <input type="number" class="form-control premium-input amenity-qty" value="1" min="1"
                            oninput="calcAmenityTotal('amenity-{ID}')">
                    </div>

                    <!-- Activity/Ticket specific -->
                    <div class="col-md-3 calc-adult-price" style="display:none;">
                        <label class="small fw-bold text-muted mb-1">Adult Price (RM)</label>
                        <input type="number" step="0.01" class="form-control premium-input amenity-adult-price" value="0.00"
                            oninput="calcAmenityTotal('amenity-{ID}')">
                    </div>
                    <div class="col-md-3 calc-adult-qty" style="display:none;">
                        <label class="small fw-bold text-muted mb-1">Adult Count</label>
                        <input type="number" class="form-control premium-input amenity-adult-qty" value="1" min="0"
                            oninput="calcAmenityTotal('amenity-{ID}')">
                    </div>
                    <div class="col-md-3 calc-child-price" style="display:none;">
                        <label class="small fw-bold text-muted mb-1">Child Price (RM)</label>
                        <input type="number" step="0.01" class="form-control premium-input amenity-child-price" value="0.00"
                            oninput="calcAmenityTotal('amenity-{ID}')">
                    </div>
                    <div class="col-md-3 calc-child-qty" style="display:none;">
                        <label class="small fw-bold text-muted mb-1">Child Count</label>
                        <input type="number" class="form-control premium-input amenity-child-qty" value="0" min="0"
                            oninput="calcAmenityTotal('amenity-{ID}')">
                    </div>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
    <!-- Quill Editor Assets -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let activeStep = 1;
        let quillEditors = {};
        let cachedSuppliers = [];
        let currentCountryDestinations = [];
        let amenityCounter = 0;
        let itineraryDayCount = 0;
        let galleryImages = [];

        // Global suppliers data passed from controller
        const allSuppliers = @json($suppliers);

        $(document).ready(function () {
            // Init selects
            $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });

            // Step 4 Rich Editors
            quillEditors['desc'] = initQuillEditor('#description-editor', 250);

            // Load Initial Cascading Values
            loadCountries();
            loadSuppliers();

            // Bind Category Toggles
            $('#categories').on('change', handleCategoryChange);

            // Populate dynamic rows for Inclusions / Exclusions initially
            addInclusionRow();
            addExclusionRow();

            // Title slug autogeneration
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

            // Pricing calculators bind
            $('#markup_percentage, #net_price').on('input', calculateSellingPrice);
            $('#price').on('change', reverseCalculateMarkup);

            // Add first day by default
            addItineraryDay();
        });

        // ----------------------------------------------------
        // STEP NAVIGATION & VALIDATION
        // ----------------------------------------------------
        function step(dir) {
            if (dir > 0) {
                let errors = validateStep(activeStep);
                if (errors.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Required Fields Missing',
                        html: `<div class="text-start small">${errors.join('<br>')}</div>`,
                        confirmButtonColor: '#1e293b'
                    });
                    return;
                }
            }

            let target = activeStep + dir;
            if (target < 1 || target > 4) return;

            $(`#step-${activeStep}`).removeClass('active');
            $(`#step-${target}`).addClass('active');

            $('.step-item').removeClass('active completed');
            for (let i = 1; i < target; i++) {
                $(`.step-item[data-step="${i}"]`).addClass('completed');
            }
            $(`.step-item[data-step="${target}"]`).addClass('active');

            activeStep = target;

            // Toggle buttons display
            $('#btn-prev').toggle(activeStep > 1);
            $('#btn-next').toggle(activeStep < 4);
            $('#btn-save-immediate').toggle(activeStep < 4);
            $('#btn-publish').toggle(activeStep === 4);

            if (activeStep === 3) {
                updateOverallPricing();
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function validateStep(stepId) {
            let stepEl = document.getElementById(`step-${stepId}`);
            if (!stepEl) return [];

            let inputs = stepEl.querySelectorAll('input, select, textarea');
            let errors = [];

            const fieldLabels = {
                'name': 'Group Package Title',
                'country': 'Country',
                'location': 'Location',
                'destination_id': 'Primary City',
                'categories[]': 'Service Categories',
                'supplier_ids[]': 'Source Vendors',
                'net_price': 'Net Price',
                'price': 'Selling Price'
            };

            inputs.forEach(input => {
                if (input.hasAttribute('required') || input.required) {
                    let isEmpty = !input.value || input.value.trim() === '';
                    if (input.tagName === 'SELECT' && input.multiple) {
                        isEmpty = $(input).val() === null || $(input).val().length === 0;
                    }
                    if (isEmpty) {
                        let fieldName = fieldLabels[input.name] || input.placeholder || input.name || 'Required Field';
                        errors.push(`<b>${fieldName}</b> is required.`);
                    }
                }
            });

            // Step 2 specific validations
            if (stepId === 2) {
                if ($('.itinerary-day-card').length === 0) {
                    errors.push('At least <b>one day</b> must be added to the itinerary.');
                }
                $('.itinerary-day-card').each(function (i) {
                    let dayNum = i + 1;
                    let dayTitle = $(this).find('.day-title').val();
                    if (!dayTitle || dayTitle.trim() === '') {
                        errors.push(`Day ${dayNum}: <b>Day Theme/Title</b> is required.`);
                    }
                });
            }

            return errors;
        }

        // ----------------------------------------------------
        // STEP 1: FUNDAMENTALS SCRIPTS
        // ----------------------------------------------------
        function loadCountries() {
            $.ajax({
                url: '{{ route("admin.destinations.countries") }}',
                type: 'GET',
                dataType: 'json',
                success: function (countries) {
                    const countrySelect = $('#country_select');
                    countrySelect.find('option:not(:first)').remove();
                    if (Array.isArray(countries)) {
                        countries.forEach(function (country) {
                            countrySelect.append(`<option value="${country}">${country}</option>`);
                        });
                    }
                    countrySelect.trigger('change.select2');
                }
            });
        }

        $('#country_select').on('change', function () {
            const country = $(this).val();
            const locationSelect = $('#location_select');
            const destinationSelect = $('#destination_id');

            locationSelect.find('option:not(:first)').remove();
            destinationSelect.find('option:not(:first)').remove();

            locationSelect.prop('disabled', true).trigger('change.select2');
            destinationSelect.prop('disabled', true).trigger('change.select2');

            if (country) {
                $.ajax({
                    url: '{{ route("admin.destinations.locations") }}',
                    type: 'GET',
                    data: { country: country },
                    dataType: 'json',
                    success: function (locations) {
                        if (Array.isArray(locations)) {
                            locations.forEach(function (loc) {
                                locationSelect.append(`<option value="${loc}">${loc}</option>`);
                            });
                        }
                        locationSelect.prop('disabled', false).trigger('change.select2');
                    }
                });

                // Pre-load cities for itinerary selections
                $.ajax({
                    url: '{{ route("admin.destinations.index") }}',
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

        $('#location_select').on('change', function () {
            const country = $('#country_select').val();
            const location = $(this).val();
            const destinationSelect = $('#destination_id');

            destinationSelect.find('option:not(:first)').remove();
            destinationSelect.prop('disabled', true).trigger('change.select2');

            if (location) {
                $.ajax({
                    url: '{{ route("admin.destinations.cities") }}',
                    type: 'GET',
                    data: { country: country, location: location },
                    dataType: 'json',
                    success: function (cities) {
                        if (Array.isArray(cities)) {
                            cities.forEach(function (city) {
                                destinationSelect.append(`<option value="${city.id}">${city.city} (${city.name})</option>`);
                            });
                        }
                        destinationSelect.prop('disabled', false).trigger('change.select2');
                    }
                });
            }
        });

        $('#destination_id').on('change', function () {
            const destId = $(this).val();
            loadSuppliers(destId || null);
        });

        function loadSuppliers(destinationId = null) {
            const params = {};
            if (destinationId) params.destination_id = destinationId;

            $.get('{{ route("admin.suppliers.index") }}', params, function (data) {
                const suppliers = data.data || data;
                if (Array.isArray(suppliers)) {
                    cachedSuppliers = suppliers;
                    updateSupplierDropdowns();
                }
            });
        }

        function updateSupplierDropdowns() {
            const select = $('#supplier_ids');
            const curVal = select.val() || [];
            select.find('optgroup, option').remove();

            const byType = {};
            cachedSuppliers.forEach(s => {
                if (!byType[s.type]) byType[s.type] = [];
                byType[s.type].push(s);
            });

            Object.keys(byType).sort().forEach(type => {
                let grp = $('<optgroup>').attr('label', type);
                byType[type].forEach(s => {
                    let opt = $('<option>').val(s.id).text(`${s.name} (${s.type})`);
                    if (curVal.includes(s.id.toString()) || curVal.includes(parseInt(s.id))) {
                        opt.attr('selected', 'selected');
                    }
                    grp.append(opt);
                });
                select.append(grp);
            });
            select.trigger('change.select2');
            updateDayVendorDropdowns();
        }

        function handleCategoryChange() {
            const categories = $('#categories').val() || [];

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
        }

        // Cover Image Async Uploader
        function uploadCoverImage(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = e => {
                    $('#hero-preview img').attr('src', e.target.result);
                    $('#hero-preview').removeClass('d-none');
                };
                reader.readAsDataURL(input.files[0]);

                const file = input.files[0];
                const formData = new FormData();
                formData.append('image', file);

                $('#cover-upload-text').html('<i class="bi bi-hourglass-split text-primary fs-3"></i><p class="small text-muted mt-2">Uploading image...</p>');

                $.ajax({
                    url: '{{ route("admin.upload.image") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success && response.path) {
                            $('#image_path').val(response.path);
                            $('#cover-upload-text').html('<i class="bi bi-check-circle-fill text-success fs-3"></i><p class="small text-muted mt-2">Cover photo uploaded successfully</p>');
                        } else {
                            Swal.fire('Error', 'Image upload failed', 'error');
                            clearHeroPreview();
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Error uploading image', 'error');
                        clearHeroPreview();
                    }
                });
            }
        }

        function clearHeroPreview() {
            $('#hero-img').val('');
            $('#image_path').val('');
            $('#hero-preview img').attr('src', '');
            $('#hero-preview').addClass('d-none');
            $('#cover-upload-text').html('<i class="bi bi-image text-primary fs-1"></i><p class="small text-muted mt-2">Click to upload cover photo</p>');
        }

        // Gallery Images Async Uploader
        function uploadGallery(btn) {
            const files = $('#gallery-img')[0].files;
            if (!files || files.length === 0) return;

            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }

            $(btn).prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Uploading...');

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
                        $('#gallery-img').val('');
                        $(btn).hide().prop('disabled', false).html('<i class="bi bi-cloud-arrow-up me-1"></i> Upload Selected');
                    }
                },
                error: function (xhr) {
                    let errorMsg = 'Error uploading images';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', errorMsg, 'error');
                    $(btn).prop('disabled', false).html('<i class="bi bi-cloud-arrow-up me-1"></i> Upload Selected');
                }
            });
        }

        function addGalleryImage(url, path) {
            const previews = $('#gallery-previews');
            const imgHtml = `
                <div class="position-relative m-1" data-path="${path}" style="width: 80px; height: 80px;">
                    <img src="${url}" class="img-thumbnail" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                    <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 p-0 d-flex align-items-center justify-content-center" 
                            onclick="removeGalleryImage('${path}')" style="width: 20px; height: 20px; margin-top:-5px; margin-right:-5px;">
                        <i class="bi bi-x" style="font-size: 12px; line-height: 1;"></i>
                    </button>
                </div>
            `;
            previews.append(imgHtml);
        }

        function removeGalleryImage(path) {
            galleryImages = galleryImages.filter(p => p !== path);
            $(`[data-path="${path}"]`).remove();
            updateGalleryPaths();
        }

        function updateGalleryPaths() {
            $('#gallery_paths').val(JSON.stringify(galleryImages));
        }

        // ----------------------------------------------------
        // STEP 2: JOURNEY MAP SCRIPTS (COST COMPONENTS & DAYS)
        // ----------------------------------------------------
        function addAmenity() {
            const container = $('#addon-amenities-container');
            const id = amenityCounter++;
            let tpl = $('#amenity-tpl').html().replace(/{ID}/g, id);

            container.append(tpl);

            const row = $(`.amenity-item[data-amenity-id="amenity-${id}"]`);
            const supplierSelect = row.find('.amenity-supplier');

            // Populate supplier options
            supplierSelect.empty().append('<option value="">Select Supplier</option>');
            allSuppliers.forEach(s => {
                supplierSelect.append(`<option value="${s.id}" data-type="${s.type}">${s.name} (${s.type})</option>`);
            });
        }

        function removeAmenity(amenityId) {
            $(`.amenity-item[data-amenity-id="${amenityId}"]`).remove();
            updateOverallPricing();
            updateDayVendorDropdowns();
        }

        function handleSupplierChange(amenityId, select) {
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

            // Adjust calculator layouts dynamically
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

            // Fetch supplier assets via API
            assetSelect.html('<option value="">Loading...</option>').attr('disabled', true);

            $.get(`/api/inventory/suppliers/${supplierId}/assets`, function (data) {
                assetSelect.html('<option value="">Select Item</option>').removeAttr('disabled');

                if (data.type === 'hotel') {
                    let foundAnyRoom = false;
                    data.assets.forEach(hotel => {
                        if (hotel.rooms && hotel.rooms.length > 0) {
                            hotel.rooms.forEach(room => {
                                foundAnyRoom = true;
                                assetSelect.append(`<option value="${room.id}" data-price="${room.base_price}" data-name="${hotel.name} - ${room.room_type}">${room.room_type} (${hotel.name}) - RM${room.base_price}</option>`);
                            });
                        } else {
                            assetSelect.append(`<option value="" disabled>${hotel.name} (No Rooms Added Yet)</option>`);
                        }
                    });
                    if (!foundAnyRoom && data.assets.length === 0) {
                        assetSelect.append('<option value="" disabled>No Hotels/Rooms found</option>');
                    }
                } else if (data.type === 'transport') {
                    data.assets.forEach(asset => {
                        const displayName = `${asset.vehicle_type} (${asset.name})`;
                        assetSelect.append(`<option value="${asset.id}" data-price="${asset.base_price || 0}" data-name="${displayName}">${displayName} - RM${asset.base_price || 0}</option>`);
                    });
                } else if (data.type === 'activity' || data.type === 'tickets' || data.type === 'entry_tickets') {
                    data.assets.forEach(asset => {
                        const name = asset.name || asset.attraction_name || 'Unnamed Item';
                        const adultPrice = asset.base_price || asset.adult_price || 0;
                        const childPrice = asset.child_price || 0;
                        assetSelect.append(`<option value="${asset.id}" data-adultprice="${adultPrice}" data-childprice="${childPrice}" data-name="${name}">${name} - Ad: RM${adultPrice} / Ch: RM${childPrice}</option>`);
                    });
                    if (data.extra_assets) {
                        data.extra_assets.forEach(asset => {
                            assetSelect.append(`<option value="${asset.id}" data-adultprice="${asset.adult_price || 0}" data-childprice="${asset.child_price || 0}" data-name="${asset.attraction_name}">${asset.attraction_name} - Ad: RM${asset.adult_price || 0} / Ch: RM${asset.child_price || 0}</option>`);
                        });
                    }
                } else if (data.assets && Array.isArray(data.assets)) {
                    data.assets.forEach(asset => {
                        const name = asset.name || asset.attraction_name || 'Unnamed Item';
                        const price = asset.base_price || asset.price || asset.adult_price || 0;
                        assetSelect.append(`<option value="${asset.id}" data-price="${price}" data-name="${name}">${name} - RM${price}</option>`);
                    });
                }
            });
        }

        function handleAssetChange(amenityId, select) {
            const itemRow = $(`.amenity-item[data-amenity-id="${amenityId}"]`);
            const selected = $(select).find('option:selected');
            const name = selected.data('name');

            if (name) itemRow.find('.amenity-name').val(name);

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
        }

        function calcAmenityTotal(amenityId) {
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
        }

        function updateOverallPricing() {
            let calcNetPrice = 0;
            let html = '';

            $('.amenity-item').each(function () {
                const name = $(this).find('.amenity-name').val() || 'Unnamed Component';
                const total = parseFloat($(this).find('.amenity-total-price').val()) || 0;
                calcNetPrice += total;

                if (total > 0) {
                    html += `<div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">${name}</span>
                                <span class="fw-bold text-dark">RM ${total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                             </div>`;
                }
            });

            $('#cost-breakdown').html(html || '<p class="text-muted italic">No components added yet</p>');
            $('#net-total').text(`RM ${calcNetPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
            $('#net_price').val(calcNetPrice.toFixed(2));

            calculateSellingPrice();
        }

        // ----------------------------------------------------
        // ITINERARY BUILDER SCRIPTS
        // ----------------------------------------------------
        function addItineraryDay() {
            itineraryDayCount++;
            let tpl = $('#day-tpl').html().replace(/{N}/g, itineraryDayCount);

            const card = $(tpl).appendTo('#itinerary-timeline');

            // Init multi-select elements
            card.find('.day-destinations').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select primary destinations',
                width: '100%'
            });

            card.find('.day-meals').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select meals',
                width: '100%'
            });

            card.find('.day-activities').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select activities',
                width: '100%'
            });

            updateDuration();
            updateSingleItineraryDestinations(card);
            updateSingleItineraryDropdowns(card);
        }

        function removeItineraryDay(dayId) {
            if ($('.itinerary-day-card').length === 1) {
                Swal.fire('Alert', 'Your itinerary must have at least one day.', 'warning');
                return;
            }
            $(`#${dayId}`).fadeOut(300, function () {
                $(this).remove();
                reorderDays();
            });
        }

        function reorderDays() {
            itineraryDayCount = 0;
            $('.itinerary-day-card').each(function (i) {
                itineraryDayCount = i + 1;
                $(this).attr('id', `day-${itineraryDayCount}`).attr('data-day', itineraryDayCount);
                $(this).find('.day-count').text(itineraryDayCount);
                $(this).find('button[onclick^="removeItineraryDay"]').attr('onclick', `removeItineraryDay('day-${itineraryDayCount}')`);
            });
            updateDuration();
        }

        function updateDuration() {
            let d = $('.itinerary-day-card').length;
            $('#dur-count').text(`${d} Days / ${d > 0 ? d - 1 : 0} Nights`);
        }

        function toggleCustomHotel(btn) {
            const row = $(btn).closest('.itinerary-day-card').find('.custom-hotel-row');
            row.toggle();
            if (row.is(':visible')) {
                row.find('input').focus();
                $(btn).addClass('btn-primary').removeClass('btn-outline-primary').html('<i class="bi bi-x"></i> Cancel');
            } else {
                row.find('input').val('');
                $(btn).removeClass('btn-primary').addClass('btn-outline-primary').html('<i class="bi bi-plus"></i> Custom');
            }
        }

        function toggleCustomTransport(btn) {
            const row = $(btn).closest('.itinerary-day-card').find('.custom-transport-row');
            row.toggle();
            if (row.is(':visible')) {
                row.find('input').focus();
                $(btn).addClass('btn-warning').removeClass('btn-outline-warning').html('<i class="bi bi-x"></i> Cancel');
            } else {
                row.find('input').val('');
                $(btn).removeClass('btn-warning').addClass('btn-outline-warning').html('<i class="bi bi-plus"></i> Custom');
            }
        }

        // Linking components to Day Selects
        function updateDayVendorDropdowns() {
            $('.itinerary-day-card').each(function () {
                updateSingleItineraryDropdowns($(this));
            });
        }

        function updateSingleItineraryDropdowns(card) {
            const hotels = [];
            const transports = [];
            const others = [];

            $('.amenity-item').each(function () {
                const type = $(this).find('.amenity-type').val();
                const name = $(this).find('.amenity-name').val();
                const assetId = $(this).find('.amenity-asset').val();

                if (name && assetId) {
                    const val = type + '_' + assetId;
                    const text = name;
                    if (type === 'hotel') hotels.push({ val, text });
                    else if (type === 'transport') transports.push({ val, text });
                    else others.push({ val, text });
                }
            });

            const dayHotel = card.find('.day-hotel');
            const dayTrans = card.find('.day-transport');
            const dayActs = card.find('.day-activities');

            const curHotel = dayHotel.val();
            const curTrans = dayTrans.val();
            const curActs = dayActs.val() || [];

            dayHotel.html('<option value="">— Select hotel from components —</option>');
            hotels.forEach(h => dayHotel.append(`<option value="${h.val}">${h.text}</option>`));
            dayHotel.val(curHotel);

            dayTrans.html('<option value="">— Select transport from components —</option>');
            transports.forEach(t => dayTrans.append(`<option value="${t.val}">${t.text}</option>`));
            dayTrans.val(curTrans);

            dayActs.empty();
            others.forEach(o => dayActs.append(`<option value="${o.val}">${o.text}</option>`));
            dayActs.val(curActs).trigger('change.select2');
        }

        function updateAllItineraryDestinations() {
            $('.itinerary-day-card').each(function () {
                updateSingleItineraryDestinations($(this));
            });
        }

        function updateSingleItineraryDestinations(card) {
            const destSelect = card.find('.day-destinations');
            const selected = destSelect.val() || [];

            destSelect.empty();
            currentCountryDestinations.forEach(dest => {
                let isSelected = selected.includes(dest.id.toString()) ? 'selected' : '';
                destSelect.append(`<option value="${dest.id}" ${isSelected}>${dest.city} - ${dest.name}</option>`);
            });
            destSelect.trigger('change.select2');
        }

        // ----------------------------------------------------
        // STEP 3: COMMERCIALS SCRIPTS
        // ----------------------------------------------------
        function calculateSellingPrice() {
            const netPrice = parseFloat($('#net_price').val()) || 0;
            const markupPct = parseFloat($('#markup_percentage').val()) || 0;

            const markupAmount = (netPrice * markupPct) / 100;
            const sellingPrice = netPrice + markupAmount;

            $('#price').val(Math.round(sellingPrice));
            $('#foot-price').text(`RM ${Math.round(sellingPrice).toLocaleString()}`);

            // FX matrix previews
            const fxRates = { USD: 4.70, SGD: 3.48, AED: 1.28, INR: 0.056 };
            let matrixHtml = '';
            Object.keys(fxRates).forEach(currency => {
                const converted = (sellingPrice / fxRates[currency]).toFixed(2);
                matrixHtml += `<div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold opacity-75">${currency} Preview</span>
                                <span class="fw-black fs-5">${converted}</span>
                               </div>`;
            });
            $('#fx-matrix').html(matrixHtml);
        }

        function reverseCalculateMarkup() {
            const sellingPrice = parseFloat($(this).val()) || 0;
            const netPrice = parseFloat($('#net_price').val()) || 0;

            if (netPrice > 0 && sellingPrice >= netPrice) {
                const markupAmount = sellingPrice - netPrice;
                const markupPct = (markupAmount / netPrice) * 100;
                $('#markup_percentage').val(markupPct.toFixed(2));
                $('#foot-price').text(`RM ${Math.round(sellingPrice).toLocaleString()}`);
            }
        }

        // ----------------------------------------------------
        // STEP 4: COMPLIANCE SCRIPTS
        // ----------------------------------------------------
        let inclusionCounter = 0;
        let exclusionCounter = 0;

        function addInclusionRow(val = '') {
            const container = $('#inclusions-container');
            const id = inclusionCounter++;
            const html = `
                <div class="input-group mb-2 inclusion-row" id="inc-row-${id}">
                    <input type="text" name="inclusions[]" class="form-control premium-input" value="${val}" placeholder="Ex: Daily Buffet Breakfast Included">
                    <button type="button" class="btn btn-outline-danger" onclick="$('#inc-row-${id}').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.append(html);
        }

        function addExclusionRow(val = '') {
            const container = $('#exclusions-container');
            const id = exclusionCounter++;
            const html = `
                <div class="input-group mb-2 exclusion-row" id="exc-row-${id}">
                    <input type="text" name="exclusions[]" class="form-control premium-input" value="${val}" placeholder="Ex: Visa Fees or Flight Surcharges">
                    <button type="button" class="btn btn-outline-danger" onclick="$('#exc-row-${id}').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.append(html);
        }

        // ----------------------------------------------------
        // FORM SUBMISSION (IMMEDIATE SAVE)
        // ----------------------------------------------------
        function submitPackageForm() {
            // Build description field from Quill
            const descHtml = quillEditors['desc'] ? quillEditors['desc'].root.innerHTML : '';
            $('#description').val(descHtml);

            // Pre-validate all steps
            let allErrors = [];
            for (let i = 1; i <= 4; i++) {
                allErrors = allErrors.concat(validateStep(i));
            }

            if (allErrors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Please correct validation errors',
                    html: `<div class="text-start small">${allErrors.join('<br>')}</div>`,
                    confirmButtonColor: '#1e293b'
                });
                return;
            }

            // Collect Addon Amenities (Cost Components)
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

            // Collect Day-by-Day Itinerary
            const itineraryArr = [];
            $('.itinerary-day-card').each(function () {
                const dayCard = $(this);
                const customHotel = dayCard.find('.day-custom-hotel').val() || '';
                const customTransport = dayCard.find('.day-custom-transport').val() || '';

                itineraryArr.push({
                    day: parseInt(dayCard.find('.day-count').text()),
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

            // Pack full request data
            const payload = {
                _token: '{{ csrf_token() }}',
                destination_id: $('#destination_id').val() || null,
                supplier_ids: $('#supplier_ids').val() || [],
                categories: $('#categories').val() || [],
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
                price: parseFloat($('#price').val()),
                discount_price: $('#discount_price').val() ? parseFloat($('#discount_price').val()) : null,
                price_2_6: $('#price_2_6').val() ? parseFloat($('#price_2_6').val()) : null,
                price_6_10: $('#price_6_10').val() ? parseFloat($('#price_6_10').val()) : null,
                currency: $('#currency').val() || 'MYR',
                announcement_date: $('#announcement_date').val() || null,
                total_pax: $('#total_pax').val() ? parseInt($('#total_pax').val()) : null,
                duration_days: $('.itinerary-day-card').length,
                duration_nights: Math.max(0, $('.itinerary-day-card').length - 1),
                image: $('#image_path').val() || null,
                gallery: galleryImages,
                itinerary: itineraryArr.length > 0 ? itineraryArr : null,
                is_featured: $('#is_featured').is(':checked') ? 1 : 0,
                is_active: $('#is_active').is(':checked') ? 1 : 0,
                inclusions: $('input[name="inclusions[]"]').map(function () { return $(this).val(); }).get().filter(v => v.trim() !== ''),
                exclusions: $('input[name="exclusions[]"]').map(function () { return $(this).val(); }).get().filter(v => v.trim() !== ''),
                meta_title: $('#meta_title').val() || null,
                meta_description: $('#meta_description').val() || null,
                meta_keywords: $('#meta_keywords').val() || null
            };

            Swal.fire({
                title: 'Creating Group Package...',
                text: 'Syncing your group configuration and vendor allocations.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '{{ route("admin.group-packages.store") }}',
                type: 'POST',
                data: payload,
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Group Package Created!',
                        text: 'Your collaborative package template is now live.'
                    }).then(() => {
                        window.location.href = '/admin/group-package-itineraries/' + response.id + '/edit';
                    });
                },
                error: function (xhr) {
                    let errorMsg = 'Error saving group package';
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Configuration Error',
                        html: `<div class="text-start small">${errorMsg}</div>`,
                        confirmButtonColor: '#1e293b'
                    });
                }
            });
        }
    </script>
@endpush