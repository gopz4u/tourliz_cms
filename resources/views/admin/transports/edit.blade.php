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

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Price <span class="text-danger">*</span></label>
                                <input type="number" name="base_price" id="base_price" class="form-control" value="{{ $transport->base_price }}" step="0.01" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Currency</label>
                                <select name="currency" id="currency" class="form-select">
                                    <option value="MYR" {{ $transport->currency == 'MYR' ? 'selected' : '' }}>MYR</option>
                                    <option value="INR" {{ $transport->currency == 'INR' ? 'selected' : '' }}>INR</option>
                                    <option value="USD" {{ $transport->currency == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="SGD" {{ $transport->currency == 'SGD' ? 'selected' : '' }}>SGD</option>
                                    <option value="AED" {{ $transport->currency == 'AED' ? 'selected' : '' }}>AED</option>
                                </select>
                            </div>
                        </div>

                        <!-- Real-time Multi-currency Preview -->
                        <div class="row mb-4" id="currency-preview-row" style="display: none;">
                            <div class="col-12">
                                <div class="bg-light p-2 rounded border">
                                    <div class="d-flex gap-3 overflow-auto pb-1" id="multi-currency-previews" style="font-size: 0.85rem;">
                                        <!-- Will be populated by JS -->
                                    </div>
                                </div>
                            </div>
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
    let cachedRates = [];
    function updateCurrencyPreview() {
        const price = parseFloat($('#base_price').val()) || 0;
        const currentCurrency = $('#currency').val();
        const container = $('#multi-currency-previews');
        
        if (price <= 0 || cachedRates.length === 0) {
            $('#currency-preview-row').hide();
            return;
        }

        $('#currency-preview-row').show();
        container.empty();

        // Find rate for current currency
        const currentRateObj = cachedRates.find(r => (r.code || r.currency_code) === currentCurrency) || { exchange_rate: 1 };
        const currentRate = parseFloat(currentRateObj.exchange_rate || currentRateObj.rate_to_inr);
        const priceInMYR = price * currentRate;

        cachedRates.forEach(rate => {
            const code = rate.code || rate.currency_code;
            if (code === currentCurrency) return;
            
            const convertedPrice = priceInMYR / parseFloat(rate.exchange_rate || rate.rate_to_inr);
            const symbol = getCurrencySymbol(code);
            
            container.append(`
                <div class="flex-shrink-0">
                    <div class="text-uppercase text-muted" style="font-size: 9px; font-weight: 800;">${code}</div>
                    <div class="fw-bold text-dark">${symbol} ${convertedPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                </div>
            `);
        });
    }

    function getCurrencySymbol(code) {
        const symbols = { 'INR': '₹', 'USD': '$', 'MYR': 'RM', 'SGD': 'S$', 'AED': 'AED' };
        return symbols[code] || code;
    }

    // Run on load to ensure filter is correct
    document.addEventListener('DOMContentLoaded', function() {
        filterDestinations();
        
        // Fetch exchange rates for real-time preview
        $.get('/api/v1/currency/rates', function(response) {
            if (response.success) {
                cachedRates = response.rates;
                updateCurrencyPreview();
            }
        });

        $('#base_price, #currency').on('input change', updateCurrencyPreview);
    });
</script>
@endpush
@endsection