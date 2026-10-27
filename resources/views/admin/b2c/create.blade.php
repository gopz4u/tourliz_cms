@extends('layouts.admin')

@section('title', 'New B2C Lead')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-person-plus me-2"></i>New B2C Walk-in Lead</h2>
                <p class="text-muted mb-0">Capture direct customer requirements</p>
            </div>
            <a href="{{ route('admin.b2c-itineraries.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.b2c-itineraries.store') }}" method="POST">
                        @csrf

                        <h5 class="mb-4 border-bottom pb-2">Client Basic Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Customer Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="client_name" class="form-control" placeholder="e.g. John Doe"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Primary Phone / WhatsApp <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="+91 ..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="client@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Lead Source</label>
                                <select name="lead_source" class="form-select">
                                    <option value="walk_in">Walk-in Customer</option>
                                    <option value="phone">Phone Enquiry</option>
                                    <option value="social">Social Media (FB/Insta)</option>
                                    <option value="reference">Reference</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="mt-5 mb-4 border-bottom pb-2">Trip Requirements</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold small text-primary mb-0">
                                            <i class="bi bi-signpost-split-fill me-1"></i>Choose How to Start the Itinerary
                                        </label>
                                        <span class="badge bg-secondary-subtle text-secondary border small">Not Mandatory</span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-3 mb-3 p-2 bg-white rounded border">
                                        @foreach(['blank' => ['bi-file-earmark-plus', 'Start Blank'], 'fixed' => ['bi-pin-map', 'Fixed Itinerary'], 'b2c' => ['bi-person-check', 'Existing B2C'], 'group' => ['bi-people', 'Operations Template']] as $value => [$icon, $label])
                                            <div class="form-check">
                                                <input class="form-check-input import-source-radio" type="radio" name="import_source" value="{{ $value }}" id="import_source_{{ $value }}" {{ old('import_source', 'blank') === $value ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-semibold" for="import_source_{{ $value }}"><i class="bi {{ $icon }} me-1"></i>{{ $label }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-12 import-source-panel d-none" data-source="fixed">
                                            <label class="form-label small text-muted mb-1 fw-semibold">
                                                <i class="bi bi-pin-map text-primary me-1"></i>Fixed Itinerary:
                                            </label>
                                            <select name="fixed_itinerary_id" id="fixed_itinerary_select" class="form-select form-select-sm">
                                                <option value="">-- None / Blank (Build Custom Day-Wise Plan) --</option>
                                                @if(isset($fixedItineraries))
                                                    @foreach($fixedItineraries as $fi)
                                                        @php
                                                            $fixedDays = is_array($fi->days) ? $fi->days : (json_decode($fi->days, true) ?: []);
                                                            $livePrice = collect($fixedDays)->sum(function ($day) {
                                                                return collect($day['items'] ?? [])->sum(function ($item) {
                                                                    return in_array(strtolower($item['item_type'] ?? ''), ['hotel', 'transport', 'activity', 'ticket', 'meal'], true)
                                                                        ? (float) ($item['price'] ?? 0)
                                                                        : 0;
                                                                });
                                                            });
                                                        @endphp
                                                        <option value="{{ $fi->id }}" 
                                                                data-days="{{ $fi->duration_days ?: 1 }}" 
                                                                data-title="{{ $fi->title }}" 
                                                                data-destination="{{ $fi->destination_id }}"
                                                                data-price="{{ $fi->fixed_price }}"
                                                                data-live-price="{{ $livePrice }}"
                                                                data-currency="{{ $fi->currency ?: 'MYR' }}">
                                                            {{ $fi->title }} ({{ $fi->duration_days }}D{{ $fi->duration_nights ?: 0 }}N - {{ $fi->currency ?: 'MYR' }} {{ number_format($fi->fixed_price, 2) }})
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <div id="fixed-price-choice" class="mt-3 p-3 bg-white border rounded d-none">
                                                <div class="small fw-bold text-dark mb-2">Choose the price to use</div>
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <label class="border rounded p-2 w-100 h-100 d-flex align-items-center gap-2" for="create-pricing-fixed">
                                                            <input class="form-check-input mt-0" type="radio" name="pricing_mode" id="create-pricing-fixed" value="fixed" checked>
                                                            <span><small class="text-muted d-block">Fixed Price</small><strong id="create-fixed-price">—</strong></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="border rounded p-2 w-100 h-100 d-flex align-items-center gap-2" for="create-pricing-live">
                                                            <input class="form-check-input mt-0" type="radio" name="pricing_mode" id="create-pricing-live" value="live">
                                                            <span><small class="text-muted d-block">Live Price</small><strong id="create-live-price">—</strong></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-text">Live price is calculated from the current package items. You can change the method again on the proposal edit screen.</div>
                                            </div>
                                        </div>
                                        <div class="col-12 import-source-panel d-none" data-source="b2c">
                                            <label class="form-label small text-muted mb-1 fw-semibold">
                                                <i class="bi bi-person-check text-success me-1"></i>Existing B2C Itinerary:
                                            </label>
                                            <select name="b2c_source_id" id="b2c_itinerary_select" class="form-select form-select-sm">
                                                <option value="">-- Select a saved B2C itinerary --</option>
                                                @foreach($b2cTemplates ?? [] as $template)
                                                    <option value="{{ $template->id }}"
                                                        data-days="{{ $template->duration_days ?: 1 }}"
                                                        data-title="{{ $template->title }}"
                                                        data-destination="{{ $template->destination_id }}">
                                                        #{{ $template->id }} — {{ $template->title }} ({{ $template->duration_days }} Days)
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Showing {{ ($b2cTemplates ?? collect())->count() }} recently saved B2C itineraries. Create and save a B2C proposal first if this list is empty.</div>
                                        </div>
                                        <div class="col-12 import-source-panel d-none" data-source="group">
                                            <label class="form-label small text-muted mb-1 fw-semibold">
                                                <i class="bi bi-box-seam text-info me-1"></i>Operations Group Tour Master:
                                            </label>
                                            <select name="group_package_id" id="group_package_select" class="form-select form-select-sm">
                                                <option value="">-- None / Blank (Build Custom Day-Wise Plan) --</option>
                                                @if(isset($groupPackages))
                                                    @foreach($groupPackages as $gp)
                                                        <option value="{{ $gp->id }}" 
                                                                data-days="{{ $gp->duration_days ?: 3 }}" 
                                                                data-title="{{ $gp->name }}" 
                                                                data-destination="{{ $gp->destination_id }}"
                                                                data-price="{{ $gp->price }}"
                                                                data-currency="{{ $gp->currency ?: 'MYR' }}">
                                                            {{ $gp->name }} ({{ $gp->duration ?: ($gp->duration_days . ' Days') }} - {{ $gp->currency ?: 'MYR' }} {{ number_format($gp->price, 2) }})
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-text small text-muted mt-2">
                                        <i class="bi bi-info-circle me-1 text-primary"></i>Existing B2C import copies only the itinerary, pricing items and proposal content. Previous customer contact, payment and follow-up details are never copied.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">Trip Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="e.g. 5 Days Dubai Family Tour" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Base Destination (Country) <span
                                        class="text-danger">*</span></label>
                                <select name="destination_id" class="form-select" required>
                                    <option value="">Select Destination</option>
                                    @foreach($destinations as $destination)
                                        <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Duration (Days) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="duration_days" class="form-control" value="5" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Travel date status</label>
                                <select name="travel_date_status" id="travel-date-status" class="form-select">
                                    <option value="tbd" {{ old('travel_date_status', 'tbd') === 'tbd' ? 'selected' : '' }}>Date not decided</option>
                                    <option value="flexible" {{ old('travel_date_status') === 'flexible' ? 'selected' : '' }}>Flexible month</option>
                                    <option value="exact" {{ old('travel_date_status') === 'exact' ? 'selected' : '' }}>Exact date</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="exact-date-field">
                                <label class="form-label fw-bold small text-muted">Departure date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                            </div>
                            <div class="col-md-4" id="flexible-month-field">
                                <label class="form-label fw-bold small text-muted">Preferred month</label>
                                <input type="month" name="preferred_travel_month" class="form-control" value="{{ old('preferred_travel_month') }}">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-muted">Date preference notes</label>
                                <input type="text" name="travel_date_notes" class="form-control" value="{{ old('travel_date_notes') }}"
                                    placeholder="e.g. Any week after school holidays">
                                <div class="form-text">Leave the exact date empty while this customer is still deciding.</div>
                            </div>
                            <div class="col-12 mt-2">
                                <label class="form-label fw-bold small text-muted">Additional Countries Covered</label>
                                <div class="row g-2" style="max-height: 120px; overflow-y: auto;">
                                    @foreach($destinations as $dest)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="country_ids[]"
                                                    value="{{ $dest->id }}" id="b2c_country_{{ $dest->id }}">
                                                <label class="form-check-label small"
                                                    for="b2c_country_{{ $dest->id }}">{{ $dest->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label class="form-label fw-bold small text-muted">Assigned To (Managed By)</label>
                                <select name="user_id" class="form-select">
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ old('user_id', auth()->id()) == $admin->id ? 'selected' : '' }}>
                                            {{ $admin->name }} ({{ $admin->role }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 shadow">
                                <i class="bi bi-magic me-2"></i>Generate Draft Itinerary
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fixedSelect = document.getElementById('fixed_itinerary_select');
    const groupSelect = document.getElementById('group_package_select');
    const b2cSelect = document.getElementById('b2c_itinerary_select');
    const titleInput = document.querySelector('input[name="title"]');
    const daysInput = document.querySelector('input[name="duration_days"]');

    const sourceRadios = document.querySelectorAll('.import-source-radio');
    const destinationSelect = document.querySelector('select[name="destination_id"]');
    const fixedPriceChoice = document.getElementById('fixed-price-choice');
    const travelDateStatus = document.getElementById('travel-date-status');
    const exactDateField = document.getElementById('exact-date-field');
    const flexibleMonthField = document.getElementById('flexible-month-field');

    function updateTravelDateFields() {
        const isExact = travelDateStatus.value === 'exact';
        exactDateField.classList.toggle('d-none', !isExact);
        flexibleMonthField.classList.toggle('d-none', travelDateStatus.value !== 'flexible');
        const exactDateInput = exactDateField.querySelector('input');
        exactDateInput.required = isExact;
        if (!isExact) exactDateInput.value = '';
    }

    travelDateStatus.addEventListener('change', updateTravelDateFields);
    updateTravelDateFields();

    function selectSource(source) {
        const radio = document.getElementById(`import_source_${source}`);
        if (radio) radio.checked = true;
        showSourcePanel(source);
    }

    function showSourcePanel(source) {
        document.querySelectorAll('.import-source-panel').forEach(panel => {
            panel.classList.toggle('d-none', panel.dataset.source !== source);
        });
    }

    function applyTemplate(select, source) {
        if (!select?.value) return;
        selectSource(source);
        [fixedSelect, b2cSelect, groupSelect].forEach(other => { if (other && other !== select) other.value = ''; });
        const opt = select.options[select.selectedIndex];
        if (titleInput && opt.dataset.title) titleInput.value = opt.dataset.title;
        if (daysInput && opt.dataset.days) daysInput.value = opt.dataset.days;
        if (destinationSelect && opt.dataset.destination) destinationSelect.value = opt.dataset.destination;
        if (source === 'fixed' && fixedPriceChoice) {
            const currency = opt.dataset.currency || 'MYR';
            document.getElementById('create-fixed-price').textContent = `${currency} ${Number(opt.dataset.price || 0).toFixed(2)}`;
            document.getElementById('create-live-price').textContent = `${currency} ${Number(opt.dataset.livePrice || 0).toFixed(2)}`;
            fixedPriceChoice.classList.remove('d-none');
        }
    }

    sourceRadios.forEach(radio => radio.addEventListener('change', function() {
        showSourcePanel(this.value);
        if (this.value === 'blank') [fixedSelect, b2cSelect, groupSelect].forEach(select => { if (select) select.value = ''; });
        if (this.value !== 'fixed' && fixedPriceChoice) fixedPriceChoice.classList.add('d-none');
    }));

    showSourcePanel(document.querySelector('.import-source-radio:checked')?.value || 'blank');

    if (fixedSelect) {
        fixedSelect.addEventListener('change', function() {
            applyTemplate(this, 'fixed');
            if (!this.value && fixedPriceChoice) fixedPriceChoice.classList.add('d-none');
        });
    }

    if (b2cSelect) b2cSelect.addEventListener('change', function() { applyTemplate(this, 'b2c'); });

    if (groupSelect) {
        groupSelect.addEventListener('change', function() {
            applyTemplate(this, 'group');
        });
    }
});
</script>
@endpush
