@extends('layouts.admin')

@section('title', 'Edit Entry Ticket')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-pencil-square me-2"></i>Edit Entry Ticket</h2>
                <p class="text-muted mb-0">Update ticket pricing details</p>
            </div>
            <a href="{{ route('admin.entry-tickets.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('admin.entry-tickets.update', $entryTicket->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Attraction Name <span class="text-danger">*</span></label>
                            <input type="text" name="attraction_name" class="form-control"
                                value="{{ $entryTicket->attraction_name }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country Filter <span class="text-danger">*</span></label>
                                <select id="countrySelect" class="form-select" onchange="filterDestinations()" required>
                                    <option value="">-- Select Country --</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->name }}" {{ ($entryTicket->destination && $entryTicket->destination->country == $country->name) ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Destination <span class="text-danger">*</span></label>
                                <select name="destination_id" id="destinationSelect" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->id }}" data-country="{{ $dest->country }}" {{ $entryTicket->destination_id == $dest->id ? 'selected' : '' }}>
                                            {{ $dest->city }} ({{ $dest->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Adult Price ($) <span class="text-danger">*</span></label>
                                <input type="number" name="adult_price" class="form-control"
                                    value="{{ $entryTicket->adult_price }}" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Child Price ($)</label>
                                <input type="number" name="child_price" class="form-control"
                                    value="{{ $entryTicket->child_price }}" step="0.01">
                            </div>
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" {{ $entryTicket->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="activeCheck">Active (Visible in builder)</label>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Update Entry Ticket</button>
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