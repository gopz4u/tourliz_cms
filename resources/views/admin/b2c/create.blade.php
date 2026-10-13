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
                                            <i class="bi bi-pin-map-fill me-1"></i>Select Master Package / Fixed Itinerary (Optional)
                                        </label>
                                        <span class="badge bg-secondary-subtle text-secondary border small">Not Mandatory</span>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted mb-1 fw-semibold">
                                                <i class="bi bi-pin-map text-primary me-1"></i>Fixed Itinerary:
                                            </label>
                                            <select name="fixed_itinerary_id" id="fixed_itinerary_select" class="form-select form-select-sm">
                                                <option value="">-- None / Blank (Build Custom Day-Wise Plan) --</option>
                                                @if(isset($fixedItineraries))
                                                    @foreach($fixedItineraries as $fi)
                                                        <option value="{{ $fi->id }}" 
                                                                data-days="{{ $fi->duration_days ?: 1 }}" 
                                                                data-title="{{ $fi->title }}" 
                                                                data-destination="{{ $fi->destination_id }}"
                                                                data-price="{{ $fi->fixed_price }}"
                                                                data-currency="{{ $fi->currency ?: 'MYR' }}">
                                                            {{ $fi->title }} ({{ $fi->duration_days }}D{{ $fi->duration_nights ?: 0 }}N - {{ $fi->currency ?: 'MYR' }} {{ number_format($fi->fixed_price, 2) }})
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
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
                                        <i class="bi bi-info-circle me-1 text-primary"></i><strong>Flexibility Note:</strong> Selecting a Fixed Itinerary or Group Tour is <u>optional</u>. If you need to build or add a day-wise itinerary manually from scratch, leave these blank and proceed directly.
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
                                <label class="form-label fw-bold small text-muted">Arrival Date (Start)</label>
                                <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Duration (Days) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="duration_days" class="form-control" value="5" min="1" required>
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
    const titleInput = document.querySelector('input[name="title"]');
    const daysInput = document.querySelector('input[name="duration_days"]');

    if (fixedSelect) {
        fixedSelect.addEventListener('change', function() {
            if (this.value) {
                if (groupSelect) groupSelect.value = '';
                const opt = this.options[this.selectedIndex];
                if (titleInput && opt.getAttribute('data-title')) {
                    titleInput.value = opt.getAttribute('data-title');
                }
                if (daysInput && opt.getAttribute('data-days')) {
                    daysInput.value = opt.getAttribute('data-days');
                }
            }
        });
    }

    if (groupSelect) {
        groupSelect.addEventListener('change', function() {
            if (this.value) {
                if (fixedSelect) fixedSelect.value = '';
                const opt = this.options[this.selectedIndex];
                if (titleInput && opt.getAttribute('data-title')) {
                    titleInput.value = opt.getAttribute('data-title');
                }
                if (daysInput && opt.getAttribute('data-days')) {
                    daysInput.value = opt.getAttribute('data-days');
                }
            }
        });
    }
});
</script>
@endpush