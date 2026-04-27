@extends('layouts.admin')

@section('title', 'Edit Transport')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-pencil-square me-2"></i>Edit Transport</h2>
                <p class="text-muted mb-0">Update transport details</p>
            </div>
            <a href="{{ route('admin.transports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('admin.transports.update', $transport->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Vendor <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Select Vendor</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ $transport->supplier_id == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Service Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $transport->name }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country Filter</label>
                                <select id="countrySelect" class="form-select" onchange="filterDestinations()">
                                    <option value="">-- Select Country --</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->name }}" {{ (optional($transport->destination)->country == $country->name) ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City / Destination <span class="text-danger">*</span></label>
                                <select name="destination_id" id="destinationSelect" class="form-select" required>
                                    <option value="">-- Select Destination --</option>
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->id }}" data-country="{{ $dest->country }}" {{ $transport->destination_id == $dest->id ? 'selected' : '' }}>
                                            {{ $dest->city }} ({{ $dest->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Route Details</label>
                                <input type="text" name="destination" class="form-control" value="{{ $transport->destination }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration / Hour</label>
                                <input type="text" name="duration" class="form-control" value="{{ $transport->duration }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                                <select name="vehicle_type" class="form-select" required>
                                    @foreach(['Sedan', 'SUV', 'Van', 'Mini Bus', 'Coaster', 'Luxury Car'] as $type)
                                        <option value="{{ $type }}" {{ $transport->vehicle_type == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Capacity (Pax)</label>
                                <input type="number" name="capacity" class="form-control" value="{{ $transport->capacity }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" name="base_price" class="form-control" value="{{ $transport->base_price }}" step="0.01" required>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" {{ $transport->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="activeCheck">Active (Visible in builder)</label>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Update Transport</button>
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

        // Don't reset if it's the initial load and we have a match
        // But for simplicity, we just filter
        for (var i = 1; i < options.length; i++) {
            var opt = options[i];
            if (!country || opt.getAttribute('data-country') === country) {
                opt.style.display = "";
            } else {
                opt.style.display = "none";
            }
        }
    }
    // Run on load to ensure filter is correct
    document.addEventListener('DOMContentLoaded', filterDestinations);
</script>
@endpush
@endsection