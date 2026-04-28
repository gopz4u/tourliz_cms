@extends('layouts.admin')

@section('title', 'Edit Hotel')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-pencil-square me-2"></i>Edit Hotel</h2>
                <p class="text-muted mb-0">Update hotel profile and room rates</p>
            </div>
            <a href="{{ route('admin.hotels.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('admin.hotels.update', $hotel->id) }}" method="POST">
        @csrf
        @method('PUT')
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
                            <input type="text" name="name" class="form-control" value="{{ $hotel->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Country Filter <span class="text-danger">*</span></label>
                            <select id="countrySelect" class="form-select" onchange="filterDestinations()" required>
                                <option value="">-- Select Country --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->name }}" {{ ($hotel->destination && $hotel->destination->country == $country->name) ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">City/Destination <span class="text-danger">*</span></label>
                            <select name="destination_id" id="destinationSelect" class="form-select" required>
                                <option value="">-- Select Destination --</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" data-country="{{ $dest->country }}" {{ $hotel->destination_id == $dest->id ? 'selected' : '' }}>
                                        {{ $dest->city }} ({{ $dest->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Star Rating</label>
                            <select name="star_rating" class="form-select">
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ $hotel->star_rating == $i ? 'selected' : '' }}>{{ $i }} Stars
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Currency <span class="text-danger">*</span></label>
                            <select name="currency" id="hotel-currency" class="form-select" required>
                                <option value="MYR" {{ $hotel->currency == 'MYR' ? 'selected' : '' }}>MYR - Malaysian Ringgit</option>
                                <option value="INR" {{ $hotel->currency == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                <option value="USD" {{ $hotel->currency == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="SGD" {{ $hotel->currency == 'SGD' ? 'selected' : '' }}>SGD - Singapore Dollar</option>
                                <option value="AED" {{ $hotel->currency == 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ $hotel->address }}</textarea>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" {{ $hotel->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="activeCheck">Active (Visible in builder)</label>
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
                            @foreach($hotel->rooms as $index => $room)
                                <div class="room-row row g-2 mb-3 border-bottom pb-3">
                                    <div class="col-md-5">
                                        <label class="small text-muted">Room Type</label>
                                        <input type="text" name="rooms[{{ $index }}][room_type]" class="form-control"
                                            value="{{ $room->room_type }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small text-muted">Base Price (<span class="currency-label">{{ $hotel->currency ?? 'MYR' }}</span>)</label>
                                        <input type="number" name="rooms[{{ $index }}][base_price]" class="form-control"
                                            value="{{ $room->base_price }}" step="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="small text-muted">Cap.</label>
                                        <input type="number" name="rooms[{{ $index }}][capacity]" class="form-control"
                                            value="{{ $room->capacity }}">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger w-100"
                                            onclick="this.closest('.room-row').remove()"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            @endforeach

                            @if($hotel->rooms->count() == 0)
                                <div class="room-row row g-2 mb-3 border-bottom pb-3">
                                    <div class="col-md-5">
                                        <input type="text" name="rooms[0][room_type]" class="form-control"
                                            placeholder="Room Type" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" name="rooms[0][base_price]" class="form-control" value="0.00"
                                            step="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="rooms[0][capacity]" class="form-control" value="2">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger w-100"
                                            onclick="this.closest('.room-row').remove()"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-white py-3">
                        <button type="submit" class="btn btn-primary w-100">Update Hotel & Rooms</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            let roomIndex = {{ count($hotel->rooms) > 0 ? count($hotel->rooms) : 1 }};
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
                                <label class="small text-muted">Base Price (<span class="currency-label">${currency}</span>)</label>
                                <input type="number" name="rooms[${roomIndex}][base_price]" class="form-control" value="0.00" step="0.01" required>
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

                // Update currency labels
                const currencySelect = document.getElementById('hotel-currency');
                if (currencySelect) {
                    currencySelect.addEventListener('change', function() {
                        const labels = document.querySelectorAll('.currency-label');
                        labels.forEach(label => label.textContent = this.value);
                    });
                }
            });
        </script>
    @endpush
@endsection