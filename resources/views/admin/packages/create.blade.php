@extends('layouts.admin')

@section('title', 'Create Premium Holiday Package')

@push('styles')
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

        .day-builder-help {
            background: linear-gradient(135deg, #f0f4ff, #f8fbff);
            border: 1px solid #dfe7ff;
            border-radius: 18px;
            color: #526078;
            padding: 1rem 1.25rem;
        }

        .inventory-actions { display: flex; flex-wrap: wrap; gap: .55rem; }
        .inventory-action {
            align-items: center; background: white; border: 1px solid #dfe5ef; border-radius: 12px;
            color: #475569; display: inline-flex; font-size: .78rem; font-weight: 700; gap: .4rem; padding: .6rem .8rem;
        }
        .inventory-action:hover { border-color: #8aa8ff; color: #0052cc; transform: translateY(-1px); }
        .services-empty { border: 1px dashed #cfd8e6; border-radius: 14px; color: #94a3b8; font-size: .8rem; padding: 1rem; text-align: center; }
        .day-notes { background: #fbfcfe; border: 1px solid #edf0f5; border-radius: 16px; }

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
                <h1 class="fw-black text-dark tracking-tight mb-1">New Holiday Package</h1>
                <p class="text-muted mb-0">Crafting premium travel experiences</p>
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
                <div class="step-label">Fundamentals</div>
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

        <form id="package-wizard-form" enctype="multipart/form-data">
            <div class="wizard-card">
                <div class="wizard-body p-4 p-md-5">
                    <!-- STEP 1: FUNDAMENTALS -->
                    <div class="form-step active" id="step-1">
                        <div class="mb-5">
                            <label class="premium-label fs-5">Package Title</label>
                            <input type="text" name="name" class="form-control premium-input form-control-lg fs-4 fw-bold"
                                placeholder="Ex: Luxury Escape to Maldives" required>
                            <p class="small text-muted mt-2">This is the public name of your holiday package.</p>
                        </div>

                        <div class="row g-5">
                            <div class="col-md-8">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="premium-label">Country</label>
                                        <select name="country_id" id="country_id" class="form-select select2" required>
                                            <option value="" data-country-name="">Select Country</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" data-country-name="{{ $country->name }}">
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="premium-label">Primary Destinations</label>
                                        <select name="destination_ids[]" id="destination_ids" class="form-select select2"
                                            multiple required data-placeholder="Select Primary Cities">
                                            @foreach($destinations as $dest)
                                                <option value="{{ $dest->id }}" data-country="{{ $dest->country }}">
                                                    {{ $dest->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="premium-label">Additional Countries Covered</label>
                                        <div class="row g-2" style="max-height: 120px; overflow-y: auto;">
                                            @foreach($countries as $c)
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="country_ids[]"
                                                            value="{{ $c->id }}" id="pkg_country_{{ $c->id }}">
                                                        <label class="form-check-label small"
                                                            for="pkg_country_{{ $c->id }}">{{ $c->name }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="premium-label">Package Category</label>
                                        <select name="package_category" class="form-select premium-input">
                                            <option value="Honeymoon">Honeymoon</option>
                                            <option value="Family">Family Holiday</option>
                                            <option value="Budget">Budget Friendly</option>
                                            <option value="Standard" selected>Standard Comfort</option>
                                            <option value="Premium">Premium Luxury</option>
                                            <option value="Adventure">Adventure Tour</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-4 mt-1">
                                    <div class="col-md-12">
                                        <label class="premium-label">Source Vendors (Suppliers)</label>
                                        <select name="supplier_ids[]" id="supplier_ids" class="form-select select2" multiple
                                            data-placeholder="Select vendors for this package...">
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    data-country="{{ $supplier->destination->country ?? '' }}">
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Select all vendors providing services for this
                                            package.</small>
                                    </div>
                                </div>

                                <div class="mt-5">
                                    <label class="premium-label">Core Highlights</label>
                                    <div id="highlight-editor-container"
                                        style="height: 200px; border-radius: 16px; background: #f8fafc;"></div>
                                </div>

                                <div class="row g-4 mt-4">
                                    <div class="col-md-4">
                                        <label class="premium-label">Guest Limit (Min)</label>
                                        <input type="number" name="min_pax" class="form-control premium-input" value="1">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="premium-label">Guest Limit (Max)</label>
                                        <input type="number" name="max_pax" class="form-control premium-input" value="10">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="premium-label">Flight Included?</label>
                                        <div class="d-flex gap-3 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="includes_flight"
                                                    value="1">
                                                <label class="form-check-label small fw-bold">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="includes_flight"
                                                    value="0" checked>
                                                <label class="form-check-label small fw-bold">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-5">
                                    <label class="premium-label">Hero Cover Image</label>
                                    <label for="hero-img" class="dropzone-box w-100 mb-0" style="cursor: pointer;">
                                        <i class="bi bi-image text-primary fs-1"></i>
                                        <p class="small text-muted mt-2">Click to upload cover photo</p>
                                        <input type="file" id="hero-img" name="image" class="d-none" accept="image/*"
                                            onchange="previewHero(this)">
                                    </label>
                                    <div id="hero-preview" class="mt-3 d-none">
                                        <img src="" class="img-fluid rounded-4 shadow-sm border">
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="premium-label">Gallery Images</label>
                                    <label for="gallery-img" class="dropzone-box py-3 w-100 mb-0" style="cursor: pointer;">
                                        <i class="bi bi-images text-primary fs-3"></i>
                                        <p class="small text-muted mb-0">Add up to 10 photos</p>
                                        <input type="file" id="gallery-img" name="gallery[]" class="d-none" multiple
                                            accept="image/*" onchange="previewGallery(this)">
                                    </label>
                                    <div id="gallery-previews" class="d-flex flex-wrap mt-2"></div>
                                </div>

                                <div class="bg-light p-4 rounded-4 border">
                                    <h6 class="fw-bold mb-3">Attributes</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="is_featured" checked>
                                        <label class="form-check-label small fw-bold">Mark as Featured</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="is_trending">
                                        <label class="form-check-label small fw-bold">Trending Experience</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" checked>
                                        <label class="form-check-label small fw-bold">Live Status</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: JOURNEY MAP -->
                    <div class="form-step" id="step-2">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                            <div>
                                <h4 class="fw-black text-dark mb-1">Day-by-Day Itinerary</h4>
                                <p class="text-muted small mb-0">Build the journey in the same order your guest will experience it. Total: <span id="dur-count"
                                        class="fw-bold text-primary">0 Days</span></p>
                            </div>
                            <button type="button" class="btn btn-primary rounded-pill px-4" onclick="addDay()">
                                <i class="bi bi-plus-lg me-2"></i> Add another day
                            </button>
                        </div>

                        <div class="day-builder-help d-flex align-items-start gap-3 mb-4">
                            <i class="bi bi-lightbulb-fill text-warning fs-5"></i>
                            <div><strong class="d-block text-dark mb-1">Quick guide</strong><span class="small">Name the day, describe the guest experience, then add hotels, transport, tickets, activities or meals using the quick buttons.</span></div>
                        </div>

                        <div id="itinerary-timeline">
                            <!-- Dynamic Day Cards -->
                        </div>
                    </div>

                    <!-- STEP 3: COMMERCIALS -->
                    <div class="form-step" id="step-3">
                        <div class="row g-5">
                            <div class="col-md-7">
                                <div class="bg-white border rounded-4 p-5 shadow-sm mb-4">
                                    <h5 class="fw-black mb-4">Net Cost Aggregate</h5>
                                    <div id="cost-breakdown" class="mb-4">
                                        <!-- Dynamic Breakdown -->
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                                        <span class="fw-bold text-muted">Total Component Value</span>
                                        <span class="fs-2 fw-black text-dark" id="net-total">RM 0</span>
                                    </div>
                                </div>

                                {{-- Price Mode Toggle --}}
                                <div class="bg-light p-3 rounded-3 border mb-4 d-flex align-items-center gap-3">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="direct-price-toggle"
                                            onchange="togglePriceMode(this)">
                                        <label class="form-check-label fw-bold small" for="direct-price-toggle">Enter Direct
                                            Price</label>
                                    </div>
                                    <span class="text-muted small" id="price-mode-hint">Auto-calculating from services
                                        below</span>
                                </div>

                                <div id="auto-price-section">
                                    <div class="row g-4 mt-2">
                                        <div class="col-md-6">
                                            <label class="premium-label">Service Markup (%)</label>
                                            <input type="number" name="markup_percentage" id="markup-in"
                                                class="form-control premium-input" value="10" oninput="calculateRates()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label">Government GST (%)</label>
                                            <input type="number" name="gst_percentage" id="gst-in"
                                                class="form-control premium-input" value="5" oninput="calculateRates()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label">TCS (If applicable %)</label>
                                            <input type="number" name="tcs_percentage" id="tcs-in"
                                                class="form-control premium-input" value="0" oninput="calculateRates()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label">Final Selling Rate (Auto-Calculated)</label>
                                            <input type="number" name="price" id="selling-in"
                                                class="form-control premium-input form-control-lg fw-black text-primary"
                                                readonly>
                                            <small class="text-muted">Formula: (Cost + Markup) + Taxes</small>
                                        </div>
                                    </div>
                                </div>

                                <div id="direct-price-section" style="display:none;">
                                    <div class="row g-4 mt-2">
                                        <div class="col-md-6">
                                            <label class="premium-label">Direct Selling Price (RM)</label>
                                            <input type="number" name="price" id="direct-price-in"
                                                class="form-control premium-input form-control-lg fw-black text-primary"
                                                placeholder="Enter price directly" min="0" step="0.01"
                                                oninput="onDirectPriceChange()">
                                            <small class="text-muted">Enter the final price when you don't have itemised
                                                services.</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label">Service Markup (%)</label>
                                            <input type="number" name="markup_percentage" class="form-control premium-input"
                                                placeholder="Optional" value="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label">Government GST (%)</label>
                                            <input type="number" name="gst_percentage" class="form-control premium-input"
                                                placeholder="Optional" value="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label">TCS (If applicable %)</label>
                                            <input type="number" name="tcs_percentage" class="form-control premium-input"
                                                placeholder="Optional" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="card border-0 bg-primary text-white rounded-4 shadow-lg p-4">
                                    <div class="card-body">
                                        <h5 class="fw-bold mb-4">Global Matrix</h5>
                                        <div id="fx-matrix" class="space-y-3">
                                            <!-- FX Previews -->
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
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="premium-label">Package Inclusions</label>
                                <div id="inc-editor-container"
                                    style="height: 200px; border-radius: 16px; background: #f8fafc;"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="premium-label">Package Exclusions</label>
                                <div id="exc-editor-container"
                                    style="height: 200px; border-radius: 16px; background: #f8fafc;"></div>
                            </div>
                        </div>

                        <div class="row g-4 mt-5">
                            <div class="col-md-6">
                                <label class="premium-label">Cancellation Terms</label>
                                <textarea name="cancellation_policy" class="form-control premium-input" rows="5"
                                    placeholder="Define refund brackets..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="premium-label">Terms & Conditions</label>
                                <textarea name="terms" class="form-control premium-input" rows="5"
                                    placeholder="Legal fine print..."></textarea>
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
                                            <input type="text" name="meta_title" class="form-control premium-input"
                                                placeholder="SEO optimized title">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label small">Meta Keywords</label>
                                            <input type="text" name="meta_keywords" class="form-control premium-input"
                                                placeholder="travel, maldives, luxury, tour">
                                        </div>
                                        <div class="col-12">
                                            <label class="premium-label small">Meta Description</label>
                                            <textarea name="meta_description" class="form-control premium-input" rows="3"
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
                        <div class="price-tag d-none d-lg-flex align-items-center gap-3">
                            <div>
                                <span class="small fw-bold text-muted">LIVE QUOTE:</span>
                                <span class="fw-black text-primary fs-5" id="foot-price">RM 0</span>
                            </div>
                            <div class="vr"></div>
                            <div id="profit-display" style="display:none;">
                                <span class="small fw-bold text-muted">PROFIT:</span>
                                <span class="fw-black fs-6" id="foot-profit" style="color:#198754;">—</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success rounded-pill px-5 py-3 fw-bold" id="btn-save-immediate"
                            onclick="submitPackage()">
                            Save Package <i class="bi bi-check-circle ms-2"></i>
                        </button>
                        <button type="button" class="btn btn-dark rounded-pill px-5 py-3 fw-bold" id="btn-next"
                            onclick="step(1)">
                            Next Step <i class="bi bi-chevron-right ms-2"></i>
                        </button>
                        <button type="button" class="btn btn-publish px-5 py-3" id="btn-publish" style="display:none;"
                            onclick="submitPackage()">
                            FINISH & PUBLISH <i class="bi bi-rocket-takeoff ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Template: Day Card -->
    <template id="day-tpl">
        <div class="itinerary-day-card" data-day="{N}">
            <div class="day-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="day-count">{N}</div>
                    <div><div class="small text-muted fw-bold">DAY {N} SCHEDULE</div><input type="text" class="form-control border-0 bg-transparent fw-black fs-5 day-title-in p-0" placeholder="Arrival & Welcome" style="width:min(460px,60vw); outline:none;"></div>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm border-0 rounded-circle"
                    onclick="deleteDay(this)">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-8"><label class="premium-label">Day description</label><textarea class="form-control premium-input day-desc-in" rows="3" placeholder="Day description and itinerary flow..."></textarea></div>
                    <div class="col-md-4"><label class="premium-label">Overnight location</label><input type="text" class="form-control premium-input overnight-in" placeholder="City / hotel"></div>
                </div>

                <div class="bg-light p-4 rounded-4 border">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom flex-wrap gap-2">
                        <label class="premium-label mb-0"><i class="bi bi-box-seam me-2 text-primary"></i>Operations Inventory & Supplier Bookings</label>
                        <div class="btn-group btn-group-sm flex-wrap">
                            <button type="button" class="btn btn-outline-primary" onclick="openInventoryModal({I}, 'hotels')"><i class="bi bi-building"></i> + Hotel</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="openInventoryModal({I}, 'transports')"><i class="bi bi-car-front"></i> + Transport</button>
                            <button type="button" class="btn btn-outline-danger" onclick="openInventoryModal({I}, 'tickets')"><i class="bi bi-ticket-perforated"></i> + Ticket</button>
                            <button type="button" class="btn btn-outline-info" onclick="openInventoryModal({I}, 'spots')"><i class="bi bi-geo-alt"></i> + Spot</button>
                            <button type="button" class="btn btn-outline-warning text-dark" onclick="openInventoryModal({I}, 'activities')"><i class="bi bi-lightning"></i> + Activity</button>
                            <button type="button" class="btn btn-outline-success" onclick="openInventoryModal({I}, 'meals')"><i class="bi bi-cup-hot"></i> + Meal</button>
                        </div>
                    </div>
                    <div class="day-items-container" id="day-items-{I}"><div class="services-empty"><i class="bi bi-plus-circle me-1"></i>Selected inventory items will appear here.</div></div>
                </div>

                <div class="day-notes row g-3 mt-4 p-3">
                    <div class="col-md-4"><label class="small fw-bold text-warning mb-2"><i class="bi bi-star me-1"></i>Highlights</label><textarea class="form-control day-highlights-in" rows="2" placeholder="One highlight per line"></textarea></div>
                    <div class="col-md-4"><label class="small fw-bold text-success mb-2"><i class="bi bi-check-lg me-1"></i>Inclusions</label><textarea class="form-control day-inclusions-in" rows="2" placeholder="One inclusion per line"></textarea></div>
                    <div class="col-md-4"><label class="small fw-bold text-danger mb-2"><i class="bi bi-x-lg me-1"></i>Exclusions</label><textarea class="form-control day-exclusions-in" rows="2" placeholder="One exclusion per line"></textarea></div>
                </div>
            </div>
        </div>
    </template>
    @include('admin.itineraries.partials.inventory_modal_and_scripts')
@endsection

@push('scripts')
    <!-- Quill Editor Assets (if not in layout) -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let activeStep = 1;
        let quillEditors = {};

        // Inventory Data
        @php
            $hotelList = $hotels->map(function ($h) {
                return ['id' => $h->id, 'name' => $h->name, 'supplier_id' => $h->supplier_id, 'country' => $h->supplier->destination->country ?? '', 'price' => $h->roomTypes->avg('base_price') ?? 0];
            });
            $transportList = $transportRoutes->map(function ($t) {
                return ['id' => $t->id, 'name' => $t->name, 'supplier_id' => $t->supplier_id, 'country' => $t->supplier->destination->country ?? '', 'price' => $t->base_price];
            });
            $activityList = $activities->map(function ($a) {
                return ['id' => $a->id, 'name' => $a->name, 'supplier_id' => $a->supplier_id, 'country' => $a->supplier->destination->country ?? '', 'price' => $a->base_price];
            });
            $ticketList = $entryTickets->map(function ($et) {
                return ['id' => $et->id, 'name' => $et->attraction_name, 'supplier_id' => $et->supplier_id, 'country' => $et->supplier->destination->country ?? '', 'price' => $et->adult_price];
            });
            $mealList = $meals->map(function ($m) {
                return ['id' => $m->id, 'name' => $m->name, 'supplier_id' => $m->supplier_id, 'country' => $m->supplier->destination->country ?? '', 'price' => $m->price];
            });
            $touristSpotList = $touristSpots->map(function ($ts) {
                return ['id' => $ts->id, 'name' => $ts->name, 'supplier_id' => $ts->supplier_id, 'country' => $ts->country->name ?? $ts->destination->country ?? '', 'price' => 0];
            });
        @endphp

        const inventory = {
            hotel: @json($hotelList),
            transport: @json($transportList),
            activity: @json($activityList),
            ticket: @json($ticketList),
            meal: @json($mealList),
            spot: @json($touristSpotList)
        };

        $(document).ready(function () {
            $('.select2').select2({ theme: 'bootstrap-5' });

            // Country to Destination Cascading
            $('#country_id').on('change', function () {
                let selectedCountryName = $(this).find(':selected').data('country-name') || '';
                let destSelect = $('#destination_ids');

                // Clear current selection
                destSelect.val(null).trigger('change');

                // Filter destinations by country string match
                destSelect.find('option').each(function () {
                    let optCountry = $(this).data('country'); // country string e.g. "Thailand"
                    if (!selectedCountryName || !optCountry || optCountry === selectedCountryName || $(this).val() === "") {
                        $(this).prop('disabled', false);
                    } else {
                        $(this).prop('disabled', true);
                    }
                });

                // Re-init select2 to refresh disabled states
                destSelect.select2({ theme: 'bootstrap-5' });

                // Filter suppliers by country
                let supplierSelect = $('#supplier_ids');
                let currentSuppliers = supplierSelect.val() || [];
                supplierSelect.find('option').each(function () {
                    let sCountry = $(this).data('country');
                    if (!selectedCountryName || !sCountry || sCountry === selectedCountryName || $(this).val() === '') {
                        $(this).prop('disabled', false);
                    } else {
                        $(this).prop('disabled', true);
                    }
                });
                supplierSelect.val(currentSuppliers.filter(v => {
                    return !supplierSelect.find('option[value="' + v + '"]').prop('disabled');
                })).trigger('change');
                supplierSelect.select2({ theme: 'bootstrap-5' });

                // Filter tourist spots in all day cards by country
                filterAllSpotsByCountry(selectedCountryName);
            });

            quillEditors['highlight'] = initQuillEditor('#highlight-editor-container', 200);
            quillEditors['inc'] = initQuillEditor('#inc-editor-container', 200);
            quillEditors['exc'] = initQuillEditor('#exc-editor-container', 200);
            addDay();
        });

        function step(dir) {
            if (dir > 0) {
                let errors = validateStep(activeStep);
                if (errors.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Please complete the required fields',
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
            for (let i = 1; i < target; i++) $(`.step-item[data-step="${i}"]`).addClass('completed');
            $(`.step-item[data-step="${target}"]`).addClass('active');
            activeStep = target;
            $('#btn-prev').toggle(activeStep > 1);
            $('#btn-next').toggle(activeStep < 4);
            $('#btn-save-immediate').toggle(activeStep < 4);
            $('#btn-publish').toggle(activeStep === 4);
            if (activeStep === 3) calculateRates();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function validateStep(stepId) {
            let stepEl = document.getElementById(`step-${stepId}`);
            if (!stepEl) return [];

            let inputs = stepEl.querySelectorAll('input, select, textarea');
            let errors = [];

            const fieldLabels = {
                'name': 'Package Title',
                'country_id': 'Country',
                'destination_ids[]': 'Primary Destinations',
                'price': 'Final Selling Rate'
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
                if (input.value && !input.checkValidity()) {
                    let fieldName = fieldLabels[input.name] || input.placeholder || input.name || 'Field';
                    errors.push(`<b>${fieldName}</b>: ${input.validationMessage}`);
                }
            });

            return errors;
        }

        function filterAllSpotsByCountry(countryName) {
            $('.itinerary-day-card').each(function () {
                let spotsSelect = $(this).find('.select2-spots-multi');
                spotsSelect.find('option').each(function () {
                    let spotCountry = ($(this).data('country') || '').trim().toLowerCase();
                    if (!countryName || !spotCountry || spotCountry === countryName.trim().toLowerCase()) {
                        $(this).prop('disabled', false);
                    } else {
                        $(this).prop('disabled', true);
                    }
                });
                // Refresh select2
                let currentVal = spotsSelect.val();
                spotsSelect.select2({ theme: 'bootstrap-5', placeholder: spotsSelect.data('placeholder') });
                if (currentVal) {
                    let validVals = currentVal.filter(v => {
                        return !spotsSelect.find('option[value="' + v + '"]').prop('disabled');
                    });
                    spotsSelect.val(validVals).trigger('change');
                }
            });
        }

        function addDay() {
            let n = $('.itinerary-day-card').length + 1;
            let tpl = $('#day-tpl').html().replace(/{N}/g, n).replace(/{I}/g, n - 1);
            $(tpl).appendTo('#itinerary-timeline');
            updateDuration();
        }

        function addServiceRow(dayN, data = null, forcedType = null) {
            let tpl = $('#service-row-tpl').html();
            let container = $(`#services-day-${dayN}`);
            container.find('.services-empty').remove();
            container.append(tpl);
            let row = container.find('.service-row').last();
            row.find('.select2-vendor, .select2-service').select2({ theme: 'bootstrap-5' });

            if (data) {
                row.find('.service-type').val(data.type).trigger('change');
                row.find('.select2-vendor').val(data.supplier_id).trigger('change');
                setTimeout(() => {
                    row.find('.select2-service').val(data.id).trigger('change');
                }, 100);
            } else if (forcedType) {
                row.find('.service-type').val(forcedType).trigger('change');
            } else {
                row.find('.service-type').trigger('change');
            }
        }

        // Global vendors data for reconstruction
        const allVendors = @json($suppliers->map(function ($s) {
            return ['id' => $s->id, 'name' => $s->name];
        }));

        function onServiceTypeChange(el) {
            let row = $(el).closest('.service-row');
            let type = $(el).val();
            let vendorSelect = row.find('.select2-vendor');
            let selectedCountry = ($('#country_id').find(':selected').data('country-name') || '').trim().toLowerCase();

            // Filter inventory by country first
            let filteredItems = inventory[type];
            if (selectedCountry) {
                filteredItems = inventory[type].filter(i => !i.country || i.country.trim().toLowerCase() === selectedCountry);
            }

            // Find vendors who have items of this type (and country)
            let validVendorIds = [...new Set(filteredItems.map(i => i.supplier_id))];

            vendorSelect.empty().append('<option value="">Choose Vendor</option>');

            if (type === 'spot') {
                vendorSelect.append('<option value="none">No Vendor / Public Spot</option>');
            }

            allVendors.forEach(v => {
                if (validVendorIds.includes(v.id)) {
                    vendorSelect.append(`<option value="${v.id}">${v.name}</option>`);
                }
            });

            vendorSelect.trigger('change');
            row.find('.select2-service').empty().append('<option value="">Choose Service</option>').trigger('change');
        }

        function onVendorChange(el) {
            let row = $(el).closest('.service-row');
            let type = row.find('.service-type').val();
            let vendorId = $(el).val();
            let serviceSelect = row.find('.select2-service');
            let selectedCountry = $('#country_id').find(':selected').data('country-name') || '';
            serviceSelect.empty().append('<option value="">Choose Service</option>');
            if (type) {
                let items = [];
                if (type === 'spot' && (!vendorId || vendorId === 'none')) {
                    items = inventory[type];
                } else if (vendorId) {
                    items = inventory[type].filter(i => i.supplier_id == vendorId);
                }
                // Apply country filter
                if (selectedCountry) {
                    items = items.filter(i => !i.country || i.country === selectedCountry);
                }
                items.forEach(i => { serviceSelect.append(`<option value="${i.id}" data-price="${i.price}">${i.name}</option>`); });
            }
            serviceSelect.trigger('change');
        }

        function deleteDay(btn) {
            if ($('.itinerary-day-card').length === 1) return;
            $(btn).closest('.itinerary-day-card').fadeOut(300, function () {
                $(this).remove();
                reorderDays();
            });
        }

        function reorderDays() {
            $('.itinerary-day-card').each(function (i) {
                let n = i + 1;
                $(this).attr('data-day', n).find('.day-count').text(n);
                $(this).find('.day-items-container').attr('id', `day-items-${i}`);
                $(this).find('button[onclick^="openInventoryModal"]').each(function () { this.setAttribute('onclick', this.getAttribute('onclick').replace(/openInventoryModal\(\d+,/, `openInventoryModal(${i},`)); });
            });
            updateDuration();
            calculateRates();
        }

        function updateDuration() {
            let d = $('.itinerary-day-card').length;
            $('#dur-count').text(`${d} Days / ${d > 0 ? d - 1 : 0} Nights`);
        }

        function calculateRates() {
            let total = 0;
            let html = '';
            $('.itinerary-day-card').each(function () {
                let dNum = $(this).attr('data-day');
                $(this).find('.item-row').each(function () {
                    let type = $(this).find('input[name*="[item_type]"]').val() || 'service';
                    let title = $(this).find('input[name*="[title]"]').val() || 'Inventory item';
                    let p = parseFloat($(this).find('.item-rate-input').val()) || 0;
                    total += p;
                    let typeLabel = type.charAt(0).toUpperCase() + type.slice(1);
                    html += `<div class="d-flex justify-content-between mb-2"><span>Day ${dNum}: ${typeLabel} - ${title}</span><span class="fw-bold text-dark">RM ${p.toLocaleString()}</span></div>`;
                });
            });
            $('#cost-breakdown').html(html || '<p class="text-muted italic">No components added yet</p>');
            $('#net-total').text(`RM ${total.toLocaleString()}`);

            let mPercent = parseFloat($('#markup-in').val()) || 0;
            let gstPercent = parseFloat($('#gst-in').val()) || 0;
            let tcsPercent = parseFloat($('#tcs-in').val()) || 0;

            let markupAmount = total * (mPercent / 100);
            let subtotalWithMarkup = total + markupAmount;

            let gstAmount = subtotalWithMarkup * (gstPercent / 100);
            let tcsAmount = subtotalWithMarkup * (tcsPercent / 100);
            let totalTax = gstAmount + tcsAmount;

            let final = subtotalWithMarkup + totalTax;

            $('#selling-in').val(Math.round(final));
            $('#foot-price').text(`RM ${Math.round(final).toLocaleString()}`);

            // Show profit margin
            if (total > 0) {
                let profit = final - total;
                let profitPct = (profit / total) * 100;
                $('#profit-display').show();
                let color = profit >= 0 ? '#198754' : '#dc3545';
                $('#foot-profit').css('color', color).text(`RM ${Math.round(profit).toLocaleString()} (${profitPct.toFixed(1)}%)`);
            } else {
                $('#profit-display').hide();
            }

            // Add hidden fields for submission if not already there or update them
            if ($('#net-price-hidden').length === 0) {
                $('#package-wizard-form').append(`<input type="hidden" name="net_price" id="net-price-hidden">`);
                $('#package-wizard-form').append(`<input type="hidden" name="markup_amount" id="markup-amount-hidden">`);
                $('#package-wizard-form').append(`<input type="hidden" name="tax_amount" id="tax-amount-hidden">`);
            }
            $('#net-price-hidden').val(total);
            $('#markup-amount-hidden').val(markupAmount);
            $('#tax-amount-hidden').val(totalTax);

            const rates = { USD: 83.5, MYR: 17.8, AED: 22.8, SGD: 62.0 };
            let matrix = '';
            Object.keys(rates).forEach(c => { matrix += `<div class="d-flex justify-content-between align-items-center mb-3"><span class="fw-bold opacity-75">${c} Preview</span><span class="fw-black fs-5">${(final / rates[c]).toFixed(2)}</span></div>`; });
            $('#fx-matrix').html(matrix);
        }

        function togglePriceMode(toggle) {
            if (toggle.checked) {
                $('#auto-price-section').hide();
                $('#direct-price-section').show();
                $('#price-mode-hint').text('Entering price directly — no auto calculation');
                // Remove required from auto-calc selling input
                $('#selling-in').removeAttr('required').val('');
            } else {
                $('#auto-price-section').show();
                $('#direct-price-section').hide();
                $('#price-mode-hint').text('Auto-calculating from services below');
                $('#direct-price-in').val('');
                calculateRates();
            }
        }

        function onDirectPriceChange() {
            let val = parseFloat($('#direct-price-in').val()) || 0;
            $('#foot-price').text('RM ' + Math.round(val).toLocaleString());
        }

        function previewHero(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = e => { $('#hero-preview img').attr('src', e.target.result); $('#hero-preview').removeClass('d-none'); };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewGallery(input) {
            $('#gallery-previews').empty();
            if (input.files) {
                Array.from(input.files).forEach(f => {
                    let reader = new FileReader();
                    reader.onload = e => { $('#gallery-previews').append(`<img src="${e.target.result}" style="width:80px;height:80px;object-fit:cover;margin:5px;border-radius:12px;border:2px solid white;shadow:0 4px 10px rgba(0,0,0,0.1)">`); };
                    reader.readAsDataURL(f);
                });
            }
        }

        function submitPackage() {
            // Handle direct price mode
            let isDirectMode = $('#direct-price-toggle').is(':checked');
            if (isDirectMode) {
                let directPrice = parseFloat($('#direct-price-in').val()) || 0;
                if (directPrice <= 0) {
                    Swal.fire({ icon: 'error', title: 'Price Required', text: 'Please enter a direct selling price.' });
                    return;
                }
                // Sync price to the selling-in hidden value so form captures it
                $('#selling-in').val(directPrice);
            } else {
                calculateRates(); // Ensure rates and hidden fields are populated
            }
            let allErrors = [];
            for (let i = 1; i <= 4; i++) {
                allErrors = allErrors.concat(validateStep(i));
            }
            if (allErrors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Please complete the required fields',
                    html: `<div class="text-start small">${allErrors.join('<br>')}</div>`,
                    confirmButtonColor: '#1e293b'
                });
                return;
            }

            const formData = new FormData(document.getElementById('package-wizard-form'));
            let itinerary = [];
            $('.itinerary-day-card').each(function () {
                let day = { day_number: $(this).attr('data-day'), title: $(this).find('.day-title-in').val(), description: $(this).find('.day-desc-in').val(), overnight_location: $(this).find('.overnight-in').val(), highlights: $(this).find('.day-highlights-in').val(), inclusions: $(this).find('.day-inclusions-in').val(), exclusions: $(this).find('.day-exclusions-in').val(), meals: [], hotels: [], transports: [], activities: [], tickets: [], meals_list: [], spots: [], items: [] };
                $(this).find('.item-row').each(function () {
                    let type = $(this).find('input[name*="[item_type]"]').val();
                    let id = $(this).find('input[name*="[item_id]"]').val();
                    let item = { type, id, title: $(this).find('input[name*="[title]"]').val(), description: $(this).find('input[name*="[description]"]').val(), price: parseFloat($(this).find('.item-rate-input').val()) || 0 };
                    day.items.push(item);
                    if (!id) return;
                    if (type === 'hotel') day.hotels.push({ hotel_id: id });
                    else if (type === 'transport') day.transports.push({ transport_id: id });
                    else if (type === 'activity') day.activities.push({ activity_id: id });
                    else if (type === 'ticket') day.tickets.push({ ticket_id: id });
                    else if (type === 'meal') day.meals_list.push({ meal_id: id });
                    else if (type === 'spot') day.spots.push({ tourist_spot_id: id, hours: 1, price_per_hour: item.price });
                });
                itinerary.push(day);
            });
            formData.append('itinerary_data', JSON.stringify(itinerary));
            formData.append('short_description', quillEditors['highlight'] ? quillEditors['highlight'].root.innerHTML : '');
            formData.append('inclusions', quillEditors['inc'] ? quillEditors['inc'].root.innerHTML : '');
            formData.append('exclusions', quillEditors['exc'] ? quillEditors['exc'].root.innerHTML : '');
            formData.append('duration_days', $('.itinerary-day-card').length);
            formData.append('duration_nights', Math.max(0, $('.itinerary-day-card').length - 1));
            Swal.fire({ title: 'Finalizing Experience...', text: 'Checking your session and saving the journey', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            refreshSessionToken().done(token => {
                formData.set('_token', token);
                $.ajax({
                url: '{{ route("admin.packages.store") }}',
                type: 'POST',
                data: formData,
                headers: { 'X-CSRF-TOKEN': token },
                processData: false,
                contentType: false,
                success: (res) => { Swal.fire({ icon: 'success', title: 'Package Created!', text: 'Your multi-vendor experience is now live.' }).then(() => window.location.href = '{{ route("admin.packages.index") }}'); },
                error: (err) => {
                    let msg = 'Review your configuration and try again.';
                    if (err.status === 422 && err.responseJSON && err.responseJSON.errors) {
                        msg = Object.values(err.responseJSON.errors).flat().join('<br>');
                    } else if (err.responseJSON && (err.responseJSON.message || err.responseJSON.error)) {
                        msg = err.responseJSON.message || err.responseJSON.error;
                    } else if (err.responseText) {
                        try {
                            let parsed = JSON.parse(err.responseText);
                            msg = parsed.message || parsed.error || err.responseText;
                        } catch(e) {
                            msg = err.responseText.substring(0, 300);
                        }
                    } else {
                        msg = err.statusText || 'Server Error';
                    }
                    const expired = err.status === 419;
                    Swal.fire({ icon: 'error', title: expired ? 'Session expired' : 'Could not save package', html: `<div class="text-start small">${expired ? 'Your login session expired while this form was open. Open the admin panel in a new tab, sign in again, then return here and click Finish & Publish once more.' : msg}</div>` });
                }
                });
            }).fail(() => {
                Swal.fire({ icon: 'warning', title: 'Please sign in again', html: '<div class="text-start small">Your form is still here. Open the admin panel in a new tab, sign in, then return to this tab and submit again.</div>' });
            });
        }

        function refreshSessionToken() {
            return $.ajax({ url: '{{ route("admin.session-token") }}', type: 'GET', dataType: 'json', cache: false })
                .then(response => {
                    if (!response || !response.token) return $.Deferred().reject().promise();
                    $('meta[name="csrf-token"]').attr('content', response.token);
                    $('#package-wizard-form input[name="_token"]').val(response.token);
                    return response.token;
                });
        }
    </script>
@endpush
