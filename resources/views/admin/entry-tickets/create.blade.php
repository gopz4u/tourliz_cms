@extends('layouts.admin')

@section('title', 'Add New Entry Ticket')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-plus-circle me-2"></i>Add Entry Ticket</h2>
                <p class="text-muted mb-0">Define ticket pricing for an attraction</p>
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
                    <form action="{{ route('admin.entry-tickets.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Attraction Name <span class="text-danger">*</span></label>
                            <input type="text" name="attraction_name" class="form-control" required>
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
                                <label class="form-label">Destination <span class="text-danger">*</span></label>
                                <select name="destination_id" id="destinationSelect" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->id }}" data-country="{{ $dest->country }}">{{ $dest->city }} ({{ $dest->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-shop me-1"></i> Supplier / Vendor</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">— None / Walk-in —</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Link to an Activity/Ticket supplier from your Supplier Master</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Adult Price ($) <span class="text-danger">*</span></label>
                                <input type="number" name="adult_price" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Child Price ($)</label>
                                <input type="number" name="child_price" class="form-control" step="0.01">
                            </div>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Save Entry Ticket</button>
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