@extends('layouts.admin')

@section('title', 'Add New Transport')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-plus-circle me-2"></i>Add Transport</h2>
                <p class="text-muted mb-0">Define a new transport option</p>
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
                    <form action="{{ route('admin.transports.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Vendor <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Select Vendor</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Service Name (e.g. Airport Pickup, Full Day) <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country Filter <span class="text-danger">*</span></label>
                                <select id="countrySelect" class="form-select" onchange="filterDestinations()" required>
                                    <option value="">-- Select Country --</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City / Destination <span class="text-danger">*</span></label>
                                <select name="destination_id" id="destinationSelect" class="form-select" required>
                                    <option value="">-- Select Destination --</option>
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->id }}" data-country="{{ $dest->country }}">{{ $dest->city }} ({{ $dest->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Route Details (e.g. Airport - Hotel)</label>
                                <input type="text" name="destination" class="form-control" placeholder="e.g. Airport - Hotel">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration / Hour (Optional)</label>
                                <input type="text" name="duration" class="form-control" placeholder="e.g. 4 Hours, Transfer">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                                <select name="vehicle_type" class="form-select" required>
                                    <option value="Sedan">Sedan</option>
                                    <option value="SUV">SUV</option>
                                    <option value="Van">Van</option>
                                    <option value="Mini Bus">Mini Bus</option>
                                    <option value="Coaster">Coaster</option>
                                    <option value="Luxury Car">Luxury Car</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Capacity (Pax)</label>
                                <input type="number" name="capacity" class="form-control" value="4">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" name="base_price" class="form-control" step="0.01" required>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Save Transport Logic</button>
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

        destinationSelect.value = ""; // Reset current selection

        for (var i = 1; i < options.length; i++) {
            var opt = options[i];
            if (!country || opt.getAttribute('data-country') === country) {
                opt.style.display = "";
            } else {
                opt.style.display = "none";
            }
        }
    }
</script>
@endpush
@endsection