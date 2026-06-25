@extends('layouts.admin')

@section('title', 'Edit Tourist Spot')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-pencil-square me-2"></i>Edit Tourist Spot</h2>
                <p class="text-muted mb-0">Update attraction details</p>
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
                    <form action="{{ route('admin.tourist-spots.update', $touristSpot->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Spot Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $touristSpot->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Country</label>
                            <select name="country_id" id="countrySelect" class="form-select"
                                onchange="filterDestinations()">
                                <option value="">-- Select Country (Optional) --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" data-country-name="{{ $country->name }}" {{ $touristSpot->country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">City / Destination <span class="text-muted">(Optional)</span></label>
                            <select name="destination_id" id="destinationSelect" class="form-select">
                                <option value="">-- Any / Country-level --</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" data-country="{{ $dest->country }}" {{ $touristSpot->destination_id == $dest->id ? 'selected' : '' }}>
                                        {{ $dest->city }} ({{ $dest->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"
                                rows="4">{{ $touristSpot->description }}</textarea>
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" {{ $touristSpot->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="activeCheck">Active (Visible in builder)</label>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Update Tourist Spot</button>
                        </div>
                    </form>
                </div>
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

            var currentlySelected = destinationSelect.value;
            var validSelection = false;

            for (var i = 1; i < options.length; i++) {
                var opt = options[i];
                if (!countryName || opt.getAttribute('data-country') === countryName) {
                    opt.style.display = "";
                    if (opt.value == currentlySelected) validSelection = true;
                } else {
                    opt.style.display = "none";
                }
            }

            if (!validSelection && countryName !== '') {
                destinationSelect.value = "";
            }
        }

        // Run on load
        document.addEventListener('DOMContentLoaded', function () {
            filterDestinations();
        });
    </script>
@endpush