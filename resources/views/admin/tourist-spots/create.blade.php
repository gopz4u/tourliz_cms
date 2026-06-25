@extends('layouts.admin')

@section('title', 'Add Tourist Spot')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-plus-circle me-2"></i>Add Tourist Spot</h2>
                <p class="text-muted mb-0">Define a new attraction for a destination</p>
            </div>
            <a href="{{ route('admin.tourist-spots.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('admin.tourist-spots.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Spot Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Country</label>
                            <select name="country_id" id="countrySelect" class="form-select"
                                onchange="filterDestinations()">
                                <option value="">-- Select Country (Optional) --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" data-country-name="{{ $country->name }}">
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">City / Destination <span class="text-muted">(Optional)</span></label>
                            <select name="destination_id" id="destinationSelect" class="form-select">
                                <option value="">-- Any / Country-level --</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" data-country="{{ $dest->country }}">{{ $dest->city }}
                                        ({{ $dest->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-shop me-1"></i> Supplier / Vendor</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">— None / Walk-in —</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Link to a supplier from your Supplier Master</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"
                                placeholder="Brief info about this attraction..."></textarea>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Save Tourist Spot</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function filterDestinations() {
            var countrySelect = document.getElementById('countrySelect');
            var selectedOption = countrySelect.options[countrySelect.selectedIndex];
            var countryName = selectedOption ? (selectedOption.getAttribute('data-country-name') || '') : '';
            var destinationSelect = document.getElementById('destinationSelect');
            var options = destinationSelect.options;

            destinationSelect.value = "";

            for (var i = 1; i < options.length; i++) {
                var opt = options[i];
                if (!countryName || opt.getAttribute('data-country') === countryName) {
                    opt.style.display = "";
                } else {
                    opt.style.display = "none";
                }
            }
        }
    </script>
@endpush