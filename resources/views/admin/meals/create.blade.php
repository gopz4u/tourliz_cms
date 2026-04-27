@extends('layouts.admin')

@section('title', 'Add New Meal Option')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-plus-circle me-2"></i>Add Meal Option</h2>
                <p class="text-muted mb-0">Define a new meal plan or special menu</p>
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
                    <form action="{{ route('admin.meals.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Meal Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Seafood Dinner Buffet"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meal Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="Breakfast">Breakfast</option>
                                <option value="Lunch">Lunch</option>
                                <option value="Dinner">Dinner</option>
                                <option value="Snack">Snack</option>
                                <option value="All Inclusive">All Inclusive</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country Filter</label>
                                <select id="countrySelect" class="form-select" onchange="filterDestinations()">
                                    <option value="">Global (All Countries)</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Destination (Optional)</label>
                                <select name="destination_id" id="destinationSelect" class="form-select">
                                    <option value="">Global (All Destinations)</option>
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->id }}" data-country="{{ $dest->country }}">{{ $dest->city }} ({{ $dest->name }})</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Choose a destination if this meal is specific to a place.</div>
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
                            <small class="form-text text-muted">Link to a supplier from your Supplier Master</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price ($) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" step="0.01" value="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Save Meal Option</button>
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

        // option 0 is Global, so we start checking at 1
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