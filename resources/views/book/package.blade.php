<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Book Package - {{ $package->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .price-breakdown {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Book Package</h3>
            <small class="text-muted">{{ $package->name }} @if($package->destination) • {{ $package->destination->name }} @endif</small>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('book.package.submit') }}" id="bookingForm">
        @csrf
        <input type="hidden" name="package_id" value="{{ $package->id }}">

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="section-title">Customer Information</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1234567890">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Travel Date <span class="text-danger">*</span></label>
                        <input type="date" name="travel_date" class="form-control" value="{{ old('travel_date') }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Adults <span class="text-danger">*</span></label>
                        <input type="number" name="adults" class="form-control" min="1" max="20" value="{{ old('adults', 1) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Children</label>
                        <input type="number" name="children" class="form-control" min="0" max="20" value="{{ old('children', 0) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="section-title">Customer Address</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="customer_address" class="form-control" value="{{ old('customer_address') }}" placeholder="Street address">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="customer_city" class="form-control" value="{{ old('customer_city') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State/Province</label>
                        <input type="text" name="customer_state" class="form-control" value="{{ old('customer_state') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Postal Code</label>
                        <input type="text" name="customer_postal_code" class="form-control" value="{{ old('customer_postal_code') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <input type="text" name="customer_country" class="form-control" value="{{ old('customer_country') }}">
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($package->addon_amenities) && is_array($package->addon_amenities))
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="section-title">Package Add-ons</h5>
                <p class="text-muted">Select any additional add-ons for your package</p>
                <div class="row">
                    @foreach($package->addon_amenities as $key => $addon)
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input addon-checkbox" type="checkbox" name="addons[]" value="{{ $key }}" id="addon_{{ $key }}" 
                                data-price="{{ $addon['price'] ?? 0 }}" {{ in_array($key, old('addons', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="addon_{{ $key }}">
                                {{ $addon['name'] ?? $key }}
                                @if(isset($addon['price']) && $addon['price'] > 0)
                                    <span class="text-success">(+{{ $package->currency ?? 'USD' }} {{ number_format($addon['price'], 2) }})</span>
                                @endif
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($services && $services->count() > 0)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="section-title">Add-on Services</h5>
                <p class="text-muted">Select additional services</p>
                <div class="row">
                    @foreach($services as $service)
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input service-checkbox" type="checkbox" name="addon_services[]" value="{{ $service->id }}" 
                                id="service_{{ $service->id }}" data-price="{{ $service->price ?? 0 }}" 
                                {{ in_array($service->id, old('addon_services', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="service_{{ $service->id }}">
                                {{ $service->name }}
                                @if($service->price > 0)
                                    <span class="text-success">({{ $service->currency ?? 'USD' }} {{ number_format($service->price, 2) }})</span>
                                @endif
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="section-title">Payment Information</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-select" id="payment_status">
                            <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partially_paid" {{ old('payment_status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Method</label>
                        <input type="text" name="payment_method" class="form-control" value="{{ old('payment_method') }}" 
                            placeholder="e.g., Credit Card, PayPal, Bank Transfer" id="payment_method">
                    </div>
                    <div class="col-md-6" id="payment_transaction_field" style="display: none;">
                        <label class="form-label">Transaction ID</label>
                        <input type="text" name="payment_transaction_id" class="form-control" value="{{ old('payment_transaction_id') }}">
                    </div>
                    <div class="col-md-6" id="payment_amount_field" style="display: none;">
                        <label class="form-label">Payment Amount</label>
                        <input type="number" name="payment_amount" class="form-control" value="{{ old('payment_amount') }}" step="0.01" min="0">
                    </div>
                    <div class="col-md-6" id="payment_date_field" style="display: none;">
                        <label class="form-label">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="section-title">Contact Preference</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Preferred Contact Method</label>
                        <select name="contact_method" class="form-select" id="contact_method">
                            <option value="email" {{ old('contact_method', 'email') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="whatsapp" {{ old('contact_method') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                            <option value="phone" {{ old('contact_method') == 'phone' ? 'selected' : '' }}>Phone Call</option>
                            <option value="query" {{ old('contact_method') == 'query' ? 'selected' : '' }}>Query Form</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="whatsapp_field" style="display: none;">
                        <label class="form-label">WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number') }}" 
                            placeholder="+1234567890">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="section-title">Additional Notes</h5>
                <textarea name="notes" rows="4" class="form-control" placeholder="Extra details, preferences, special requests, or questions">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="price-breakdown">
                    <div class="mb-3">
                        <label class="form-label">Coupon Code</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="coupon_code" id="coupon_code" placeholder="Enter coupon code">
                            <button class="btn btn-primary" type="button" id="apply_coupon">Apply</button>
                        </div>
                        <div id="coupon_message" class="form-text mt-1"></div>
                    </div>

                    <h6 class="mb-3">Price Breakdown</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Base Price:</span>
                        <strong id="base_price">{{ $package->currency ?? 'USD' }} {{ number_format($package->price ?? 0, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success" id="addons_row" style="display: none;">
                        <span>Add-ons:</span>
                        <strong id="addons_amount">USD 0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success" id="services_row" style="display: none;">
                        <span>Services:</span>
                        <strong id="services_amount">USD 0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-danger" id="discount_row" style="display: none;">
                        <span>Discount:</span>
                        <strong id="discount_amount">USD 0.00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total Amount:</span>
                        <strong class="fs-5 text-primary" id="total_amount">{{ $package->currency ?? 'USD' }} {{ number_format($package->price ?? 0, 2) }}</strong>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-send"></i> Submit Booking Request
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const currency = '{{ $package->currency ?? "USD" }}';
    const basePrice = {{ $package->price ?? 0 }};
    let discountAmount = 0;
    
    // Calculate total price
    function calculateTotal() {
        let total = basePrice;
        let addonsAmount = 0;
        let servicesAmount = 0;
        
        // Calculate addons
        document.querySelectorAll('.addon-checkbox:checked').forEach(checkbox => {
            addonsAmount += parseFloat(checkbox.dataset.price || 0);
        });
        
        // Calculate services
        document.querySelectorAll('.service-checkbox:checked').forEach(checkbox => {
            servicesAmount += parseFloat(checkbox.dataset.price || 0);
        });
        
        total = basePrice + addonsAmount + servicesAmount - discountAmount;
        if (total < 0) total = 0;
        
        // Update display
        document.getElementById('base_price').textContent = currency + ' ' + basePrice.toFixed(2);
        document.getElementById('total_amount').textContent = currency + ' ' + total.toFixed(2);
        
        if (addonsAmount > 0) {
            document.getElementById('addons_amount').textContent = currency + ' ' + addonsAmount.toFixed(2);
            document.getElementById('addons_row').style.display = 'flex';
        } else {
            document.getElementById('addons_row').style.display = 'none';
        }
        
        if (servicesAmount > 0) {
            document.getElementById('services_amount').textContent = currency + ' ' + servicesAmount.toFixed(2);
            document.getElementById('services_row').style.display = 'flex';
        } else {
            document.getElementById('services_row').style.display = 'none';
        }

        if (discountAmount > 0) {
            document.getElementById('discount_amount').textContent = currency + ' -' + discountAmount.toFixed(2);
            document.getElementById('discount_row').style.display = 'flex';
        } else {
            document.getElementById('discount_row').style.display = 'none';
        }
    }
    
    // Add event listeners
    document.querySelectorAll('.addon-checkbox, .service-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotal);
    });
    
    // Show/hide payment fields
    document.getElementById('payment_status').addEventListener('change', function() {
        const paymentFields = ['payment_transaction_field', 'payment_amount_field', 'payment_date_field'];
        if (this.value === 'paid' || this.value === 'partially_paid') {
            paymentFields.forEach(field => {
                document.getElementById(field).style.display = 'block';
            });
            document.getElementById('payment_method').required = true;
        } else {
            paymentFields.forEach(field => {
                document.getElementById(field).style.display = 'none';
            });
            document.getElementById('payment_method').required = false;
        }
    });
    
    // Trigger payment status change on load
    document.getElementById('payment_status').dispatchEvent(new Event('change'));
    
    // Show/hide WhatsApp field
    document.getElementById('contact_method').addEventListener('change', function() {
        if (this.value === 'whatsapp') {
            document.getElementById('whatsapp_field').style.display = 'block';
            document.querySelector('[name="whatsapp_number"]').required = true;
        } else {
            document.getElementById('whatsapp_field').style.display = 'none';
            document.querySelector('[name="whatsapp_number"]').required = false;
        }
    });
    
    // Trigger contact method change on load
    document.getElementById('contact_method').dispatchEvent(new Event('change'));
    
    // Initial calculation
    calculateTotal();

    // Coupon Application
    document.getElementById('apply_coupon').addEventListener('click', function() {
        const couponCode = document.getElementById('coupon_code').value;
        const msgDiv = document.getElementById('coupon_message');
        
        if (!couponCode) {
            msgDiv.innerHTML = '<span class="text-danger">Please enter a coupon code.</span>';
            return;
        }

        // Calculate current subtotal first
        let currentTotal = basePrice;
        let addonsAmount = 0;
        let servicesAmount = 0;
        document.querySelectorAll('.addon-checkbox:checked').forEach(c => addonsAmount += parseFloat(c.dataset.price || 0));
        document.querySelectorAll('.service-checkbox:checked').forEach(c => servicesAmount += parseFloat(c.dataset.price || 0));
        currentTotal += addonsAmount + servicesAmount;

        // Reset discount
        discountAmount = 0;
        calculateTotal();
        msgDiv.innerHTML = '<span class="text-muted">Validating...</span>';

        fetch('{{ route("book.check-coupon") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                coupon_code: couponCode,
                amount: currentTotal
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                discountAmount = data.discount;
                msgDiv.innerHTML = `<span class="text-success">${data.message}</span>`;
                calculateTotal();
            } else {
                discountAmount = 0;
                msgDiv.innerHTML = `<span class="text-danger">${data.message}</span>`;
                calculateTotal();
            }
        })
        .catch(err => {
            console.error(err);
            msgDiv.innerHTML = '<span class="text-danger">Error validating coupon.</span>';
        });
    });

    // Reset discount on option change
    document.querySelectorAll('.addon-checkbox, .service-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (discountAmount > 0) {
                discountAmount = 0;
                document.getElementById('coupon_code').value = '';
                document.getElementById('coupon_message').innerHTML = '<span class="text-warning">Options changed. Please re-apply coupon.</span>';
            }
        });
    });
</script>
</body>
</html>
