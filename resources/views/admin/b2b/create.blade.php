@extends('layouts.admin')

@section('title', 'Create B2B Proposal')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-plus-circle me-2"></i>Create B2B Proposal</h2>
                <p class="text-muted mb-0">Start a new custom itinerary for an agency</p>
            </div>
            <a href="{{ route('admin.b2b-itineraries.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Step 1: Basic Details</h5>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.b2b-itineraries.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Select Agency <span class="text-danger">*</span></label>
                            <select name="agency_id" class="form-select" required>
                                <option value="">-- Choose Agency --</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" {{ (old('agency_id') == $agency->id || (isset($selected_agency_id) && $selected_agency_id == $agency->id)) ? 'selected' : '' }}>
                                        {{ $agency->company_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Who is this proosal for?</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Destination (Country) <span class="text-danger">*</span></label>
                            <select name="destination_id" class="form-select" required>
                                <option value="">-- Choose Destination --</option>
                                @foreach($destinations as $destination)
                                    <option value="{{ $destination->id }}" {{ old('destination_id') == $destination->id ? 'selected' : '' }}>
                                        {{ $destination->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Countries Covered</label>
                            <div class="row g-2" style="max-height: 150px; overflow-y: auto;">
                                @foreach($destinations as $dest)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="country_ids[]" value="{{ $dest->id }}" id="b2b_country_{{ $dest->id }}">
                                        <label class="form-check-label small" for="b2b_country_{{ $dest->id }}">{{ $dest->name }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="form-text">Select all countries this itinerary covers (multi-country tour).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Proposal Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                                placeholder="e.g. 5 Days Bali Honeymoon" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Client Name (Optional)</label>
                            <input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}"
                                placeholder="e.g. Mr. Smith Family">
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Duration (Days) <span class="text-danger">*</span></label>
                                <input type="number" name="duration_days" class="form-control"
                                    value="{{ old('duration_days', 3) }}" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Travel date status</label>
                                <select name="travel_date_status" id="travel-date-status" class="form-select">
                                    <option value="tbd" {{ old('travel_date_status', 'tbd') === 'tbd' ? 'selected' : '' }}>Date not decided</option>
                                    <option value="flexible" {{ old('travel_date_status') === 'flexible' ? 'selected' : '' }}>Flexible month</option>
                                    <option value="exact" {{ old('travel_date_status') === 'exact' ? 'selected' : '' }}>Exact date</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="exact-date-field">
                                <label class="form-label">Departure date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                            </div>
                            <div class="col-md-4" id="flexible-month-field">
                                <label class="form-label">Preferred month</label>
                                <input type="month" name="preferred_travel_month" class="form-control" value="{{ old('preferred_travel_month') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Date preference notes <small class="text-muted">(Optional)</small></label>
                                <input type="text" name="travel_date_notes" class="form-control" value="{{ old('travel_date_notes') }}"
                                    placeholder="e.g. After school holidays, any week in June">
                                <div class="form-text">The departure date is only required when the proposal is confirmed.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Assigned To (Managed By)</label>
                            <select name="user_id" class="form-select">
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ old('user_id', auth()->id()) == $admin->id ? 'selected' : '' }}>
                                        {{ $admin->name }} ({{ $admin->role }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">User responsible for handling this proposal and followups.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2">
                                Next: Build Itinerary <i class="bi bi-arrow-right ms-2"></i>
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
        document.addEventListener('DOMContentLoaded', function () {
            const status = document.getElementById('travel-date-status');
            const exactField = document.getElementById('exact-date-field');
            const monthField = document.getElementById('flexible-month-field');
            const exactInput = exactField.querySelector('input');

            function updateTravelDateFields() {
                const isExact = status.value === 'exact';
                exactField.classList.toggle('d-none', !isExact);
                monthField.classList.toggle('d-none', status.value !== 'flexible');
                exactInput.required = isExact;
                if (!isExact) exactInput.value = '';
            }

            status.addEventListener('change', updateTravelDateFields);
            updateTravelDateFields();
        });
    </script>
@endpush
