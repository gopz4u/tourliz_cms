@extends('layouts.admin')

@section('title', 'Manage Itinerary — {{ $package->name }}')

@section('content')

<style>
.vendor-palette { border-radius: 16px; background: #f8f9ff; border: 1px solid #e8e3ff; }
.vendor-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;
    cursor: grab; user-select: none; transition: all 0.2s; white-space: nowrap;
}
.vendor-chip:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,0.12); }
.vendor-chip.hotel   { background: #e8eaff; color: #5a52e5; border: 1px solid #c4c0ff; }
.vendor-chip.transport { background: #fff4e0; color: #d97706; border: 1px solid #fde68a; }
.vendor-chip.activity  { background: #dcfce7; color: #16a34a; border: 1px solid #86efac; }
.vendor-chip.other   { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
.day-card { border-left: 4px solid var(--primary); border-radius: 14px; overflow: hidden; }
.day-card .card-header {
    background: linear-gradient(135deg, #f0efff, #fff);
    border-bottom: 1px solid #e8e3ff;
}
.service-row   { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; border-radius: 10px; padding: 8px 12px; background: #fafafa; border: 1px solid #f0f0f0; }
.service-row:last-child { margin-bottom: 0; }
.service-label { flex-shrink: 0; width: 80px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
.service-value { flex: 1; font-size: 0.85rem; color: #334155; }
.custom-field  { display: none; }
.section-header { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between; }
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="mb-1"><i class="bi bi-calendar-range me-2"></i>Manage Itinerary</h2>
            <p class="text-muted mb-0">
                <i class="bi bi-briefcase me-1"></i>{{ $package->name }}
                @if($package->duration)
                    &nbsp;·&nbsp;<i class="bi bi-clock me-1"></i>{{ $package->duration }}
                @endif
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.group-packages.edit', $package->id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil"></i> Edit Group Package
            </a>
            <a href="{{ route('admin.group-package-itineraries.preview', $package->id) }}" class="btn btn-outline-info btn-sm" target="_blank">
                <i class="bi bi-eye"></i> Preview
            </a>
            <button type="button" class="btn btn-success" id="saveBtn">
                <i class="bi bi-save me-1"></i>Save Itinerary
            </button>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- ── LEFT: Vendor Palette ─────────────────────────── --}}
    <div class="col-lg-3">
        <div class="card vendor-palette sticky-top" style="top: 16px;">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-1" style="color:var(--primary);">
                    <i class="bi bi-grid-3x3-gap me-1"></i>Cost Components
                </h6>
                <p class="text-muted small mb-3">Click a vendor chip on a day card to assign it.</p>

                @php
                    $amenities = $package->addon_amenities ?? [];
                    $hotels    = collect($amenities)->filter(fn($a) => ($a['type'] ?? '') === 'hotel')->values();
                    $transports= collect($amenities)->filter(fn($a) => in_array($a['type'] ?? '', ['transport','airport_pickup','airport_drop']))->values();
                    $tickets   = collect($amenities)->filter(fn($a) => in_array($a['type'] ?? '', ['ticket','entry_tickets','entry_ticket']))->values();
                    $activities= collect($amenities)->filter(fn($a) => ($a['type'] ?? '') === 'activity' || (($a['type'] ?? '') === 'other' && !in_array($a['type'] ?? '', ['hotel','transport','airport_pickup','airport_drop','ticket','entry_tickets','entry_ticket'])))->values();
                @endphp

                @if($hotels->count())
                <div class="mb-3">
                    <div class="section-header"><span><i class="bi bi-building me-1"></i>Hotels</span></div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($hotels as $i => $a)
                            @php $sName = $supplierMap[$a['supplier_id'] ?? ''] ?? null; @endphp
                            <span class="vendor-chip hotel" data-type="hotel" data-index="{{ $i }}" data-name="{{ $a['name'] }}" data-supplier-id="{{ $a['supplier_id'] ?? '' }}" data-supplier-name="{{ $sName ?? '' }}" data-total="{{ $a['value'] ?? ($a['total'] ?? 0) }}" onclick="assignVendorToActiveDay(this)" title="{{ $sName ? 'Vendor: '.$sName : '' }}">
                                <i class="bi bi-building"></i> {{ $a['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @php
                    $allPlaces = collect();
                    if(isset($touristSpots)) {
                        foreach($touristSpots as $spot) $allPlaces->push(['name' => $spot->name, 'id' => $spot->id, 'type' => 'spot', 'supplier_id' => null, 'supplier_name' => null]);
                    }
                    if(isset($coreServices)) {
                        foreach($coreServices as $service) {
                            $sName = $supplierMap[$service->supplier_id] ?? null;
                            $allPlaces->push(['name' => $service->name, 'id' => $service->id, 'type' => 'service', 'supplier_id' => $service->supplier_id, 'supplier_name' => $sName]);
                        }
                    }
                    // Sort alphabetically
                    $allPlaces = $allPlaces->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->unique('name');
                @endphp

                @if($allPlaces->count())
                <hr class="my-3 text-muted">
                <h6 class="fw-bold mb-2" style="color:var(--info);">
                    <i class="bi bi-geo-alt me-1"></i>Core Services & Tourist Spots
                </h6>
                <div class="d-flex flex-wrap gap-1" style="max-height: 200px; overflow-y: auto; align-content: flex-start;">
                    @foreach($allPlaces as $spot)
                        @php $sTitle = $spot['supplier_name'] ? 'Vendor: '.$spot['supplier_name'] : ''; @endphp
                        <span class="vendor-chip places" data-type="places" data-name="{{ $spot['name'] }}" data-item-id="{{ $spot['id'] }}" data-item-type="{{ $spot['type'] }}" data-supplier-id="{{ $spot['supplier_id'] ?? '' }}" data-supplier-name="{{ $spot['supplier_name'] ?? '' }}" onclick="assignVendorToActiveDay(this)" style="background:var(--info-bg-subtle, #e0f8f8); color:#0c7e7e; border-color:#b4e5e5;" title="{{ $sTitle }}">
                            <i class="bi bi-pin-map"></i> {{ $spot['name'] }}
                        </span>
                    @endforeach
                </div>
                @endif
                
                <hr class="my-3 text-muted">

                @if($transports->count())
                <div class="mb-3">
                    <div class="section-header"><span><i class="bi bi-truck me-1"></i>Transport</span></div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($transports as $i => $a)
                            @php $sName = $supplierMap[$a['supplier_id'] ?? ''] ?? null; @endphp
                            <span class="vendor-chip transport" data-type="transport" data-index="{{ $i }}" data-name="{{ $a['name'] }}" data-supplier-id="{{ $a['supplier_id'] ?? '' }}" data-supplier-name="{{ $sName ?? '' }}" data-total="{{ $a['value'] ?? ($a['total'] ?? 0) }}" onclick="assignVendorToActiveDay(this)" title="{{ $sName ? 'Vendor: '.$sName : '' }}">
                                <i class="bi bi-truck"></i> {{ $a['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($tickets->count())
                <div class="mb-3">
                    <div class="section-header"><span><i class="bi bi-ticket-perforated me-1"></i>Entry Tickets</span></div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($tickets as $i => $a)
                            @php $sName = $supplierMap[$a['supplier_id'] ?? ''] ?? null; @endphp
                            <span class="vendor-chip activity" data-type="ticket" data-index="{{ $i }}" data-name="{{ $a['name'] }}" data-supplier-id="{{ $a['supplier_id'] ?? '' }}" data-supplier-name="{{ $sName ?? '' }}" data-total="{{ $a['value'] ?? ($a['total'] ?? 0) }}" onclick="assignVendorToActiveDay(this)" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;" title="{{ $sName ? 'Vendor: '.$sName : '' }}">
                                <i class="bi bi-ticket-perforated"></i> {{ $a['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($activities->count())
                <div class="mb-3">
                    <div class="section-header"><span><i class="bi bi-lightning me-1"></i>Activities</span></div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($activities as $i => $a)
                            @php $sName = $supplierMap[$a['supplier_id'] ?? ''] ?? null; @endphp
                            <span class="vendor-chip activity" data-type="activity" data-index="{{ $i }}" data-name="{{ $a['name'] }}" data-supplier-id="{{ $a['supplier_id'] ?? '' }}" data-supplier-name="{{ $sName ?? '' }}" data-total="{{ $a['value'] ?? ($a['total'] ?? 0) }}" onclick="assignVendorToActiveDay(this)" title="{{ $sName ? 'Vendor: '.$sName : '' }}">
                                <i class="bi bi-lightning"></i> {{ $a['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Master Suppliers Section --}}
                @if(isset($suppliers) && $suppliers->count())
                <hr class="my-3 text-muted">
                <h6 class="fw-bold mb-2" style="color:#6366f1;">
                    <i class="bi bi-shop me-1"></i>Master Vendors ({{ $package->destination->country ?? 'Global' }})
                </h6>
                <div class="d-flex flex-column gap-1" style="max-height: 250px; overflow-y: auto;">
                    @foreach($suppliers as $supplier)
                        <div class="vendor-chip {{ strtolower($supplier->type) }}" 
                             data-type="{{ strtolower($supplier->type) }}" 
                             data-name="{{ $supplier->name }}" 
                             onclick="assignVendorToActiveDay(this)"
                             style="cursor:pointer; display:flex; justify-content:space-between; width:100%;">
                            <span><i class="bi bi-check-circle-fill me-1"></i> {{ $supplier->name }}</span>
                            <small class="opacity-75">({{ $supplier->type }})</small>
                        </div>
                    @endforeach
                </div>
                @endif

                @if(!$hotels->count() && !$transports->count() && !$activities->count())
                    <p class="text-muted small text-center py-3">
                        <i class="bi bi-info-circle d-block mb-1 fs-4"></i>
                        No cost components found.<br>
                        <a href="{{ route('admin.group-packages.edit', $package->id) }}">Add them in the group package editor</a>.
                    </p>
                @endif

                <hr class="my-2">
                <div class="section-header"><span><i class="bi bi-calendar-check me-1"></i>Meals Legend</span></div>
                <div class="d-flex flex-wrap gap-1">
                    @foreach(['Breakfast','Lunch','Dinner'] as $m)
                        <span class="vendor-chip other" data-type="meal" data-name="{{ $m }}" onclick="assignMealToActiveDay(this)">
                            <i class="bi bi-cup-hot"></i> {{ $m }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT: Day Builder ───────────────────────────── --}}
    <div class="col-lg-9">

        <div id="itinerary-builder"></div>

        <div class="text-center my-3">
            <button class="btn btn-outline-primary" onclick="addDay()">
                <i class="bi bi-plus-circle me-1"></i> Add Day
            </button>
        </div>

    </div>
</div>

{{-- Hidden save form --}}
<form id="saveForm" action="{{ route('admin.group-package-itineraries.update', $package->id) }}" method="POST" style="display:none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="itinerary" id="itineraryData">
</form>

@endsection

@push('scripts')
<script>
// ── Data ─────────────────────────────────────────────────────────────
let itinerary = @json($package->itinerary ?? []);
const currency = "{{ $package->currency ?? 'MYR' }}";
const costComponents = @json($package->addon_amenities ?? []);

// ── Active day tracking (for vendor assignment) ───────────────────────
let activeDayIndex = null;
function setActiveDay(index) {
    activeDayIndex = index;
    document.querySelectorAll('.day-card').forEach((el, i) => {
        el.classList.toggle('border-primary', i === index);
        el.classList.toggle('border-secondary', i !== index);
    });
}

// ── Resolve vendor name from component reference ─────────────────────
function resolveVendorName(val) {
    if (!val) return null;
    if (typeof val === 'object' && val.name) return val.name;
    if (typeof val === 'string') {
        // Try to match "hotel_18" or just a plain name
        const match = val.match(/^(\w+)_(\d+)$/);
        if (match) {
            const idx = parseInt(match[2]);
            const comp = costComponents.find((_, i) => i === idx);
            return comp ? comp.name : val;
        }
        return val; // plain custom name
    }
    return null;
}

// ── Render all days ─────────────────────────────────────────────────
function renderBuilder() {
    const container = document.getElementById('itinerary-builder');
    container.innerHTML = '';

    if (itinerary.length === 0) {
        container.innerHTML = `
            <div class="card text-center py-5 border-dashed" style="border: 2px dashed #d1d5db; border-radius: 16px;">
                <i class="bi bi-calendar-plus fs-1 text-muted d-block mb-2"></i>
                <h5 class="text-muted">No days yet</h5>
                <p class="text-muted small">Click <strong>Add Day</strong> below to start building the day-by-day itinerary.</p>
            </div>`;
        return;
    }

    itinerary.forEach((day, index) => {
        container.appendChild(createDayCard(day, index));
    });

    // Activate first day by default
    if (itinerary.length > 0 && activeDayIndex === null) setActiveDay(0);
}

// ── Build a single day card ──────────────────────────────────────────
function createDayCard(day, index) {
    const div = document.createElement('div');
    div.className = 'card mb-3 day-card shadow-sm';
    div.style.borderLeft = '4px solid var(--primary)';
    div.style.borderRadius = '14px';
    div.onclick = function(e) {
        if (!e.target.closest('button, input, select, textarea')) setActiveDay(index);
    };

    // Resolve vendor info
    const hotelName = resolveVendorName(day.hotel);
    const transportItems = Array.isArray(day.transport) ? day.transport : (day.transport ? [{ mode: resolveVendorName(day.transport) }] : []);
    const activities = Array.isArray(day.activities) ? day.activities : [];
    const places = Array.isArray(day.places) ? day.places : [];
    const meals = day.meals || {};
    const mealsIncluded = Array.isArray(meals)
        ? meals
        : Object.entries(meals).filter(([k, v]) => v && v !== 'Not included').map(([k]) => k.charAt(0).toUpperCase() + k.slice(1));

    // Resolve vendor info
    const hotelObj = typeof day.hotel === 'object' ? day.hotel : { name: day.hotel };
    const hotelName = hotelObj.name || '';
    const hotelSupplier = hotelObj.supplier_name || '';
    // Build transport entries HTML
    let transportRows = '';
    transportItems.forEach((t, ti) => {
        const mode = t.mode || t.type || t.name || '';
        transportRows += `
        <div class="d-flex align-items-center gap-2 mb-1 transport-entry" data-ti="${ti}">
            <span class="text-muted small flex-grow-1 transport-mode-text">${mode}</span>
            <input type="text" class="form-control form-control-sm flex-grow-1 d-none transport-mode-input" value="${mode}" onblur="updateTransportMode(${index}, ${ti}, this.value); toggleTransportEdit(this, false);" placeholder="e.g. Private Van">
            <button class="btn btn-sm btn-link text-muted p-0" onclick="toggleTransportEdit(this.previousElementSibling.previousElementSibling, true); $(this).prev('.transport-mode-input').show(); $(this).prev('.transport-mode-text').hide();" title="Edit">
                <i class="bi bi-pencil" style="font-size:0.75rem;"></i>
            </button>
            <button class="btn btn-sm btn-link text-danger p-0" onclick="removeTransport(${index}, ${ti})" title="Remove">
                <i class="bi bi-x" style="font-size:0.75rem;"></i>
            </button>
        </div>`;
    });

    // Build activity entries HTML
    let activityRows = '';
    activities.forEach((act, ai) => {
        const actName = typeof act === 'string' ? act : (act.name || act.mode || '');
        const actSupplier = typeof act === 'object' ? act.supplier_name : '';
        activityRows += `
        <div class="d-flex flex-column mb-1 activity-entry" data-ai="${ai}">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success flex-grow-1 text-start" style="border-radius:6px; font-size:0.75rem; padding:4px 8px;">${actName}</span>
                <button class="btn btn-sm btn-link text-danger p-0" onclick="removeActivity(${index}, ${ai})" title="Remove">
                    <i class="bi bi-x" style="font-size:0.75rem;"></i>
                </button>
            </div>
            ${actSupplier ? `<small class="text-muted ms-1" style="font-size: 0.65rem;"><i class="bi bi-shop"></i> ${actSupplier}</small>` : ''}
        </div>`;
    });

    // Build place entries HTML
    let placeRows = '';
    places.forEach((plc, pi) => {
        const placeName = typeof plc === 'string' ? plc : (plc.name || plc.mode || '');
        const placeSupplier = typeof plc === 'object' ? plc.supplier_name : '';
        placeRows += `
        <div class="d-flex flex-column mb-1 place-entry" data-pi="${pi}">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-info-subtle text-info flex-grow-1 text-start" style="border-radius:6px; font-size:0.75rem; padding:4px 8px;"><i class="bi bi-pin-map"></i> ${placeName}</span>
                <button class="btn btn-sm btn-link text-danger p-0" onclick="removePlace(${index}, ${pi})" title="Remove">
                    <i class="bi bi-x" style="font-size:0.75rem;"></i>
                </button>
            </div>
            ${placeSupplier ? `<small class="text-muted ms-1" style="font-size: 0.65rem;"><i class="bi bi-shop"></i> ${placeSupplier}</small>` : ''}
        </div>`;
    });

    div.innerHTML = `
        <div class="card-header py-2" style="background: linear-gradient(135deg, #f0efff, #fff);">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:28px;height:28px;border-radius:8px;background:var(--primary);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-calendar2-day text-white" style="font-size:0.75rem;"></i>
                    </div>
                    <div>
                        <span class="text-muted small">Day ${day.day}</span>
                        <input type="text" class="form-control form-control-sm border-0 fw-bold p-0 ps-1"
                            style="min-width:220px; font-size:0.95rem; color:var(--primary); background:transparent;"
                            value="${escHtml(day.title || '')}"
                            placeholder="Day title..."
                            onchange="updateField(${index}, 'title', this.value)">
                    </div>
                </div>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#day-body-${index}">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeDay(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="day-body-${index}" class="collapse show">
            <div class="card-body pt-3 pb-3">

                <div class="row g-3">
                    {{-- Hotel --}}
                    <div class="col-md-6">
                        <div class="section-header">
                            <span><i class="bi bi-building me-1 text-primary"></i>Hotel</span>
                            <button class="btn btn-link btn-sm p-0 text-muted" onclick="toggleCustomInput(${index}, 'hotel')" title="Add custom hotel">
                                <i class="bi bi-plus-circle" style="font-size:0.8rem;"></i>
                            </button>
                        </div>
                        <div id="hotel-display-${index}" class="service-row" style="${hotelName ? '' : 'display:none;'}">
                            <span class="badge bg-primary-subtle text-primary service-label"><i class="bi bi-building"></i></span>
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="service-value hotel-name-text">${hotelName}</span>
                                ${hotelSupplier ? `<small class="text-muted" style="font-size:0.65rem;"><i class="bi bi-shop"></i> ${hotelSupplier}</small>` : ''}
                            </div>
                            <button class="btn btn-link btn-sm text-danger p-0 ms-auto" onclick="clearHotel(${index})" title="Remove"><i class="bi bi-x"></i></button>
                        </div>
                        <div id="hotel-empty-${index}" class="text-muted small py-1 ps-1" style="${hotelName ? 'display:none;' : ''}">
                            <i class="bi bi-dash"></i> No hotel assigned
                        </div>
                        <div id="hotel-custom-${index}" class="mt-1 custom-field">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-building-add"></i></span>
                                <input type="text" class="form-control" placeholder="e.g. Marriott Putrajaya"
                                    onkeydown="if(event.key==='Enter'){assignCustomHotel(${index}, this.value); this.value=''; toggleCustomInput(${index},'hotel');}">
                                <button class="btn btn-primary" type="button" onclick="const i=this.previousElementSibling; assignCustomHotel(${index}, i.value); i.value=''; toggleCustomInput(${index},'hotel');">
                                    Add
                                </button>
                            </div>
                            <small class="text-muted">Press Enter or click Add</small>
                        </div>
                    </div>

                    {{-- Transport --}}
                    <div class="col-md-6">
                        <div class="section-header">
                            <span><i class="bi bi-truck me-1 text-warning"></i>Transport</span>
                            <button class="btn btn-link btn-sm p-0 text-muted" onclick="toggleCustomInput(${index}, 'transport')" title="Add custom transport">
                                <i class="bi bi-plus-circle" style="font-size:0.8rem;"></i>
                            </button>
                        </div>
                        <div id="transport-list-${index}">
                            ${transportRows || '<div class="text-muted small py-1 ps-1"><i class="bi bi-dash"></i> No transport assigned</div>'}
                        </div>
                        <div id="transport-custom-${index}" class="mt-1 custom-field">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-truck"></i></span>
                                <input type="text" class="form-control" placeholder="e.g. Private Van"
                                    onkeydown="if(event.key==='Enter'){addCustomTransport(${index}, this.value); this.value=''; toggleCustomInput(${index},'transport');}">
                                <button class="btn btn-warning text-white" type="button" onclick="const i=this.previousElementSibling; addCustomTransport(${index}, i.value); i.value=''; toggleCustomInput(${index},'transport');">
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Places --}}
                    <div class="col-md-6">
                        <div class="section-header">
                            <span><i class="bi bi-pin-map me-1 text-info"></i>Places</span>
                            <button class="btn btn-link btn-sm p-0 text-muted" onclick="toggleCustomInput(${index}, 'places')" title="Add custom place">
                                <i class="bi bi-plus-circle" style="font-size:0.8rem;"></i>
                            </button>
                        </div>
                        <div id="place-list-${index}">
                            ${placeRows || '<div class="text-muted small py-1 ps-1"><i class="bi bi-dash"></i> No places assigned</div>'}
                        </div>
                        <div id="places-custom-${index}" class="mt-1 custom-field">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-info-subtle border-info text-info"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" class="form-control border-info" placeholder="e.g. KLCC Park"
                                    onkeydown="if(event.key==='Enter'){addCustomPlace(${index}, this.value); this.value=''; toggleCustomInput(${index},'places');}">
                                <button class="btn btn-info text-white" type="button" onclick="const i=this.previousElementSibling; addCustomPlace(${index}, i.value); i.value=''; toggleCustomInput(${index},'places');">
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Activities --}}
                    <div class="col-md-6">
                        <div class="section-header">
                            <span><i class="bi bi-lightning me-1 text-success"></i>Activities</span>
                            <button class="btn btn-link btn-sm p-0 text-muted" onclick="toggleCustomInput(${index}, 'activities')" title="Add custom activity">
                                <i class="bi bi-plus-circle" style="font-size:0.8rem;"></i>
                            </button>
                        </div>
                        <div id="activity-list-${index}">
                            ${activityRows || '<div class="text-muted small py-1 ps-1"><i class="bi bi-dash"></i> No activities assigned</div>'}
                        </div>
                        <div id="activities-custom-${index}" class="mt-1 custom-field">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-lightning"></i></span>
                                <input type="text" class="form-control" placeholder="e.g. Petronas Towers Visit"
                                    onkeydown="if(event.key==='Enter'){addCustomActivity(${index}, this.value); this.value=''; toggleCustomInput(${index},'activities');}">
                                <button class="btn btn-success text-white" type="button" onclick="const i=this.previousElementSibling; addCustomActivity(${index}, i.value); i.value=''; toggleCustomInput(${index},'activities');">
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-2">

                {{-- Meals --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="section-header mb-0 me-2" style="width:auto;"><i class="bi bi-cup-hot me-1"></i>Meals:</span>
                    ${mealsIncluded.length
                        ? mealsIncluded.map(m => `<span class="badge bg-light text-dark border" style="border-radius:20px; font-size:0.75rem;"><i class="bi bi-check text-success"></i> ${m}</span>`).join('')
                        : '<span class="text-muted small">None included</span>'}
                    <button class="btn btn-outline-secondary btn-sm ms-auto" onclick="toggleMealEditor(${index})" style="border-radius:8px; font-size:0.75rem;">
                        <i class="bi bi-pencil"></i> Edit Meals
                    </button>
                </div>
                <div id="meal-editor-${index}" class="mt-2" style="display:none;">
                    <div class="d-flex gap-2 flex-wrap">
                        ${['Breakfast','Lunch','Dinner'].map(m => {
                            const key = m.toLowerCase();
                            const checked = (Array.isArray(meals) ? meals.includes(m) : meals[key] && meals[key] !== 'Not included') ? 'checked' : '';
                            return `<label class="d-flex align-items-center gap-1 small" style="cursor:pointer;">
                                <input type="checkbox" ${checked} onchange="toggleMeal(${index}, '${key}', this.checked)"> ${m}
                            </label>`;
                        }).join('')}
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mt-2">
                    <textarea class="form-control form-control-sm" rows="1" style="border-radius:8px;"
                        placeholder="Day notes (optional)..."
                        onchange="updateField(${index}, 'notes', this.value)">${escHtml(day.notes || day.description || '')}</textarea>
                </div>

            </div>
        </div>`;

    return div;
}

// ── Escape HTML helper ───────────────────────────────────────────────
function escHtml(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── Custom toggles ───────────────────────────────────────────────────
window.toggleCustomInput = function(index, type) {
    const el = document.getElementById(`${type}-custom-${index}`);
    if (!el) return;
    const isHidden = el.style.display === 'none' || !el.style.display;
    el.style.display = isHidden ? 'block' : 'none';
    if (isHidden) el.querySelector('input')?.focus();
};
window.toggleMealEditor = function(index) {
    const el = document.getElementById(`meal-editor-${index}`);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
};
window.toggleTransportEdit = function(el, show) {};

// ── Field updaters ───────────────────────────────────────────────────
window.updateField = function(index, field, value) {
    itinerary[index][field] = value;
};

// ── Hotel ────────────────────────────────────────────────────────────
window.assignCustomHotel = function(index, name) {
    if (!name.trim()) return;
    itinerary[index].hotel = { name: name.trim(), type: '', price_per_night: 0, currency };
    document.querySelector(`#hotel-display-${index} .hotel-name-text`).textContent = name.trim();
    document.getElementById(`hotel-display-${index}`).style.display = '';
    document.getElementById(`hotel-empty-${index}`).style.display = 'none';
};
window.clearHotel = function(index) {
    itinerary[index].hotel = { name: '', type: '', price_per_night: 0, currency };
    document.getElementById(`hotel-display-${index}`).style.display = 'none';
    document.getElementById(`hotel-empty-${index}`).style.display = '';
};

// ── Transport ─────────────────────────────────────────────────────────
window.addCustomTransport = function(index, mode) {
    if (!mode.trim()) return;
    if (!Array.isArray(itinerary[index].transport)) itinerary[index].transport = [];
    itinerary[index].transport.push({ type: 'Custom', mode: mode.trim(), from: '', to: '', price: 0, currency });
    refreshTransportList(index);
};
window.removeTransport = function(index, ti) {
    itinerary[index].transport.splice(ti, 1);
    refreshTransportList(index);
};
window.updateTransportMode = function(index, ti, val) {
    if (itinerary[index].transport[ti]) itinerary[index].transport[ti].mode = val;
};
function refreshTransportList(index) {
    const list = document.getElementById(`transport-list-${index}`);
    if (!list) return;
    const items = itinerary[index].transport || [];
    if (!items.length) { list.innerHTML = '<div class="text-muted small py-1 ps-1"><i class="bi bi-dash"></i> No transport assigned</div>'; return; }
    list.innerHTML = items.map((t, ti) => {
        const mode = t.mode || t.type || '';
        const supplier = t.supplier_name || '';
        return `
        <div class="d-flex flex-column mb-1">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small flex-grow-1">${mode}</span>
                <button class="btn btn-sm btn-link text-danger p-0" onclick="removeTransport(${index}, ${ti})"><i class="bi bi-x"></i></button>
            </div>
            ${supplier ? `<small class="text-muted" style="font-size:0.65rem; margin-top:-2px;"><i class="bi bi-shop"></i> ${supplier}</small>` : ''}
        </div>`;
    }).join('');
}

// ── Activities ────────────────────────────────────────────────────────
window.addCustomActivity = function(index, name) {
    if (!name.trim()) return;
    if (!Array.isArray(itinerary[index].activities)) itinerary[index].activities = [];
    itinerary[index].activities.push(name.trim());
    refreshActivityList(index);
};
window.removeActivity = function(index, ai) {
    (itinerary[index].activities || []).splice(ai, 1);
    refreshActivityList(index);
};
function refreshActivityList(index) {
    const list = document.getElementById(`activity-list-${index}`);
    if (!list) return;
    const items = itinerary[index].activities || [];
    if (!items.length) { list.innerHTML = '<div class="text-muted small py-1 ps-1"><i class="bi bi-dash"></i> No activities assigned</div>'; return; }
    list.innerHTML = items.map((act, ai) => {
        const name = typeof act === 'string' ? act : (act.name || act.mode || '');
        const supplier = typeof act === 'object' ? act.supplier_name : '';
        return `<div class="d-flex flex-column mb-1">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success flex-grow-1 text-start" style="border-radius:6px; font-size:0.75rem; padding:4px 8px;">${name}</span>
                <button class="btn btn-sm btn-link text-danger p-0" onclick="removeActivity(${index}, ${ai})"><i class="bi bi-x"></i></button>
            </div>
            ${supplier ? `<small class="text-muted ms-1" style="font-size: 0.65rem;"><i class="bi bi-shop"></i> ${supplier}</small>` : ''}
        </div>`;
    }).join('');
}

// ── Places ────────────────────────────────────────────────────────
window.addCustomPlace = function(index, name) {
    if (!name.trim()) return;
    if (!Array.isArray(itinerary[index].places)) itinerary[index].places = [];
    itinerary[index].places.push(name.trim());
    refreshPlaceList(index);
};
window.removePlace = function(index, pi) {
    (itinerary[index].places || []).splice(pi, 1);
    refreshPlaceList(index);
};
function refreshPlaceList(index) {
    const list = document.getElementById(`place-list-${index}`);
    if (!list) return;
    const items = itinerary[index].places || [];
    if (!items.length) { list.innerHTML = '<div class="text-muted small py-1 ps-1"><i class="bi bi-dash"></i> No places assigned</div>'; return; }
    list.innerHTML = items.map((plc, pi) => {
        const name = typeof plc === 'string' ? plc : (plc.name || plc.mode || '');
        const supplier = typeof plc === 'object' ? plc.supplier_name : '';
        return `<div class="d-flex flex-column mb-1">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-info-subtle text-info flex-grow-1 text-start" style="border-radius:6px; font-size:0.75rem; padding:4px 8px;"><i class="bi bi-pin-map"></i> ${name}</span>
                <button class="btn btn-sm btn-link text-danger p-0" onclick="removePlace(${index}, ${pi})"><i class="bi bi-x"></i></button>
            </div>
            ${supplier ? `<small class="text-muted ms-1" style="font-size: 0.65rem;"><i class="bi bi-shop"></i> ${supplier}</small>` : ''}
        </div>`;
    }).join('');
}

// ── Meals ──────────────────────────────────────────────────────────────
window.toggleMeal = function(index, key, checked) {
    if (!itinerary[index].meals || Array.isArray(itinerary[index].meals)) {
        itinerary[index].meals = { breakfast: 'Not included', lunch: 'Not included', dinner: 'Not included' };
    }
    itinerary[index].meals[key] = checked ? 'Included' : 'Not included';
};

// ── Vendor Palette Assignment ─────────────────────────────────────────
window.assignVendorToActiveDay = function(chipEl) {
    if (activeDayIndex === null) {
        // If no day active, use first day
        if (itinerary.length === 0) { alert('Add a day first!'); return; }
        setActiveDay(0);
    }
    const type = chipEl.dataset.type;
    const name = chipEl.dataset.name;
    const total = parseFloat(chipEl.dataset.total || 0);
    const supplierId = chipEl.dataset.supplierId || null;
    const supplierName = chipEl.dataset.supplierName || null;
    const index = activeDayIndex;

    if (type === 'hotel') {
        itinerary[index].hotel = { name, type: '', price_per_night: total, currency, supplier_id: supplierId, supplier_name: supplierName };
        document.querySelector(`#hotel-display-${index} .hotel-name-text`).textContent = name;
        document.getElementById(`hotel-display-${index}`).style.display = '';
        document.getElementById(`hotel-empty-${index}`).style.display = 'none';
    } else if (type === 'transport') {
        if (!Array.isArray(itinerary[index].transport)) itinerary[index].transport = [];
        itinerary[index].transport.push({ type: 'Component', mode: name, from: '', to: '', price: total, currency, supplier_id: supplierId, supplier_name: supplierName });
        refreshTransportList(index);
    } else if (type === 'places') {
        if (!Array.isArray(itinerary[index].places)) itinerary[index].places = [];
        itinerary[index].places.push({ name: name, supplier_id: supplierId, supplier_name: supplierName });
        refreshPlaceList(index);
    } else {
        if (!Array.isArray(itinerary[index].activities)) itinerary[index].activities = [];
        itinerary[index].activities.push({ name: name, supplier_id: supplierId, supplier_name: supplierName });
        refreshActivityList(index);
    }
};

window.assignMealToActiveDay = function(chipEl) {
    if (activeDayIndex === null) {
        if (itinerary.length === 0) { alert('Add a day first!'); return; }
        setActiveDay(0);
    }
    const meal = chipEl.dataset.name.toLowerCase();
    const index = activeDayIndex;
    if (!itinerary[index].meals || Array.isArray(itinerary[index].meals)) {
        itinerary[index].meals = { breakfast: 'Not included', lunch: 'Not included', dinner: 'Not included' };
    }
    itinerary[index].meals[meal] = 'Included';
    // Re-render just this day to reflect meal change
    const card = document.getElementById(`day-body-${index}`);
    const newCard = createDayCard(itinerary[index], index);
    card.closest('.day-card').replaceWith(newCard);
};

// ── Add / Remove Day ──────────────────────────────────────────────────
window.addDay = function() {
    const nextNum = itinerary.length + 1;
    itinerary.push({
        day: nextNum,
        title: `Day ${nextNum}`,
        hotel: { name: '', type: '', price_per_night: 0, currency },
        transport: [],
        activities: [],
        meals: { breakfast: 'Not included', lunch: 'Not included', dinner: 'Not included' },
        notes: '',
        places: [],
        destinations: [],
    });
    const card = createDayCard(itinerary[itinerary.length - 1], itinerary.length - 1);
    document.getElementById('itinerary-builder').appendChild(card);
    setActiveDay(itinerary.length - 1);
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

window.removeDay = function(index) {
    if (!confirm('Remove Day ' + itinerary[index].day + '?')) return;
    itinerary.splice(index, 1);
    itinerary.forEach((d, i) => { d.day = i + 1; });
    activeDayIndex = null;
    renderBuilder();
};

// ── Save ──────────────────────────────────────────────────────────────
document.getElementById('saveBtn').addEventListener('click', function () {
    document.getElementById('itineraryData').value = JSON.stringify(itinerary);
    document.getElementById('saveForm').submit();
});

// ── Initial render ────────────────────────────────────────────────────
renderBuilder();
</script>
@endpush