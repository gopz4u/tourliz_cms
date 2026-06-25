<!-- Shared Modal for Inventory Selection -->
<div class="modal fade" id="inventoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow border-0">
            <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="inventoryModalTitle" style="color: var(--bs-primary);">Select Item</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light rounded-bottom-4 mt-3">
                <div id="inventorySearchBox" class="mb-4 d-flex flex-wrap gap-2">
                    <select id="inventoryCountrySelect" class="form-select border-0 shadow-sm rounded-pill px-3" style="max-width: 180px;" onchange="filterInventoryCities()">
                        <option value="" data-country-id="">All Countries</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->name }}" data-country-id="{{ $country->id }}" {{ (isset($itinerary->destination) && $itinerary->destination->name == $country->name) ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    <select id="inventoryDestinationId" class="form-select border-0 shadow-sm rounded-pill px-3" style="max-width: 180px;">
                        <option value="">All Cities</option>
                        @foreach($destinations as $destination)
                            <option value="{{ $destination->id }}" data-country="{{ $destination->country }}">{{ $destination->city }}</option>
                        @endforeach
                    </select>
                    <div class="input-group flex-grow-1 shadow-sm rounded-pill overflow-hidden bg-white">
                        <span class="input-group-text bg-white border-0 text-muted px-3"><i class="bi bi-search"></i></span>
                        <input type="text" id="inventorySearch" class="form-control border-0 shadow-none px-2" placeholder="Search Master Inventory...">
                    </div>
                </div>
                <div id="inventoryResults" class="list-group list-group-flush rounded-3 px-2">
                    <!-- Results injected here -->
                </div>
            </div>
        </div>
    </div>
</div>