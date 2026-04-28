@extends('layouts.admin')

@section('title', 'Add New Hotel')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-plus-circle me-2"></i>Add Hotel</h2>
                <p class="text-muted mb-0">Define hotel and its room categories</p>
            </div>
            <a href="{{ route('admin.hotels.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('admin.hotels.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Hotel Info -->
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Hotel Profile</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Hotel Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Country Filter <span class="text-danger">*</span></label>
                            <select id="countrySelect" class="form-select" onchange="filterDestinations()" required>
                                <option value="">-- Select Country --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->name }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">City/Destination <span class="text-danger">*</span></label>
                            <select name="destination_id" id="destinationSelect" class="form-select" required>
                                <option value="">-- Select Destination --</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" data-country="{{ $dest->country }}">{{ $dest->city }} ({{ $dest->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Star Rating</label>
                            <select name="star_rating" class="form-select">
                                <option value="1">1 Star</option>
                                <option value="2">2 Stars</option>
                                <option value="3" selected>3 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="5">5 Stars</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-shop me-1"></i> Supplier / Vendor</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">— Select Hotel Vendor —</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Link this hotel to a vendor from your Supplier Master</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Currency <span class="text-danger">*</span></label>
                            <select name="currency" id="hotel-currency" class="form-select" required>
                                <option value="MYR" selected>MYR - Malaysian Ringgit</option>
                                <option value="INR">INR - Indian Rupee</option>
                                <option value="USD">USD - US Dollar</option>
                                <option value="SGD">SGD - Singapore Dollar</option>
                                <option value="AED">AED - UAE Dirham</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-shop me-1"></i> Supplier / Vendor</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">— None / Walk-in —</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Link to a Hotel supplier from your Supplier Master</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Types -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Room Categories & Pricing</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRoomRow()">
                            <i class="bi bi-plus"></i> Add Room Type
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="rooms-container">
                            <div class="room-row row g-2 mb-3 border-bottom pb-3">
                                <div class="col-md-5">
                                    <label class="small text-muted">Room Type (e.g. Deluxe)</label>
                                    <input type="text" name="rooms[0][room_type]" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-muted">Base Price (Per Night)</label>
                                    <div class="input-group">
                                        <span class="input-group-text currency-label">MYR</span>
                                        <input type="number" name="rooms[0][base_price]" class="form-control" value="0.00"
                                            step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Capacity</label>
                                    <input type="number" name="rooms[0][capacity]" class="form-control" value="2">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger w-100"
                                        onclick="this.closest('.room-row').remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-3">
                        <button type="submit" class="btn btn-primary w-100">Save Hotel & Rooms</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            let roomIndex = 1;
            // Run on load
            document.addEventListener('DOMContentLoaded', function() {
                // Update currency labels
                const currencySelect = document.getElementById('hotel-currency');
                if (currencySelect) {
                    currencySelect.addEventListener('change', function() {
                        const labels = document.querySelectorAll('.currency-label');
                        labels.forEach(label => label.textContent = this.value);
                    });
                }
            });

            function addRoomRow() {
                const container = document.getElementById('rooms-container');
                const currency = document.getElementById('hotel-currency').value;
                const row = document.createElement('div');
                row.className = 'room-row row g-2 mb-3 border-bottom pb-3';
                row.innerHTML = `
                            <div class="col-md-5">
                                <input type="text" name="rooms[${roomIndex}][room_type]" class="form-control" placeholder="Room Type" required>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text currency-label">${currency}</span>
                                    <input type="number" name="rooms[${roomIndex}][base_price]" class="form-control" value="0.00" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="rooms[${roomIndex}][capacity]" class="form-control" value="2">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.room-row').remove()"><i class="bi bi-trash"></i></button>
                            </div>
                        `;
                container.appendChild(row);
                roomIndex++;
            }

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