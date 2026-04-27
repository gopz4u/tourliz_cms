@extends('layouts.admin')

@section('title', 'Add New Activity')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-plus-circle me-2"></i>Add Activity</h2>
                <p class="text-muted mb-0">Define a new sightseeing or tour activity</p>
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
                    <form action="{{ route('admin.activities.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Activity Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-shop me-1"></i> Supplier / Vendor</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">— None / Walk-in —</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
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
                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City/Destination <span class="text-danger">*</span></label>
                                <select name="destination_id" id="destinationSelect" class="form-select" required>
                                    <option value="">-- Select Destination --</option>
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->id }}" data-country="{{ $dest->country }}">{{ $dest->city }} ({{ $dest->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duration (e.g. 4 Hours, Full Day)</label>
                            <input type="text" name="duration" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Base Price ($) <span class="text-danger">*</span></label>
                            <input type="number" name="base_price" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Save Activity</button>
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