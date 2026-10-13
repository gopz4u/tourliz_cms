<!-- Shared Inventory Selection Modal for Unified B2B & B2C Itineraries -->
<div class="modal fade" id="inventoryModal" tabindex="-1" aria-labelledby="inventoryModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 shadow border-0">
            <div class="modal-header border-bottom pb-3 px-4 pt-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-primary mb-1" id="inventoryModalTitle">
                        <i class="bi bi-box-seam me-2"></i>Select from Operations Master
                    </h5>
                    <small class="text-muted" id="inventoryModalSubtitle">Browse live supplier rates, room variants, and attraction tickets</small>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div id="inventorySearchBox" class="mb-3 d-flex flex-wrap gap-2">
                    <select id="inventoryCountrySelect" class="form-select shadow-sm" style="max-width: 170px;" onchange="filterInventoryCities(); fetchInventoryItems();">
                        <option value="" data-country-id="">All Countries</option>
                        @if(isset($countries))
                            @foreach($countries as $country)
                                <option value="{{ $country->name }}" data-country-id="{{ $country->id }}" {{ (isset($itinerary->destination) && ($itinerary->destination->country == $country->name || $itinerary->destination->name == $country->name)) ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    <select id="inventoryDestinationId" class="form-select shadow-sm" style="max-width: 170px;" onchange="fetchInventoryItems()">
                        <option value="">All Destinations</option>
                        @if(isset($destinations))
                            @foreach($destinations as $destination)
                                <option value="{{ $destination->id }}" data-country="{{ $destination->country }}" {{ (isset($itinerary->destination_id) && $itinerary->destination_id == $destination->id) ? 'selected' : '' }}>
                                    {{ $destination->name ?: $destination->city }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    <div class="input-group flex-grow-1 shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="inventorySearch" class="form-control border-start-0" placeholder="Search by name, attraction, vehicle..." onkeyup="debounceFetchInventory()">
                        <button type="button" class="btn btn-primary px-3" onclick="fetchInventoryItems()">Search</button>
                    </div>

                    <button type="button" class="btn btn-outline-success shadow-sm fw-semibold" onclick="toggleCustomInventoryForm()" style="white-space: nowrap;">
                        <i class="bi bi-plus-circle me-1"></i>+ Custom Item
                    </button>
                </div>

                <!-- Custom Item & Supplier Entry Form -->
                <div id="customInventoryBox" class="card p-3 mb-3 border-success border-opacity-50 bg-white shadow-sm d-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-success mb-0"><i class="bi bi-pencil-square me-1"></i>Add Custom Item / Unlisted Supplier</h6>
                        <button type="button" class="btn-close btn-sm shadow-none" onclick="toggleCustomInventoryForm()"></button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold mb-1">Item Title / Attraction / Vehicle <span class="text-danger">*</span></label>
                            <input type="text" id="custom_inv_title" class="form-control form-control-sm" placeholder="e.g. Grand Ballroom / Private Van / Sunset Cruise">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-1">Supplier / Vendor Name</label>
                            <input type="text" id="custom_inv_supplier" class="form-control form-control-sm" placeholder="e.g. Local Operator / In-house">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold mb-1">Estimated Cost / Price</label>
                            <input type="number" step="0.01" min="0" id="custom_inv_price" class="form-control form-control-sm" placeholder="0.00">
                        </div>
                        <div class="col-12 text-end mt-2">
                            <button type="button" class="btn btn-sm btn-success px-3 fw-bold" onclick="addCustomInventoryItem()">
                                <i class="bi bi-plus-lg me-1"></i>Add to Day Schedule
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Live Results -->
                <div id="inventoryResults" class="rounded-3" style="min-height: 250px;">
                    <div class="text-center p-5 text-muted">
                        <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                        <span>Connecting to Operations inventory...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white py-2 px-4 border-top">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentDayIndex = 0;
    let currentItemType = 'hotels';
    let inventoryModalInstance = null;
    let fetchTimeout = null;

    function getActiveCurrencyInfo() {
        const curSelect = document.querySelector('select[name="currency"]') || document.getElementById('currencySelect');
        const code = (curSelect ? curSelect.value : 'MYR') || 'MYR';
        let symbol = 'RM';
        if (code === 'INR') symbol = '₹';
        else if (code === 'USD') symbol = '$';
        else if (code === 'SGD') symbol = 'S$';
        else if (code === 'AED') symbol = 'AED';
        else if (code === 'EUR') symbol = '€';
        else if (code === 'GBP') symbol = '£';
        return { code, symbol };
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('inventoryModal');
        if (modalEl) {
            inventoryModalInstance = new bootstrap.Modal(modalEl);
        }

        const curSelect = document.querySelector('select[name="currency"]');
        if (curSelect) {
            curSelect.addEventListener('change', function () {
                const info = getActiveCurrencyInfo();
                document.querySelectorAll('.currency-rate-label').forEach(el => {
                    el.textContent = `Rate ${info.symbol}`;
                });
            });
        }
    });

    function openInventoryModal(dayIndex, type) {
        currentDayIndex = dayIndex;
        currentItemType = type;

        const typeLabels = {
            'hotels': 'Hotel & Room Types',
            'transports': 'Transport & Transfer Vehicles',
            'tickets': 'Attraction Entry Tickets',
            'spots': 'Tourist Spots & Sightseeing',
            'activities': 'Activities & Day Tours',
            'meals': 'Meal Plans & Dining'
        };

        const titleEl = document.getElementById('inventoryModalTitle');
        if (titleEl) {
            titleEl.innerHTML = `<i class="bi bi-box-seam me-2"></i>Select ${typeLabels[type] || 'Item'} (Day ${parseInt(dayIndex) + 1})`;
        }

        filterInventoryCities();
        fetchInventoryItems();

        const modalEl = document.getElementById('inventoryModal');
        if (modalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                inventoryModalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                inventoryModalInstance.show();
            } else if (typeof $ !== 'undefined' && typeof $('#inventoryModal').modal === 'function') {
                $('#inventoryModal').modal('show');
            }
        }
    }

    function filterInventoryCities() {
        const countryVal = document.getElementById('inventoryCountrySelect')?.value || '';
        const destSelect = document.getElementById('inventoryDestinationId');
        if (!destSelect) return;

        const options = destSelect.querySelectorAll('option');
        options.forEach(opt => {
            if (opt.value === '') {
                opt.style.display = '';
                return;
            }
            const optCountry = opt.getAttribute('data-country');
            if (!countryVal || optCountry === countryVal) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    function debounceFetchInventory() {
        clearTimeout(fetchTimeout);
        fetchTimeout = setTimeout(fetchInventoryItems, 350);
    }

    function fetchInventoryItems() {
        const dSelect = document.getElementById('inventoryDestinationId');
        const selectedDestinationId = dSelect ? dSelect.value : "";
        const countrySelectEl = document.getElementById('inventoryCountrySelect');
        const countryFilter = countrySelectEl ? countrySelectEl.value : "";
        const countryId = countrySelectEl ? (countrySelectEl.options[countrySelectEl.selectedIndex]?.getAttribute('data-country-id') || '') : '';
        const search = document.getElementById('inventorySearch')?.value || "";

        let url = `/api/inventory/${currentItemType}?search=${encodeURIComponent(search)}`;
        if (selectedDestinationId) {
            url += `&destination_id=${selectedDestinationId}`;
        } else if (countryId && currentItemType === 'spots') {
            url += `&country_id=${countryId}`;
        } else if (countryFilter) {
            url += `&country=${encodeURIComponent(countryFilter)}`;
        }

        const container = document.getElementById('inventoryResults');
        if (!container) return;
        container.innerHTML = '<div class="text-center p-5 text-muted"><div class="spinner-border text-primary spinner-border-sm me-2"></div><span>Loading real items from Operations...</span></div>';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = '';
                if (!data || data.length === 0) {
                    container.innerHTML = `
                        <div class="text-center p-5 bg-white rounded border">
                            <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">No items found in Master Inventory</h6>
                            <p class="text-muted small mb-2">No matching records found for this destination or search.</p>
                            <button type="button" class="btn btn-sm btn-outline-success px-3" onclick="toggleCustomInventoryForm()">
                                <i class="bi bi-plus-circle me-1"></i>Add as Custom Item / Supplier
                            </button>
                        </div>
                    `;
                    return;
                }

                const listGroup = document.createElement('div');
                listGroup.className = 'd-flex flex-column gap-2';

                data.forEach(item => {
                    const card = document.createElement('div');
                    card.className = 'card border bg-white p-3 shadow-xs';

                    const curSymbol = getActiveCurrencyInfo().symbol;
                    if (currentItemType === 'hotels') {
                        let roomsHtml = '';
                        if (item.rooms && item.rooms.length > 0) {
                            roomsHtml = `<div class="mt-2 pt-2 border-top"><small class="text-muted fw-bold d-block mb-1">Available Room Types:</small><div class="d-flex flex-wrap gap-2">`;
                            item.rooms.forEach(room => {
                                roomsHtml += `
                                    <button type="button" class="btn btn-sm btn-outline-primary text-start" onclick='selectInventoryItem(${JSON.stringify(item).replace(/'/g, "&#39;")}, ${JSON.stringify(room).replace(/'/g, "&#39;")})'>
                                        <span class="fw-bold">${room.room_type}</span> — <span class="badge bg-primary text-white">${curSymbol} ${room.price || room.base_price || 0}</span>
                                    </button>
                                `;
                            });
                            roomsHtml += `</div></div>`;
                        } else {
                            roomsHtml = `<div class="mt-2"><button type="button" class="btn btn-sm btn-primary" onclick='selectInventoryItem(${JSON.stringify(item).replace(/'/g, "&#39;")})'>Select Hotel</button></div>`;
                        }

                        card.innerHTML = `
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">${item.name}</h6>
                                    <div class="small text-muted">
                                        <span class="badge bg-warning text-dark me-2">${item.star_rating || 3} <i class="bi bi-star-fill small"></i></span>
                                        ${item.destination ? `<span class="me-2"><i class="bi bi-geo-alt"></i> ${item.destination}</span>` : ''}
                                        ${item.supplier ? `<span class="text-secondary"><i class="bi bi-building"></i> Supplier: ${item.supplier.name}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                            ${roomsHtml}
                        `;
                    } else if (currentItemType === 'transports') {
                        card.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">${item.name}</h6>
                                    <div class="small text-muted">
                                        <span class="badge bg-secondary me-2">${item.vehicle_type || 'Vehicle'}</span>
                                        ${item.capacity ? `<span class="me-2"><i class="bi bi-people"></i> Capacity: ${item.capacity} pax</span>` : ''}
                                        ${item.supplier ? `<span class="text-secondary"><i class="bi bi-building"></i> ${item.supplier.name}</span>` : ''}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success fs-6 mb-1">${curSymbol} ${item.price || item.base_price || 0}</div>
                                    <button type="button" class="btn btn-sm btn-primary px-3" onclick='selectInventoryItem(${JSON.stringify(item).replace(/'/g, "&#39;")})'>Select</button>
                                </div>
                            </div>
                        `;
                    } else if (currentItemType === 'tickets') {
                        card.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">${item.attraction_name || item.name}</h6>
                                    <div class="small text-muted">
                                        <span class="badge bg-danger me-2">ENTRY TICKET</span>
                                        ${item.supplier ? `<span class="text-secondary"><i class="bi bi-building"></i> ${item.supplier.name}</span>` : ''}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-danger fs-6 mb-1">Adult: ${curSymbol} ${item.adult_price || 0} ${item.child_price ? `| Child: ${curSymbol} ${item.child_price}` : ''}</div>
                                    <button type="button" class="btn btn-sm btn-danger px-3" onclick='selectInventoryItem(${JSON.stringify(item).replace(/'/g, "&#39;")})'>Select</button>
                                </div>
                            </div>
                        `;
                    } else if (currentItemType === 'spots') {
                        card.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">${item.name}</h6>
                                    <div class="small text-muted text-truncate" style="max-width: 450px;">${item.description || 'Sightseeing point'}</div>
                                    <div class="small text-secondary mt-1">
                                        ${item.destination ? `<span class="badge bg-info text-white me-1">${item.destination}</span>` : ''}
                                        ${item.country_name ? `<span class="badge bg-light text-dark border">${item.country_name}</span>` : ''}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-sm btn-info text-white px-3" onclick='selectInventoryItem(${JSON.stringify(item).replace(/'/g, "&#39;")})'>Add Spot</button>
                                </div>
                            </div>
                        `;
                    } else if (currentItemType === 'activities') {
                        card.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">${item.name}</h6>
                                    <div class="small text-muted">${item.duration || 'Day Activity'}</div>
                                    ${item.supplier ? `<small class="text-secondary d-block"><i class="bi bi-building"></i> ${item.supplier.name}</small>` : ''}
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-warning text-dark fs-6 mb-1">₹ ${item.base_price || item.adult_price || 0}</div>
                                    <button type="button" class="btn btn-sm btn-warning text-dark px-3" onclick='selectInventoryItem(${JSON.stringify(item).replace(/'/g, "&#39;")})'>Select</button>
                                </div>
                            </div>
                        `;
                    } else if (currentItemType === 'meals') {
                        card.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">${item.name}</h6>
                                    <span class="badge bg-success text-uppercase">${item.meal_type || item.type || 'Meal'}</span>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success fs-6 mb-1">₹ ${item.price || item.cost || 0}</div>
                                    <button type="button" class="btn btn-sm btn-success px-3" onclick='selectInventoryItem(${JSON.stringify(item).replace(/'/g, "&#39;")})'>Select</button>
                                </div>
                            </div>
                        `;
                    }

                    listGroup.appendChild(card);
                });

                container.appendChild(listGroup);
            })
            .catch(err => {
                container.innerHTML = '<div class="alert alert-danger">Error retrieving inventory records from server.</div>';
                console.error(err);
            });
    }

    function selectInventoryItem(item, subItem = null) {
        const itemType = currentItemType;
        let itemData = {
            item_type: itemType === 'spots' ? 'spot' : (itemType === 'hotels' ? 'hotel' : (itemType === 'transports' ? 'transport' : (itemType === 'tickets' ? 'ticket' : (itemType === 'meals' ? 'meal' : 'activity')))),
            item_id: item.id || null,
            supplier_id: item.supplier_id || (item.supplier ? item.supplier.id : null),
            title: item.name || item.attraction_name || 'Item',
            description: item.description || '',
            price: 0,
            cost: 0,
            room_type: ''
        };

        if (itemType === 'hotels') {
            itemData.room_type = subItem ? (subItem.room_type || '') : (item.room_type || 'Standard Room');
            itemData.price = subItem ? (subItem.price || subItem.base_price || 0) : (item.price || 0);
            itemData.cost = itemData.price;
            itemData.description = `${itemData.room_type} | ${item.star_rating || 3} Star`;
        } else if (itemType === 'transports') {
            itemData.price = item.price || item.base_price || 0;
            itemData.cost = itemData.price;
            itemData.description = `${item.vehicle_type || 'Vehicle'} (Capacity: ${item.capacity || 'N/A'})`;
        } else if (itemType === 'tickets') {
            itemData.price = item.adult_price || item.price || 0;
            itemData.cost = itemData.price;
            itemData.description = `Adult Price: ₹ ${itemData.price}`;
        } else if (itemType === 'spots') {
            itemData.price = 0;
            itemData.cost = 0;
            itemData.description = item.description || '';
        } else if (itemType === 'activities') {
            itemData.price = item.base_price || item.adult_price || 0;
            itemData.cost = itemData.price;
            itemData.description = item.duration || 'Guided Activity';
        } else if (itemType === 'meals') {
            itemData.price = item.price || item.cost || 0;
            itemData.cost = itemData.price;
            itemData.description = item.meal_type || item.type || 'Meal Plan';
        }

        renderItemRow(currentDayIndex, itemData);

        if (inventoryModalInstance) {
            inventoryModalInstance.hide();
        } else if (typeof $ !== 'undefined' && typeof $('#inventoryModal').modal === 'function') {
            $('#inventoryModal').modal('hide');
        }
    }

    function toggleCustomInventoryForm() {
        const box = document.getElementById('customInventoryBox');
        if (box) {
            box.classList.toggle('d-none');
            if (!box.classList.contains('d-none')) {
                document.getElementById('custom_inv_title')?.focus();
            }
        }
    }

    function addCustomInventoryItem() {
        const title = document.getElementById('custom_inv_title')?.value.trim();
        const supplierName = document.getElementById('custom_inv_supplier')?.value.trim();
        const price = parseFloat(document.getElementById('custom_inv_price')?.value || 0);

        if (!title) {
            alert('Please enter an item title / attraction name.');
            return;
        }

        const itemType = currentItemType;
        const mappedType = itemType === 'spots' ? 'spot' : (itemType === 'hotels' ? 'hotel' : (itemType === 'transports' ? 'transport' : (itemType === 'tickets' ? 'ticket' : (itemType === 'meals' ? 'meal' : 'activity'))));

        const itemData = {
            item_type: mappedType,
            item_id: null,
            supplier_id: null,
            title: title,
            description: supplierName ? `Supplier: ${supplierName}` : 'Custom Entry',
            price: price,
            cost: price,
            room_type: ''
        };

        renderItemRow(currentDayIndex, itemData);

        // Reset inputs
        const tEl = document.getElementById('custom_inv_title'); if (tEl) tEl.value = '';
        const sEl = document.getElementById('custom_inv_supplier'); if (sEl) sEl.value = '';
        const pEl = document.getElementById('custom_inv_price'); if (pEl) pEl.value = '';
        toggleCustomInventoryForm();

        if (inventoryModalInstance) {
            inventoryModalInstance.hide();
        } else if (typeof $ !== 'undefined' && typeof $('#inventoryModal').modal === 'function') {
            $('#inventoryModal').modal('hide');
        }
    }

    function renderItemRow(dayIndex, itemData) {
        const container = document.getElementById(`day-items-${dayIndex}`);
        if (!container) return;

        const itemIndex = container.querySelectorAll('.item-row').length;

        const badgeMap = {
            'hotel': { class: 'bg-primary', icon: 'bi-building', label: 'HOTEL' },
            'transport': { class: 'bg-secondary', icon: 'bi-car-front', label: 'TRANSPORT' },
            'ticket': { class: 'bg-danger', icon: 'bi-ticket-perforated', label: 'TICKET' },
            'spot': { class: 'bg-info text-white', icon: 'bi-geo-alt', label: 'SPOT' },
            'activity': { class: 'bg-warning text-dark', icon: 'bi-lightning', label: 'ACTIVITY' },
            'meal': { class: 'bg-success', icon: 'bi-cup-hot', label: 'MEAL' }
        };

        const b = badgeMap[itemData.item_type] || { class: 'bg-dark', icon: 'bi-box', label: itemData.item_type.toUpperCase() };

        const row = document.createElement('div');
        row.className = 'item-row card mb-2 p-2 border bg-white shadow-xs';
        row.innerHTML = `
            <input type="hidden" name="days[${dayIndex}][items][${itemIndex}][item_type]" value="${itemData.item_type}">
            <input type="hidden" name="days[${dayIndex}][items][${itemIndex}][item_id]" value="${itemData.item_id || ''}">
            <input type="hidden" name="days[${dayIndex}][items][${itemIndex}][supplier_id]" value="${itemData.supplier_id || ''}">
            <input type="hidden" name="days[${dayIndex}][items][${itemIndex}][sort_order]" value="${itemIndex}">
            ${itemData.room_type ? `<input type="hidden" name="days[${dayIndex}][items][${itemIndex}][room_type]" value="${itemData.room_type}">` : ''}

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="badge ${b.class}"><i class="bi ${b.icon} me-1"></i>${b.label}</span>
                    <input type="text" name="days[${dayIndex}][items][${itemIndex}][title]" value="${itemData.title}" class="form-control form-control-sm fw-bold border-0 bg-light" style="width: auto; min-width: 220px;" placeholder="Item Title">
                    ${itemData.room_type ? `<span class="badge bg-light text-dark border"><i class="bi bi-door-closed me-1"></i>${itemData.room_type}</span>` : ''}
                    <input type="text" name="days[${dayIndex}][items][${itemIndex}][description]" value="${itemData.description || ''}" class="form-control form-control-sm border-0 text-muted" style="width: auto; min-width: 180px;" placeholder="Notes / variant">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm" style="width: 140px;">
                        <span class="input-group-text bg-light currency-rate-label">Rate ${getActiveCurrencyInfo().symbol}</span>
                        <input type="number" step="0.01" name="days[${dayIndex}][items][${itemIndex}][price]" value="${itemData.price || 0}" class="form-control text-end item-rate-input">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="this.closest('.item-row').remove();" title="Remove Item">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;

        container.appendChild(row);
    }
</script>
