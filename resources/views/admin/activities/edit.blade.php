@extends('layouts.admin')

@section('title', 'Edit Activity')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-pencil-square me-2"></i>Edit Activity</h2>
                <p class="text-muted mb-0">Update activity details</p>
            </div>
            <a href="{{ route('admin.activities.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('admin.activities.update', $activity->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Activity Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $activity->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-shop me-1"></i> Supplier / Vendor</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">— None / Walk-in —</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ $activity->supplier_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Link to an Activity supplier from your Supplier Master</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country Filter <span class="text-danger">*</span></label>
                                <select id="countrySelect" class="form-select" onchange="filterDestinations()" required>
                                    <option value="">-- Select Country --</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->name }}" {{ ($activity->destination && $activity->destination->country == $country->name) ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City/Destination <span class="text-danger">*</span></label>
                                <select name="destination_id" id="destinationSelect" class="form-select" required>
                                    <option value="">-- Select Destination --</option>
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->id }}" data-country="{{ $dest->country }}" {{ $activity->destination_id == $dest->id ? 'selected' : '' }}>
                                            {{ $dest->city }} ({{ $dest->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duration</label>
                            <input type="text" name="duration" class="form-control" value="{{ $activity->duration }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Base Price ($) <span class="text-danger">*</span></label>
                            <input type="number" name="base_price" class="form-control" value="{{ $activity->base_price }}"
                                step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"
                                rows="3">{{ $activity->description }}</textarea>
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" {{ $activity->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="activeCheck">Active (Visible in builder)</label>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Update Activity</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function filterDestinations() {
                var country = document.getElementById('countrySelect').value;
                var destinationSelect = document.getElementById('destinationSelect');
                var options = destinationSelect.options;

                var currentlySelected = destinationSelect.value;
                var validSelection = false;

                for (var i = 1; i < options.length; i++) {
                    var opt = options[i];
                    if (!country || opt.getAttribute('data-country') === country) {
                        opt.style.display = "";
                        if(opt.value == currentlySelected) validSelection = true;
                    } else {
                        opt.style.display = "none";
                    }
                }

                if(!validSelection && country !== '') {
                    destinationSelect.value = "";
                }
            }

            // Run on load
            document.addEventListener('DOMContentLoaded', function() {
                filterDestinations();
            });
        </script>
    @endpush
@endsection