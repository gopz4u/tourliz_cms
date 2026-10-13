@extends('layouts.admin')

@section('title', 'Edit B2B Proposal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 font-weight-bold text-gray-800 mb-1">Edit B2B Proposal: {{ $itinerary->title }}</h2>
        <p class="text-muted small mb-0">Update net rates, agency rates, supplier details, and day schedules.</p>
    </div>
    <a href="{{ route('admin.itineraries.b2b.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.itineraries.b2b.update', $itinerary->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <!-- Main Form Left -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">B2B Proposal Details</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Proposal Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $itinerary->title) }}" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Destination <span class="text-danger">*</span></label>
                            <select name="destination_id" class="form-select" required>
                                <option value="">Select Destination</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" {{ old('destination_id', $itinerary->destination_id) == $dest->id ? 'selected' : '' }}>{{ $dest->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Target Agency</label>
                            <select name="agency_id" class="form-select">
                                <option value="">Select B2B Partner Agency (Optional)</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" {{ old('agency_id', $itinerary->b2bDetail->agency_id ?? '') == $agency->id ? 'selected' : '' }}>{{ $agency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold">Duration (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', $itinerary->duration_days) }}" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold">Duration (Nights) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_nights" class="form-control" value="{{ old('duration_nights', $itinerary->duration_nights) }}" min="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Min Pax</label>
                            <input type="number" name="min_pax" class="form-control" value="{{ old('min_pax', $itinerary->b2bDetail->min_pax ?? 1) }}" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Max Pax</label>
                            <input type="number" name="max_pax" class="form-control" value="{{ old('max_pax', $itinerary->b2bDetail->max_pax ?? 50) }}" min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proposal Overview</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $itinerary->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Package Highlights, Inclusions & Exclusions -->
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">Highlights, Inclusions & Exclusions</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-warning"><i class="bi bi-star-fill me-1"></i> Highlights (One item per line)</label>
                        <textarea name="highlights" class="form-control" rows="3" placeholder="e.g.&#10;Private Airport Transfer&#10;Luxury Hotel Accommodations">{{ old('highlights', is_array($itinerary->highlights) ? implode("\n", $itinerary->highlights) : $itinerary->highlights) }}</textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> Overall Inclusions (One item per line)</label>
                            <textarea name="inclusions" class="form-control" rows="4" placeholder="e.g.&#10;Private Vehicle & Driver&#10;Daily Breakfast">{{ old('inclusions', is_array($itinerary->inclusions) ? implode("\n", $itinerary->inclusions) : $itinerary->inclusions) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-danger"><i class="bi bi-x-circle-fill me-1"></i> Overall Exclusions (One item per line)</label>
                            <textarea name="exclusions" class="form-control" rows="4" placeholder="e.g.&#10;Personal Expenses&#10;Flight Tickets">{{ old('exclusions', is_array($itinerary->exclusions) ? implode("\n", $itinerary->exclusions) : $itinerary->exclusions) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operational & Vendor Section -->
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">Operational Requirements & Notes (Internal Only)</div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Hotel Category</label>
                            <input type="text" name="hotel_category" class="form-control" value="{{ old('hotel_category', $itinerary->b2bDetail->hotel_category ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Room Category</label>
                            <input type="text" name="room_category" class="form-control" value="{{ old('room_category', $itinerary->b2bDetail->room_category ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Vehicle Category</label>
                            <input type="text" name="vehicle_category" class="form-control" value="{{ old('vehicle_category', $itinerary->b2bDetail->vehicle_category ?? '') }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Primary Supplier / Vendor</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">Select Vendor (Optional)</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $itinerary->b2bDetail->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="guide_required" value="1" id="guideCheck" {{ old('guide_required', $itinerary->b2bDetail->guide_required ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="guideCheck">
                                    Tour Guide Required
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Internal Operational Notes (Never exposed to public API)</label>
                        <textarea name="operational_notes" class="form-control" rows="2">{{ old('operational_notes', $itinerary->b2bDetail->operational_notes ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Agent Facing Notes</label>
                        <textarea name="agent_notes" class="form-control" rows="2">{{ old('agent_notes', $itinerary->b2bDetail->agent_notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Day-by-Day Schedule -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold">Day-by-Day Itinerary Schedule</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addDayBtn"><i class="bi bi-plus"></i> Add Day</button>
                </div>
                <div class="card-body" id="daysContainer">
                    @forelse($itinerary->days as $index => $day)
                        <div class="day-card border rounded p-3 mb-3 bg-white shadow-sm" data-day="{{ $day->day_number }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar3 me-1"></i> Day {{ $day->day_number }} Schedule</h6>
                                <button type="button" class="btn btn-sm btn-link text-danger remove-day-btn" onclick="this.closest('.day-card').remove();"><i class="bi bi-trash"></i> Remove Day</button>
                            </div>
                            <input type="hidden" name="days[{{ $index }}][day_number]" value="{{ $day->day_number }}">
                            <div class="row g-2 mb-2">
                                <div class="col-md-8">
                                    <input type="text" name="days[{{ $index }}][title]" class="form-control" value="{{ $day->title }}" placeholder="Day Title (e.g. Arrival & City Tour)">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="days[{{ $index }}][overnight_location]" class="form-control" value="{{ $day->overnight_location }}" placeholder="Overnight Location (e.g. Kuala Lumpur)">
                                </div>
                            </div>
                            <textarea name="days[{{ $index }}][description]" class="form-control form-control-sm mb-2" rows="2" placeholder="Day description and flow...">{{ $day->description }}</textarea>

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
                                    @if(isset($day->items) && count($day->items) > 0)
                                        @foreach($day->items as $itemIdx => $item)
                                            @php
                                                $badgeClass = 'bg-dark';
                                                $icon = 'bi-box';
                                                switch($item->item_type) {
                                                    case 'hotel': $badgeClass = 'bg-primary'; $icon = 'bi-building'; break;
                                                    case 'transport': $badgeClass = 'bg-secondary'; $icon = 'bi-car-front'; break;
                                                    case 'ticket': $badgeClass = 'bg-danger'; $icon = 'bi-ticket-perforated'; break;
                                                    case 'spot': $badgeClass = 'bg-info text-white'; $icon = 'bi-geo-alt'; break;
                                                    case 'activity': $badgeClass = 'bg-warning text-dark'; $icon = 'bi-lightning'; break;
                                                    case 'meal': $badgeClass = 'bg-success'; $icon = 'bi-cup-hot'; break;
                                                }
                                            @endphp
                                            <div class="item-row card mb-2 p-2 border bg-white shadow-xs">
                                                <input type="hidden" name="days[{{ $index }}][items][{{ $itemIdx }}][item_type]" value="{{ $item->item_type }}">
                                                <input type="hidden" name="days[{{ $index }}][items][{{ $itemIdx }}][item_id]" value="{{ $item->item_id }}">
                                                <input type="hidden" name="days[{{ $index }}][items][{{ $itemIdx }}][supplier_id]" value="{{ $item->supplier_id }}">
                                                <input type="hidden" name="days[{{ $index }}][items][{{ $itemIdx }}][sort_order]" value="{{ $item->sort_order ?? $itemIdx }}">
                                                @if($item->room_type)
                                                    <input type="hidden" name="days[{{ $index }}][items][{{ $itemIdx }}][room_type]" value="{{ $item->room_type }}">
                                                @endif
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                                        <span class="badge {{ $badgeClass }}"><i class="bi {{ $icon }} me-1"></i>{{ strtoupper($item->item_type) }}</span>
                                                        <input type="text" name="days[{{ $index }}][items][{{ $itemIdx }}][title]" value="{{ $item->title }}" class="form-control form-control-sm fw-bold border-0 bg-light" style="width: auto; min-width: 220px;">
                                                        @if($item->room_type)
                                                            <span class="badge bg-light text-dark border"><i class="bi bi-door-closed me-1"></i>{{ $item->room_type }}</span>
                                                        @endif
                                                        <input type="text" name="days[{{ $index }}][items][{{ $itemIdx }}][description]" value="{{ $item->description }}" class="form-control form-control-sm border-0 text-muted" style="width: auto; min-width: 180px;" placeholder="Notes / details">
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="input-group input-group-sm" style="width: 140px;">
                                                            <span class="input-group-text bg-light currency-rate-label">Rate {{ $itinerary->currency_symbol }}</span>
                                                            <input type="number" step="0.01" name="days[{{ $index }}][items][{{ $itemIdx }}][price]" value="{{ $item->price }}" class="form-control text-end item-rate-input">
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="this.closest('.item-row').remove();" title="Remove Item">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="row g-2 bg-light p-2 rounded mt-2">
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-warning mb-1"><i class="bi bi-star me-1"></i> Day Highlights</label>
                                    <textarea name="days[{{ $index }}][highlights]" class="form-control form-control-sm" rows="2" placeholder="One highlight per line">{{ is_array($day->highlights) ? implode("\n", $day->highlights) : $day->highlights }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-success mb-1"><i class="bi bi-check-lg me-1"></i> Day Inclusions</label>
                                    <textarea name="days[{{ $index }}][inclusions]" class="form-control form-control-sm" rows="2" placeholder="One inclusion per line">{{ is_array($day->inclusions) ? implode("\n", $day->inclusions) : $day->inclusions }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-danger mb-1"><i class="bi bi-x-lg me-1"></i> Day Exclusions</label>
                                    <textarea name="days[{{ $index }}][exclusions]" class="form-control form-control-sm" rows="2" placeholder="One exclusion per line">{{ is_array($day->exclusions) ? implode("\n", $day->exclusions) : $day->exclusions }}</textarea>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="day-card border rounded p-3 mb-3 bg-white shadow-sm" data-day="1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar3 me-1"></i> Day 1 Schedule</h6>
                            </div>
                            <input type="hidden" name="days[0][day_number]" value="1">
                            <div class="row g-2 mb-2">
                                <div class="col-md-8">
                                    <input type="text" name="days[0][title]" class="form-control" placeholder="Day Title (e.g. Arrival & Transfer to Hotel)">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="days[0][overnight_location]" class="form-control" placeholder="Overnight Location">
                                </div>
                            </div>
                            <textarea name="days[0][description]" class="form-control form-control-sm mb-2" rows="2" placeholder="Day description and flow..."></textarea>

                            <!-- Operations Inventory & Supplier Bookings for Day 1 -->
                            <div class="day-inventory-box mt-3 p-3 bg-light rounded border">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom flex-wrap gap-2">
                                    <span class="fw-bold small text-uppercase text-primary">
                                        <i class="bi bi-box-seam me-1"></i> Operations Inventory & Supplier Bookings
                                    </span>
                                    <div class="btn-group btn-group-sm flex-wrap">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="openInventoryModal(0, 'hotels')">
                                            <i class="bi bi-building"></i> + Hotel
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="openInventoryModal(0, 'transports')">
                                            <i class="bi bi-car-front"></i> + Transport
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="openInventoryModal(0, 'tickets')">
                                            <i class="bi bi-ticket-perforated"></i> + Ticket
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="openInventoryModal(0, 'spots')">
                                            <i class="bi bi-geo-alt"></i> + Spot
                                        </button>
                                        <button type="button" class="btn btn-outline-warning text-dark btn-sm" onclick="openInventoryModal(0, 'activities')">
                                            <i class="bi bi-lightning"></i> + Activity
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="openInventoryModal(0, 'meals')">
                                            <i class="bi bi-cup-hot"></i> + Meal
                                        </button>
                                    </div>
                                </div>

                                <div class="day-items-container" id="day-items-0">
                                    {{-- Selected inventory items will appear here --}}
                                </div>
                            </div>

                            <div class="row g-2 bg-light p-2 rounded mt-2">
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-warning mb-1"><i class="bi bi-star me-1"></i> Day Highlights</label>
                                    <textarea name="days[0][highlights]" class="form-control form-control-sm" rows="2" placeholder="One highlight per line"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-success mb-1"><i class="bi bi-check-lg me-1"></i> Day Inclusions</label>
                                    <textarea name="days[0][inclusions]" class="form-control form-control-sm" rows="2" placeholder="One inclusion per line"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-danger mb-1"><i class="bi bi-x-lg me-1"></i> Day Exclusions</label>
                                    <textarea name="days[0][exclusions]" class="form-control form-control-sm" rows="2" placeholder="One exclusion per line"></textarea>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Right -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold text-primary">B2B Financial Matrix</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Primary Currency <span class="text-danger">*</span></label>
                        <select name="currency" id="primaryCurrency" class="form-select fw-bold" required>
                            <option value="MYR" {{ old('currency', $itinerary->currency ?: 'MYR') == 'MYR' ? 'selected' : '' }}>MYR - Malaysian Ringgit (RM)</option>
                            <option value="USD" {{ old('currency', $itinerary->currency ?: 'MYR') == 'USD' ? 'selected' : '' }}>USD - US Dollar ($)</option>
                            <option value="INR" {{ old('currency', $itinerary->currency ?: 'MYR') == 'INR' ? 'selected' : '' }}>INR - Indian Rupee (₹)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Net Cost Rate (<span class="currency-symbol-label">{{ $itinerary->currency_symbol }}</span>) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="net_rate" id="netRate" class="form-control" value="{{ old('net_rate', $itinerary->b2bDetail->net_rate ?? '0.00') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Agent Selling Rate (<span class="currency-symbol-label">{{ $itinerary->currency_symbol }}</span>) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="agent_rate" id="agentRate" class="form-control fw-bold text-success" value="{{ old('agent_rate', $itinerary->b2bDetail->agent_rate ?? '0.00') }}" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Markup (%)</label>
                            <input type="number" step="0.01" name="markup_percentage" class="form-control" value="{{ old('markup_percentage', $itinerary->b2bDetail->markup_percentage ?? '0.00') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Markup (<span class="currency-symbol-label">{{ $itinerary->currency_symbol }}</span>)</label>
                            <input type="number" step="0.01" name="markup_amount" class="form-control" value="{{ old('markup_amount', $itinerary->b2bDetail->markup_amount ?? '0.00') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Agent Commission (<span class="currency-symbol-label">{{ $itinerary->currency_symbol }}</span>)</label>
                        <input type="number" step="0.01" name="commission" class="form-control" value="{{ old('commission', $itinerary->b2bDetail->commission ?? '0.00') }}">
                    </div>

                    <!-- Live Multi-Currency Preview -->
                    <div class="card bg-light border-primary mt-3">
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold text-primary mb-2" style="font-size: 13px;">
                                <i class="bi bi-currency-exchange me-1"></i> Multi-Currency Equivalents
                            </h6>
                            <div id="multiCurrencyPreview" class="small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><strong>MYR (RM):</strong></span>
                                    <span id="previewMYR" class="fw-bold">RM 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span><strong>USD ($):</strong></span>
                                    <span id="previewUSD" class="fw-bold">$ 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span><strong>INR (₹):</strong></span>
                                    <span id="previewINR" class="fw-bold">₹ 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">Validity & Status</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Validity Start Date</label>
                        <input type="date" name="validity_start" class="form-control" value="{{ old('validity_start', $itinerary->b2bDetail->validity_start ? $itinerary->b2bDetail->validity_start->format('Y-m-d') : '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Validity End Date</label>
                        <input type="date" name="validity_end" class="form-control" value="{{ old('validity_end', $itinerary->b2bDetail->validity_end ? $itinerary->b2bDetail->validity_end->format('Y-m-d') : '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="draft" {{ old('status', $itinerary->status) == 'draft' ? 'selected' : '' }}>Draft Proposal</option>
                            <option value="published" {{ old('status', $itinerary->status) == 'published' ? 'selected' : '' }}>Active / Approved</option>
                            <option value="archived" {{ old('status', $itinerary->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 font-weight-bold">
                <i class="bi bi-check-circle me-1"></i> Update B2B Proposal
            </button>
        </div>
    </div>
</form>

<!-- Include Shared Inventory Modal & Scripts -->
@include('admin.itineraries.partials.inventory_modal_and_scripts')

@push('scripts')
<script>
    let dayCount = {{ count($itinerary->days) > 0 ? count($itinerary->days) : 1 }};
    document.getElementById('addDayBtn').addEventListener('click', function() {
        dayCount++;
        const index = dayCount - 1;
        const container = document.getElementById('daysContainer');
        const dayHtml = `
            <div class="day-card border rounded p-3 mb-3 bg-white shadow-sm" data-day="${dayCount}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar3 me-1"></i> Day ${dayCount} Schedule</h6>
                    <button type="button" class="btn btn-sm btn-link text-danger remove-day-btn" onclick="this.closest('.day-card').remove();"><i class="bi bi-trash"></i> Remove Day</button>
                </div>
                <input type="hidden" name="days[${index}][day_number]" value="${dayCount}">
                <div class="row g-2 mb-2">
                    <div class="col-md-8">
                        <input type="text" name="days[${index}][title]" class="form-control" placeholder="Day Title (e.g. Full Day Excursion)">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="days[${index}][overnight_location]" class="form-control" placeholder="Overnight Location">
                    </div>
                </div>
                <textarea name="days[${index}][description]" class="form-control form-control-sm mb-2" rows="2" placeholder="Day description and flow..."></textarea>

                <!-- Operations Inventory & Supplier Bookings for Added Day -->
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

    // Multi-Currency Rates & Conversions
    const exchangeRates = {
        MYR: 1.0,
        USD: 4.7428,
        INR: 0.0530,
    };

    const currencySymbols = {
        MYR: 'RM',
        USD: '$',
        INR: '₹'
    };

    function updateMultiCurrencyPreview() {
        const curSelect = document.getElementById('primaryCurrency');
        const cur = curSelect ? curSelect.value : 'MYR';
        const curSymbol = currencySymbols[cur] || cur;
        const amount = parseFloat(document.getElementById('agentRate').value) || 0;

        document.querySelectorAll('.currency-symbol-label').forEach(el => el.textContent = curSymbol);
        document.querySelectorAll('.currency-rate-label').forEach(el => el.textContent = `Rate ${curSymbol}`);

        const rateFrom = exchangeRates[cur] || 1.0;
        const amountInMYR = amount * rateFrom;

        ['MYR', 'USD', 'INR'].forEach(targetCur => {
            const rateTo = exchangeRates[targetCur] || 1.0;
            const converted = amountInMYR / rateTo;
            const previewEl = document.getElementById(`preview${targetCur}`);
            if (previewEl) {
                previewEl.textContent = `${currencySymbols[targetCur]} ${converted.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            }
        });
    }

    document.getElementById('agentRate').addEventListener('input', updateMultiCurrencyPreview);
    document.getElementById('primaryCurrency').addEventListener('change', updateMultiCurrencyPreview);
    updateMultiCurrencyPreview();
</script>
@endpush
@endsection
