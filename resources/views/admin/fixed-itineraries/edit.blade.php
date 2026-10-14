@extends('layouts.admin')

@section('title', 'Edit Fixed Itinerary - ' . $itinerary->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 font-weight-bold text-gray-800 mb-1">
            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Fixed Itinerary
        </h2>
        <p class="text-muted small mb-0">{{ $itinerary->title }} &bull; {{ $itinerary->duration }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.fixed-itineraries.show', $itinerary->id) }}" class="btn btn-outline-info">
            <i class="bi bi-eye me-1"></i> Preview
        </a>
        <a href="{{ route('admin.fixed-itineraries.pdf', $itinerary->id) }}" target="_blank" class="btn btn-outline-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('admin.fixed-itineraries.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.fixed-itineraries.update', $itinerary->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Main Form Left -->
        <div class="col-lg-8">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white font-weight-bold py-3">
                    <i class="bi bi-info-circle text-primary me-2"></i>Package Overview & Details
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Itinerary Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $itinerary->title) }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Primary Destination</label>
                            <select name="destination_id" id="destination_id" class="form-select">
                                <option value="">Select Destination</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" {{ old('destination_id', $itinerary->destination_id) == $dest->id ? 'selected' : '' }}>
                                        {{ $dest->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label font-weight-bold mb-0 text-nowrap">
                                    <i class="bi bi-buildings text-primary me-1"></i>Suppliers / Vendors
                                </label>
                                <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 fw-semibold text-nowrap" data-bs-toggle="modal" data-bs-target="#quickAddSupplierModal" style="font-size: 0.75rem;">
                                    <i class="bi bi-plus-lg me-1"></i>Custom Vendor
                                </button>
                            </div>

                            <!-- Compact Horizontal Category Filter Pills (No Wrapping, Single Line) -->
                            <div class="d-flex align-items-center gap-1 mb-1 pb-1 overflow-auto flex-nowrap" style="scrollbar-width: none; -ms-overflow-style: none;">
                                <button type="button" class="btn btn-xs rounded-pill btn-primary supplier-cat-filter px-2 py-0 text-nowrap" data-category="all" style="font-size: 0.72rem; line-height: 1.6;">All</button>
                                <button type="button" class="btn btn-xs rounded-pill btn-light text-dark border supplier-cat-filter px-2 py-0 text-nowrap" data-category="Hotel" style="font-size: 0.72rem; line-height: 1.6;">🏨 Hotels</button>
                                <button type="button" class="btn btn-xs rounded-pill btn-light text-dark border supplier-cat-filter px-2 py-0 text-nowrap" data-category="Transport" style="font-size: 0.72rem; line-height: 1.6;">🚗 Transport</button>
                                <button type="button" class="btn btn-xs rounded-pill btn-light text-dark border supplier-cat-filter px-2 py-0 text-nowrap" data-category="Entry Tickets" style="font-size: 0.72rem; line-height: 1.6;">🎟️ Tickets</button>
                                <button type="button" class="btn btn-xs rounded-pill btn-light text-dark border supplier-cat-filter px-2 py-0 text-nowrap" data-category="Activities" style="font-size: 0.72rem; line-height: 1.6;">🏄 Tours</button>
                                <button type="button" class="btn btn-xs rounded-pill btn-light text-dark border supplier-cat-filter px-2 py-0 text-nowrap" data-category="Other" style="font-size: 0.72rem; line-height: 1.6;">📦 Other</button>
                            </div>

                            @php
                                $selectedSuppliers = old('supplier_ids', $itinerary->supplier_ids ?: ($itinerary->supplier_id ? [$itinerary->supplier_id] : []));
                                $categoriesMap = [
                                    'Hotel' => ['icon' => '🏨', 'title' => 'Hotels & Accommodations', 'items' => []],
                                    'Transport' => ['icon' => '🚗', 'title' => 'Transports & Vehicles', 'items' => []],
                                    'Entry Tickets' => ['icon' => '🎟️', 'title' => 'Entry Tickets & Attractions', 'items' => []],
                                    'Activities' => ['icon' => '🏄', 'title' => 'Tours & Activities', 'items' => []],
                                    'Other' => ['icon' => '📦', 'title' => 'Other / General Vendors', 'items' => []],
                                ];
                                foreach($suppliers as $sup) {
                                    $lbl = $sup->category_label ?? 'Other';
                                    if (!isset($categoriesMap[$lbl])) {
                                        $lbl = 'Other';
                                    }
                                    $categoriesMap[$lbl]['items'][] = $sup;
                                }
                            @endphp

                            <select name="supplier_ids[]" id="supplier_ids" class="form-select select2" multiple data-placeholder="Select suppliers by category or type custom name...">
                                @foreach($categoriesMap as $catKey => $catData)
                                    @if(count($catData['items']) > 0)
                                        <optgroup label="{{ $catData['icon'] }} {{ $catData['title'] }} ({{ count($catData['items']) }})" data-category="{{ $catKey }}">
                                            @foreach($catData['items'] as $supplier)
                                                <option value="{{ $supplier->id }}" data-category="{{ $catKey }}" {{ is_array($selectedSuppliers) && in_array($supplier->id, $selectedSuppliers) ? 'selected' : '' }}>
                                                    {{ $supplier->name }} [{{ $supplier->type ?: $catKey }}]
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i>Categorized by Hotel, Transport, Entry Tickets. Type custom name or click <strong>+ Add Custom Supplier</strong>.
                            </small>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Duration (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', $itinerary->duration_days ?: 1) }}" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Duration (Nights) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_nights" class="form-control" value="{{ old('duration_nights', $itinerary->duration_nights ?: 0) }}" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Countries Covered</label>
                        <div class="row g-2 p-2 bg-light rounded border" style="max-height: 160px; overflow-y: auto;">
                            @php
                                $selectedCountries = old('country_ids', is_array($itinerary->country_ids) ? $itinerary->country_ids : []);
                            @endphp
                            @foreach($countries as $country)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="country_ids[]" value="{{ $country->id }}" id="country_{{ $country->id }}" {{ in_array($country->id, $selectedCountries) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="country_{{ $country->id }}">{{ $country->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="2" placeholder="Brief tagline or overview">{{ old('short_description', $itinerary->short_description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Itinerary Description</label>
                        <textarea name="itinerary_description" class="form-control" rows="4" placeholder="Detailed fixed itinerary narrative or highlights summary">{{ old('itinerary_description', $itinerary->itinerary_description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Package Highlights, Inclusions & Exclusions -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white font-weight-bold py-3">
                    <i class="bi bi-card-checklist text-primary me-2"></i>Highlights, Inclusions & Exclusions
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-warning">
                            <i class="bi bi-star-fill me-1"></i> Package Highlights (One item per line)
                        </label>
                        @php
                            $highlightsText = is_array($itinerary->highlights) ? implode("\n", $itinerary->highlights) : ($itinerary->highlights ?? '');
                        @endphp
                        <textarea name="highlights" class="form-control" rows="3" placeholder="e.g.&#10;Genting Cable Car Ride&#10;Kuala Lumpur City Iconic Photo-stops">{{ old('highlights', $highlightsText) }}</textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-success">
                                <i class="bi bi-check-circle-fill me-1"></i> Inclusions (One item per line)
                            </label>
                            @php
                                $inclusionsText = is_array($itinerary->inclusions) ? implode("\n", $itinerary->inclusions) : ($itinerary->inclusions ?? '');
                            @endphp
                            <textarea name="inclusions" class="form-control" rows="4" placeholder="e.g.&#10;Hotel Stay with Breakfast&#10;Airport Transfers & Ground Transport">{{ old('inclusions', $inclusionsText) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-danger">
                                <i class="bi bi-x-circle-fill me-1"></i> Exclusions (One item per line)
                            </label>
                            @php
                                $exclusionsText = is_array($itinerary->exclusions) ? implode("\n", $itinerary->exclusions) : ($itinerary->exclusions ?? '');
                            @endphp
                            <textarea name="exclusions" class="form-control" rows="4" placeholder="e.g.&#10;International Air Tickets&#10;Personal Expenses">{{ old('exclusions', $exclusionsText) }}</textarea>
                        </div>
                    </div>
                    <div>
                        <label class="form-label font-weight-bold text-secondary">
                            <i class="bi bi-shield-check me-1"></i> Terms & Conditions
                        </label>
                        <textarea name="terms_conditions" class="form-control" rows="2" placeholder="Booking terms, cancellation policies, etc.">{{ old('terms_conditions', $itinerary->terms_conditions) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Structured Day Activities -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <span class="font-weight-bold">
                        <i class="bi bi-calendar-week text-primary me-2"></i>Day-by-Day Itinerary Schedule
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addDayBtn">
                        <i class="bi bi-plus-lg me-1"></i> Add Day
                    </button>
                </div>
                <div class="card-body" id="daysContainer">
                    @php
                        $daysList = is_array($itinerary->days) && count($itinerary->days) > 0 ? $itinerary->days : [
                            ['day_number' => 1, 'title' => 'Arrival & Welcome', 'overnight_location' => '', 'description' => '', 'items' => []]
                        ];
                    @endphp

                    @foreach($daysList as $index => $rawDay)
                        @php
                            $day = (array) $rawDay;
                            $dayNum = $day['day_number'] ?? ($index + 1);
                            $dayTitle = $day['title'] ?? '';
                            $dayOvernight = $day['overnight_location'] ?? '';
                            $dayDesc = $day['description'] ?? '';
                            $dayHl = is_array($day['highlights'] ?? null) ? implode("\n", $day['highlights']) : ($day['highlights'] ?? '');
                            $dayInc = is_array($day['inclusions'] ?? null) ? implode("\n", $day['inclusions']) : ($day['inclusions'] ?? '');
                            $dayExc = is_array($day['exclusions'] ?? null) ? implode("\n", $day['exclusions']) : ($day['exclusions'] ?? '');
                            $dayItems = is_array($day['items'] ?? null) ? $day['items'] : [];
                        @endphp
                        <div class="day-card border rounded p-3 mb-3 bg-white shadow-xs" data-day="{{ $dayNum }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-primary">
                                    <i class="bi bi-calendar3 me-1"></i> Day {{ $dayNum }} Schedule
                                </h6>
                                <button type="button" class="btn btn-sm btn-link text-danger remove-day-btn" onclick="this.closest('.day-card').remove();" {{ count($daysList) <= 1 ? 'style=display:none;' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <input type="hidden" name="days[{{ $index }}][day_number]" value="{{ $dayNum }}">
                            <div class="row g-2 mb-2">
                                <div class="col-md-8">
                                    <input type="text" name="days[{{ $index }}][title]" class="form-control" value="{{ $dayTitle }}" placeholder="Day Title">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="days[{{ $index }}][overnight_location]" class="form-control" value="{{ $dayOvernight }}" placeholder="Overnight Location">
                                </div>
                            </div>
                            <textarea name="days[{{ $index }}][description]" class="form-control form-control-sm mb-2" rows="2" placeholder="Day description and flow...">{{ $dayDesc }}</textarea>

                            <!-- Operations Inventory & Supplier Bookings for Day -->
                            <div class="day-inventory-box mt-3 p-3 bg-light rounded border">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom flex-wrap gap-2">
                                    <span class="fw-bold small text-uppercase text-primary">
                                        <i class="bi bi-box-seam me-1"></i> Operations Inventory & Supplier Bookings
                                    </span>
                                    <div class="btn-group btn-group-sm flex-wrap">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="openInventoryModal({{ $index }}, 'hotels')">
                                            <i class="bi bi-building"></i> + Hotel
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="openInventoryModal({{ $index }}, 'transports')">
                                            <i class="bi bi-car-front"></i> + Transport
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="openInventoryModal({{ $index }}, 'tickets')">
                                            <i class="bi bi-ticket-perforated"></i> + Ticket
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="openInventoryModal({{ $index }}, 'spots')">
                                            <i class="bi bi-geo-alt"></i> + Spot
                                        </button>
                                        <button type="button" class="btn btn-outline-warning text-dark btn-sm" onclick="openInventoryModal({{ $index }}, 'activities')">
                                            <i class="bi bi-lightning"></i> + Activity
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="openInventoryModal({{ $index }}, 'meals')">
                                            <i class="bi bi-cup-hot"></i> + Meal
                                        </button>
                                    </div>
                                </div>

                                <div class="day-items-container" id="day-items-{{ $index }}">
                                    @foreach($dayItems as $itemIdx => $rawItem)
                                        @php
                                            $item = (array) $rawItem;
                                            $itemType = $item['item_type'] ?? 'activity';
                                            $badgeClass = 'bg-dark';
                                            $icon = 'bi-box';
                                            switch($itemType) {
                                                case 'hotel': $badgeClass = 'bg-primary'; $icon = 'bi-building'; break;
                                                case 'transport': $badgeClass = 'bg-secondary'; $icon = 'bi-car-front'; break;
                                                case 'ticket': $badgeClass = 'bg-danger'; $icon = 'bi-ticket-perforated'; break;
                                                case 'spot': $badgeClass = 'bg-info text-white'; $icon = 'bi-geo-alt'; break;
                                                case 'activity': $badgeClass = 'bg-warning text-dark'; $icon = 'bi-lightning'; break;
                                                case 'meal': $badgeClass = 'bg-success'; $icon = 'bi-cup-hot'; break;
                                            }
                                        @endphp
                                        <div class="item-row card mb-2 p-2 border bg-white shadow-xs">
                                            <input type="hidden" name="days[{{ $index }}][items][{{ $itemIdx }}][item_type]" value="{{ $itemType }}">
                                            <input type="hidden" name="days[{{ $index }}][items][{{ $itemIdx }}][item_id]" value="{{ $item['item_id'] ?? '' }}">
                                            <input type="hidden" name="days[{{ $index }}][items][{{ $itemIdx }}][supplier_id]" value="{{ $item['supplier_id'] ?? '' }}">
                                            <input type="hidden" name="days[{{ $index }}][items][{{ $itemIdx }}][sort_order]" value="{{ $item['sort_order'] ?? $itemIdx }}">
                                            @if(!empty($item['room_type']))
                                                <input type="hidden" name="days[{{ $index }}][items][{{ $itemIdx }}][room_type]" value="{{ $item['room_type'] }}">
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <span class="badge {{ $badgeClass }}"><i class="bi {{ $icon }} me-1"></i>{{ strtoupper($itemType) }}</span>
                                                    <input type="text" name="days[{{ $index }}][items][{{ $itemIdx }}][title]" value="{{ $item['title'] ?? '' }}" class="form-control form-control-sm fw-bold border-0 bg-light" style="width: auto; min-width: 220px;">
                                                    @if(!empty($item['room_type']))
                                                        <span class="badge bg-light text-dark border"><i class="bi bi-door-closed me-1"></i>{{ $item['room_type'] }}</span>
                                                    @endif
                                                    <input type="text" name="days[{{ $index }}][items][{{ $itemIdx }}][description]" value="{{ $item['description'] ?? '' }}" class="form-control form-control-sm border-0 text-muted" style="width: auto; min-width: 180px;" placeholder="Notes / details">
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="input-group input-group-sm" style="width: 140px;">
                                                        <span class="input-group-text bg-light currency-rate-label">Rate {{ $itinerary->currency_symbol }}</span>
                                                        <input type="number" step="0.01" name="days[{{ $index }}][items][{{ $itemIdx }}][price]" value="{{ $item['price'] ?? 0 }}" class="form-control text-end item-rate-input">
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="this.closest('.item-row').remove();" title="Remove Item">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="row g-2 bg-light p-2 rounded mt-2">
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-warning mb-1"><i class="bi bi-star me-1"></i> Day Highlights</label>
                                    <textarea name="days[{{ $index }}][highlights]" class="form-control form-control-sm" rows="2" placeholder="One highlight per line">{{ $dayHl }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-success mb-1"><i class="bi bi-check-lg me-1"></i> Day Inclusions</label>
                                    <textarea name="days[{{ $index }}][inclusions]" class="form-control form-control-sm" rows="2" placeholder="One inclusion per line">{{ $dayInc }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-danger mb-1"><i class="bi bi-x-lg me-1"></i> Day Exclusions</label>
                                    <textarea name="days[{{ $index }}][exclusions]" class="form-control form-control-sm" rows="2" placeholder="One exclusion per line">{{ $dayExc }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Right -->
        <div class="col-lg-4">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white font-weight-bold py-3">
                    <i class="bi bi-tag-fill text-primary me-2"></i>Pricing & Status
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Primary Currency</label>
                        <select name="currency" id="fixedCurrencySelect" class="form-select">
                            <option value="MYR" {{ old('currency', $itinerary->currency) == 'MYR' ? 'selected' : '' }}>MYR - Malaysian Ringgit (RM)</option>
                            <option value="USD" {{ old('currency', $itinerary->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar ($)</option>
                            <option value="INR" {{ old('currency', $itinerary->currency) == 'INR' ? 'selected' : '' }}>INR - Indian Rupee (₹)</option>
                            <option value="SGD" {{ old('currency', $itinerary->currency) == 'SGD' ? 'selected' : '' }}>SGD - Singapore Dollar (S$)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-success">
                            Fixed Package Price <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold" id="currencySymbolBadge">{{ $itinerary->currency_symbol }}</span>
                            <input type="number" step="0.01" min="0" name="fixed_price" id="fixedPriceInput" class="form-control form-control-lg fw-bold text-success" value="{{ old('fixed_price', $itinerary->fixed_price) }}" required>
                        </div>
                        <small class="text-muted d-block mb-2">Total package price charged in the selected primary currency.</small>

                        <!-- Multi-Currency Live Converted Preview -->
                        <div class="p-2 bg-light rounded border small" id="multiCurrencyPreview">
                            <span class="text-muted d-block fw-bold mb-1"><i class="bi bi-arrow-repeat me-1"></i>Multi-Currency Conversion (MYR / USD / INR):</span>
                            <div class="d-flex flex-wrap gap-2" id="multiCurrencyValues">
                                <!-- Populated by JS -->
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $itinerary->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $itinerary->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Featured Image</label>
                        @if($itinerary->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $itinerary->image) }}" class="img-fluid rounded border" style="max-height: 140px; object-fit: cover;" alt="Cover">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Upload a new image to replace current cover.</small>
                    </div>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white font-weight-bold py-2">
                    <i class="bi bi-lightning text-warning me-2"></i>Actions & Export
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('admin.fixed-itineraries.show', $itinerary->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i> View Quotation / Proposal
                    </a>
                    <a href="{{ route('admin.fixed-itineraries.pdf', $itinerary->id) }}" target="_blank" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-printer me-1"></i> Export / Print Branded PDF
                    </a>
                    <a href="{{ route('admin.fixed-itineraries.whatsapp', $itinerary->id) }}" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-whatsapp me-1"></i> Share via WhatsApp
                    </a>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 font-weight-bold shadow-sm">
                <i class="bi bi-check-circle me-1"></i> Update Fixed Itinerary
            </button>
        </div>
    </div>
</form>

<!-- Operations Inventory Modal Partial -->
@include('admin.itineraries.partials.inventory_modal_and_scripts')

@push('scripts')
<script>
    let dayCount = {{ count($daysList) }};
    document.getElementById('addDayBtn').addEventListener('click', function() {
        dayCount++;
        const index = dayCount - 1;
        const container = document.getElementById('daysContainer');
        const dayHtml = `
            <div class="day-card border rounded p-3 mb-3 bg-white shadow-xs" data-day="${dayCount}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar3 me-1"></i> Day ${dayCount} Schedule</h6>
                    <button type="button" class="btn btn-sm btn-link text-danger remove-day-btn" onclick="this.closest('.day-card').remove();"><i class="bi bi-trash"></i></button>
                </div>
                <input type="hidden" name="days[${index}][day_number]" value="${dayCount}">
                <div class="row g-2 mb-2">
                    <div class="col-md-8">
                        <input type="text" name="days[${index}][title]" class="form-control" placeholder="Day Title">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="days[${index}][overnight_location]" class="form-control" placeholder="Overnight Location">
                    </div>
                </div>
                <textarea name="days[${index}][description]" class="form-control form-control-sm mb-2" rows="2" placeholder="Day description and flow..."></textarea>

                <!-- Operations Inventory & Supplier Bookings for Day -->
                <div class="day-inventory-box mt-3 p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom flex-wrap gap-2">
                        <span class="fw-bold small text-uppercase text-primary">
                            <i class="bi bi-box-seam me-1"></i> Operations Inventory & Supplier Bookings
                        </span>
                        <div class="btn-group btn-group-sm flex-wrap">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="openInventoryModal(${index}, 'hotels')">
                                <i class="bi bi-building"></i> + Hotel
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="openInventoryModal(${index}, 'transports')">
                                <i class="bi bi-car-front"></i> + Transport
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="openInventoryModal(${index}, 'tickets')">
                                <i class="bi bi-ticket-perforated"></i> + Ticket
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="openInventoryModal(${index}, 'spots')">
                                <i class="bi bi-geo-alt"></i> + Spot
                            </button>
                            <button type="button" class="btn btn-outline-warning text-dark btn-sm" onclick="openInventoryModal(${index}, 'activities')">
                                <i class="bi bi-lightning"></i> + Activity
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="openInventoryModal(${index}, 'meals')">
                                <i class="bi bi-cup-hot"></i> + Meal
                            </button>
                        </div>
                    </div>

                    <div class="day-items-container" id="day-items-${index}">
                        {{-- Newly selected inventory items will appear here --}}
                    </div>
                </div>

                <div class="row g-2 bg-light p-2 rounded mt-2">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold text-warning mb-1"><i class="bi bi-star me-1"></i> Day Highlights</label>
                        <textarea name="days[${index}][highlights]" class="form-control form-control-sm" rows="2" placeholder="One highlight per line"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold text-success mb-1"><i class="bi bi-check-lg me-1"></i> Day Inclusions</label>
                        <textarea name="days[${index}][inclusions]" class="form-control form-control-sm" rows="2" placeholder="One inclusion per line"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold text-danger mb-1"><i class="bi bi-x-lg me-1"></i> Day Exclusions</label>
                        <textarea name="days[${index}][exclusions]" class="form-control form-control-sm" rows="2" placeholder="One exclusion per line"></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', dayHtml);
    });

    // Multi-Currency Realtime Converter
    const exchangeRates = {
        'MYR': 1.0,
        'USD': 4.7428,
        'INR': 0.0530,
        'SGD': 3.5428
    };
    const currencySymbols = {
        'MYR': 'RM',
        'USD': '$',
        'INR': '₹',
        'SGD': 'S$'
    };

    function updateMultiCurrencyPreview() {
        const curSelect = document.getElementById('fixedCurrencySelect');
        const priceInput = document.getElementById('fixedPriceInput');
        const badge = document.getElementById('currencySymbolBadge');
        const container = document.getElementById('multiCurrencyValues');
        if (!curSelect || !priceInput || !container) return;

        const baseCur = curSelect.value || 'MYR';
        const price = parseFloat(priceInput.value) || 0;
        const baseSymbol = currencySymbols[baseCur] || baseCur;

        if (badge) badge.textContent = baseSymbol;

        // Base amount in MYR (anchor)
        const amountInAnchor = price * (exchangeRates[baseCur] || 1.0);

        let html = '';
        ['MYR', 'USD', 'INR'].forEach(cur => {
            if (cur !== baseCur) {
                const targetRate = exchangeRates[cur] || 1.0;
                const converted = amountInAnchor / targetRate;
                const sym = currencySymbols[cur] || cur;
                html += `<span class="badge bg-white text-dark border px-2 py-1 shadow-2xs"><strong>${cur}:</strong> ${sym} ${converted.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
            }
        });
        container.innerHTML = html;
    }

    document.getElementById('fixedCurrencySelect')?.addEventListener('change', updateMultiCurrencyPreview);
    document.getElementById('fixedPriceInput')?.addEventListener('input', updateMultiCurrencyPreview);
    updateMultiCurrencyPreview();
</script>

<!-- Quick Add Custom Supplier Modal (Compact & Responsive) -->
<div class="modal fade" id="quickAddSupplierModal" tabindex="-1" aria-labelledby="quickAddSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered px-2" style="max-width: 440px;">
        <div class="modal-content rounded-4 shadow border-0 overflow-hidden">
            <div class="modal-header border-bottom py-2 px-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary rounded-circle p-2 me-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-building-add"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="quickAddSupplierModalLabel">Add Custom Vendor</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">Create and immediately assign to package</small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 bg-light">
                <form id="quickAddSupplierForm" onsubmit="event.preventDefault(); saveQuickCustomSupplier();">
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Supplier / Vendor Name <span class="text-danger">*</span></label>
                        <input type="text" id="quick_supplier_name" class="form-control form-control-sm" required placeholder="e.g. Tourliz Holidays / Grand Hyatt">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Category <span class="text-danger">*</span></label>
                        <select id="quick_supplier_type" class="form-select form-select-sm" required>
                            <option value="Hotel">🏨 Hotel / Accommodation</option>
                            <option value="Transport">🚗 Transport / Transfer / Vehicle</option>
                            <option value="Entry Tickets">🎟️ Entry Tickets / Attractions</option>
                            <option value="Activity">🏄 Activity / Tour</option>
                            <option value="Meal">🍴 Restaurant / Meal</option>
                            <option value="Other">📦 Other Service</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Destination / City</label>
                        <select id="quick_supplier_destination_id" class="form-select form-select-sm">
                            <option value="">Select Destination (Optional)</option>
                            @foreach($destinations as $dest)
                                <option value="{{ $dest->id }}" {{ (isset($itinerary) && $itinerary->destination_id == $dest->id) ? 'selected' : '' }}>{{ $dest->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Phone (Optional)</label>
                            <input type="text" id="quick_supplier_phone" class="form-control form-control-sm" placeholder="+60 12 345 6789">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Email (Optional)</label>
                            <input type="email" id="quick_supplier_email" class="form-control form-control-sm" placeholder="contact@vendor.com">
                        </div>
                    </div>
                    <div id="quickAddSupplierError" class="alert alert-danger py-1 px-2 small mb-0 d-none" style="font-size: 0.75rem;"></div>
                </form>
            </div>
            <div class="modal-footer bg-white py-2 px-3 border-top d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary px-3 fw-bold" id="btnSaveQuickSupplier" onclick="saveQuickCustomSupplier()">
                    <i class="bi bi-check2-circle me-1"></i>Save & Select
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize Select2 for multiple suppliers with Category Tagging & Custom Name entry
    $(function() {
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            $('#supplier_ids').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Choose or type custom vendor...',
                tags: true,
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term + ' (Custom / New)',
                        newTag: true
                    };
                }
            });
        }

        // Category filter buttons
        $('.supplier-cat-filter').on('click', function(e) {
            e.preventDefault();
            $('.supplier-cat-filter').removeClass('btn-primary').addClass('btn-light text-dark border');
            $(this).removeClass('btn-light text-dark border').addClass('btn-primary');
            const cat = $(this).data('category');

            if (cat === 'all') {
                $('#supplier_ids optgroup').show();
            } else {
                $('#supplier_ids optgroup').each(function() {
                    if ($(this).data('category') === cat) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
            if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                $('#supplier_ids').select2('destroy');
                $('#supplier_ids').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Choose or type custom vendor...',
                    tags: true
                });
            }
        });

        // Pre-fill destination when opening Quick Add Supplier modal
        $('#quickAddSupplierModal').on('show.bs.modal', function () {
            const selectedDest = $('#destination_id').val();
            if (selectedDest) {
                $('#quick_supplier_destination_id').val(selectedDest);
            }
            $('#quickAddSupplierError').addClass('d-none').text('');
        });
    });

    function saveQuickCustomSupplier() {
        const name = document.getElementById('quick_supplier_name').value.trim();
        const type = document.getElementById('quick_supplier_type').value;
        const destId = document.getElementById('quick_supplier_destination_id').value;
        const phone = document.getElementById('quick_supplier_phone').value.trim();
        const email = document.getElementById('quick_supplier_email').value.trim();
        const errDiv = document.getElementById('quickAddSupplierError');
        const saveBtn = document.getElementById('btnSaveQuickSupplier');

        if (!name) {
            errDiv.textContent = 'Please enter a vendor name.';
            errDiv.classList.remove('d-none');
            return;
        }
        errDiv.classList.add('d-none').textContent = '';

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

        fetch('{{ route("admin.suppliers.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                name: name,
                type: type,
                destination_id: destId ? parseInt(destId) : null,
                phone: phone || null,
                email: email || null,
                is_active: 1
            })
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw new Error(data.message || 'Error creating supplier (status ' + res.status + ')');
            }
            return data;
        })
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Save & Select';

            if (data.success && data.supplier) {
                const s = data.supplier;
                let optgroup = $(`#supplier_ids optgroup[data-category="${s.type}"]`);
                if (!optgroup.length) {
                    optgroup = $(`#supplier_ids optgroup`).first();
                }

                // If option already exists, select it, otherwise append and select
                let existingOption = $(`#supplier_ids option[value="${s.id}"]`);
                if (!existingOption.length) {
                    const newOption = new Option(`${s.name} [${s.type}]`, s.id, true, true);
                    $(newOption).attr('data-category', s.type);
                    if (optgroup.length) {
                        optgroup.append(newOption);
                    } else {
                        $('#supplier_ids').append(newOption);
                    }
                } else {
                    existingOption.prop('selected', true);
                }

                $('#supplier_ids').trigger('change');
                document.getElementById('quickAddSupplierForm').reset();

                const modalEl = document.getElementById('quickAddSupplierModal');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
                if (typeof jQuery !== 'undefined') {
                    $('#quickAddSupplierModal').modal('hide');
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '');
                }
            } else {
                errDiv.textContent = data.message || 'Error creating supplier.';
                errDiv.classList.remove('d-none');
            }
        })
        .catch(err => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Save & Select';
            errDiv.textContent = err.message || 'Failed to save supplier. Please check inputs.';
            errDiv.classList.remove('d-none');
            console.error('Error saving supplier:', err);
        });
    }
</script>
@endpush
@endsection
