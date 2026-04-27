@extends('layouts.admin')

@section('title', 'Edit Meal Option')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-pencil-square me-2"></i>Edit Meal Option</h2>
                <p class="text-muted mb-0">Update meal details and pricing</p>
            </div>
            <a href="{{ route('admin.meals.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('admin.meals.update', $meal->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Meal Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $meal->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meal Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                @foreach(['Breakfast', 'Lunch', 'Dinner', 'Snack', 'All Inclusive'] as $type)
                                    <option value="{{ $type }}" {{ $meal->type == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country Filter</label>
                                <select id="countrySelect" class="form-select" onchange="filterDestinations()">
                                    <option value="">Global (All Countries)</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->name }}" {{ ($meal->destination && $meal->destination->country == $country->name) ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Destination (Optional)</label>
                                <select name="destination_id" id="destinationSelect" class="form-select">
                                    <option value="">Global (All Destinations)</option>
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->id }}" data-country="{{ $dest->country }}" {{ $meal->destination_id == $dest->id ? 'selected' : '' }}>
                                            {{ $dest->city }} ({{ $dest->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price ($) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" value="{{ $meal->price }}" step="0.01"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $meal->description }}</textarea>
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" {{ $meal->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="activeCheck">Active (Visible in builder)</label>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Update Meal Option</button>
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
        var country = document.getElementById('countrySelect').value;
        var destinationSelect = document.getElementById('destinationSelect');
        var options = destinationSelect.options;

        var currentlySelected = destinationSelect.value;
        var validSelection = false;

        // Start from 1 to skip "Global"
        for (var i = 1; i < options.length; i++) {
            var opt = options[i];
            if (!country || opt.getAttribute('data-country') === country) {
                opt.style.display = "";
                if(opt.value == currentlySelected) validSelection = true;
            } else {
                opt.style.display = "none";
            }
        }

        if(!validSelection && country !== '' && currentlySelected !== '') {
            destinationSelect.value = "";
        }
    }

    // Run on load
    document.addEventListener('DOMContentLoaded', function() {
        filterDestinations();
    });
</script>
@endpush