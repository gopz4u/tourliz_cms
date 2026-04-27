     // Initial D        ata     let itinerary =  [] ;     const currency = " "BLADE_VAR" ";     const placeId = " "BLADE_VAR" ";
            // Helper to ensure we always have an array     function ensureArray(arr) {         return Array.isArray(arr) ? arr : [];     }
            // --- Core Rendering ---
            function renderBuilder() {
                const container = document.getElementById('itinerary-builder');
                if (!container) return;
                container.innerHTML = '';

                if (!Array.isArray(itinerary) || itinerary.length === 0) {
                    container.innerHTML = '<div class="alert alert-info border-0 shadow-sm text-center py-4">No days added. Click "Add Day" to start building your proposal.</div>';
                    return;
                }

                itinerary.forEach((day, index) => {
                    const card = createDayCard(day, index);
                    container.appendChild(card);

                    // Render lists with strict array enforcement
                    renderHotels(index, day);
                    renderListItems(index, 'activities', ensureArray(day.activities));
                    renderListItems(index, 'spots', ensureArray(day.spots));
                    renderListItems(index, 'tickets', ensureArray(day.places));
                    renderListItems(index, 'transports', ensureArray(day.transport));
                    renderListItems(index, 'meals', ensureArray(day.meals));
                });
                calculateDynamicTotal();
            }
            function safeFloat(val) {
                if (val === null || val === undefined || String(val).trim() === '') return 0;
                const n = parseFloat(val);
                return isNaN(n) ? 0 : n;
            }

            function calculateDynamicTotal() {
                let hotels = 0, transport = 0, activities = 0, meals = 0;
                itinerary.forEach(day => {
                    // 1. Hotels
                    ensureArray(day.hotels).forEach(h => {
                        const price = safeFloat(h.price_per_night);
                        const addon = safeFloat(h.add_on_price);
                        let qty = 1;
                        if (h.quantity !== undefined && h.quantity !== null && String(h.quantity).trim() !== '') {
                            qty = safeFloat(h.quantity);
                        }
                        hotels += (price + addon) * qty;
                    });

                    // 2. Transport
                    const transports = day.transport || day.transports;
                    ensureArray(transports).forEach(t => {
                        transport += safeFloat(t.price);
                    });

                    // 3. Activities
                    ensureArray(day.activities).forEach(a => {
                        let itemCost = 0;
                        if (a.entry_ticket) {
                            const et = a.entry_ticket;
                            const adultRate = safeFloat(et.adult_price || et.price);
                            const aq = safeFloat(et.adult_qty);
                            const childSmallRate = safeFloat(et.child_2_6_price);
                            const csq = safeFloat(et.child_2_6_qty);
                            const childLargeRate = safeFloat(et.child_6_11_price);
                            const clq = safeFloat(et.child_6_11_qty);

                            itemCost += (adultRate * aq) + (childSmallRate * csq) + (childLargeRate * clq);
                            if ((aq + csq + clq) === 0) {
                                itemCost += adultRate;
                            }
                        }
                        if (a.hours || a.price_per_hour) {
                            itemCost += (safeFloat(a.hours) * safeFloat(a.price_per_hour));
                        }
                        activities += itemCost;
                    });

                    // Tickets (Places)
                    ensureArray(day.places).forEach(p => {
                        let itemCost = 0;
                        if (p.entry_ticket) {
                            const et = p.entry_ticket;
                            const adultRate = safeFloat(et.adult_price || et.price);
                            const aq = safeFloat(et.adult_qty);
                            const childSmallRate = safeFloat(et.child_2_6_price);
                            const csq = safeFloat(et.child_2_6_qty);
                            const childLargeRate = safeFloat(et.child_6_11_price);
                            const clq = safeFloat(et.child_6_11_qty);

                            itemCost += (adultRate * aq) + (childSmallRate * csq) + (childLargeRate * clq);
                            if ((aq + csq + clq) === 0) {
                                itemCost += adultRate;
                            }
                        }
                        activities += itemCost;
                    });

                    // Spots
                    ensureArray(day.spots).forEach(s => {
                        activities += (safeFloat(s.hours) * safeFloat(s.price_per_hour));
                    });

                    // 4. Meals
                    ensureArray(day.meals).forEach(m => {
                        const price = safeFloat(m.price);
                        let qty = 1;
                        if (m.quantity !== undefined && m.quantity !== null && String(m.quantity).trim() !== '') {
                            qty = safeFloat(m.quantity);
                        }
                        meals += price * qty;
                    });
                });

                const baseCost = hotels + transport + activities + meals;
                const markupPerc = safeFloat(document.getElementById('markup-percentage')?.value);
                const markupAmount = (baseCost * markupPerc) / 100;
                const grandTotal = baseCost + markupAmount;

                const safeSet = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.innerText = currency + ' ' + val.toFixed(2);
                };

                safeSet('preview-hotels', hotels);
                safeSet('preview-transport', transport);
                safeSet('preview-activities', activities);
                safeSet('preview-meals', meals);
                safeSet('preview-base-total', baseCost);
                safeSet('preview-markup-perc', markupPerc);
                safeSet('preview-markup', markupAmount);
                safeSet('preview-grand-total', grandTotal);

                const adults = safeFloat(document.getElementById('pax-adults')?.value);
                const c1 = safeFloat(document.getElementById('pax-child-small')?.value);
                const c2 = safeFloat(document.getElementById('pax-child-large')?.value);
                const totalPax = adults + c1 + c2;
                const perPax = totalPax > 0 ? (grandTotal / totalPax) : 0;
                safeSet('preview-perpax-total', perPax);

                const summaryQuotedEl = document.getElementById('summary-quoted-total');
                if (summaryQuotedEl) summaryQuotedEl.innerText = currency + ' ' + grandTotal.toFixed(2);

                const actualCostStr = document.getElementById('summary-actual-cost')?.innerText || '0.00';
                const cleanActual = actualCostStr.replace(/[^0-9.-]+/g, '');
                const actualCost = parseFloat(cleanActual) || 0;

                const projectedCost = Math.max(baseCost, actualCost);
                const profit = grandTotal - projectedCost;
                const margin = grandTotal > 0 ? (profit / grandTotal) * 100 : 0;

                $('#actual-profit').text(currency + ' ' + profit.toFixed(2));
                $('#actual-margin').text(margin.toFixed(2) + '%');

                const profitEl = $('#actual-profit');
                const marginEl = $('#actual-margin');
                if (profit >= 0) {
                    profitEl.removeClass('text-danger').addClass('text-success');
                    marginEl.removeClass('text-danger').addClass('text-success');
                } else {
                    profitEl.removeClass('text-success').addClass('text-danger');
                    marginEl.removeClass('text-success').addClass('text-danger');
                }
            }
            function createDayCard(day, index) {
                const div = document.createElement('div'); div.className = 'card mb-4 border-0 shadow-sm overflow-hidden'; div.innerHTML = `
                                                                                                                                                                                                                                                                                                                                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                                                                                                                                                                                                                                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                                                                                                                                                                                                                                    <span class="badge bg-primary me-2 px-3 py-2">Day ${day.day}</span>
                                                                                                                                                                                                                                                                                                                                                    <input type="text" class="form-control form-control-sm fw-bold border-0 bg-transparent fs-5" 
                                                                                                                                                                                                                                                                                                                                                           style="width: 350px;" 
                                                                                                                                                                                                                                                                                                                                                           value="${day.title || ''}"
                                                                                                                                                                                                                                                                                                                                                    onchange="window.updateField(${index}, 'title', this.value)"
                                                                                                                                                                                                                                                                                                                                                           placeholder="Enter Day Title...">
                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                <div class="d-flex gap-2">
                                                                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-sm btn-success opacity-75" onclick="window.shareDayToDriver(${index})" title="Share Job Sheet to Driver">
                                                                                                                                                                                                                                                                                                                                                        <i class="bi bi-whatsapp"></i> Job Sheet
                                                                                                                                                                                                                                                                                                                                                    </button>
                                                                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="window.removeDay(${index})">
                                                                                                                                                                                                                                                                                                                                                        <i class="bi bi-trash"></i>
                                                                                                                                                                                                                                                                                                                                                    </button>
                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                            <div class="card-body p-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="row g-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <!-- Left: Main Items -->
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="col-md-7">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <!-- Activities -->
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="mb-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <label class="text-uppercase small fw-bold text-muted letter-spacing-1">Sightseeing & Activities</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="window.openInventoryModal(${index}, 'activities')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="bi bi-search me-1"></i> Master
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div id="activities-container-${index}" class="list-group list-group-flush mb-2"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button type="button" class="btn btn-sm text-primary p-0" onclick="window.addItem(${index}, 'activities')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="bi bi-plus-circle me-1"></i> Add Manual Activity
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <!-- Tourist Spots -->
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="mb-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <label class="text-uppercase small fw-bold text-muted letter-spacing-1">Tourist Spots</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="window.openInventoryModal(${index}, 'spots')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="bi bi-search me-1"></i> Master
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div id="spots-container-${index}" class="list-group list-group-flush mb-2"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button type="button" class="btn btn-sm text-primary p-0" onclick="window.addItem(${index}, 'spots')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="bi bi-plus-circle me-1"></i> Add Manual Spot
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <!-- Entry Tickets -->
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="mb-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <label class="text-uppercase small fw-bold text-muted letter-spacing-1">Entry Tickets</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="window.openInventoryModal(${index}, 'tickets')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="bi bi-search me-1"></i> Master
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div id="tickets-container-${index}" class="list-group list-group-flush mb-2"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button type="button" class="btn btn-sm text-primary p-0" onclick="window.addItem(${index}, 'tickets')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="bi bi-plus-circle me-1"></i> Add Manual Ticket
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <!-- Meals -->
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="mb-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <label class="text-uppercase small fw-bold text-muted letter-spacing-1">Meals</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="window.openInventoryModal(${index}, 'meals')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="bi bi-search me-1"></i> Master
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div id="meals-container-${index}" class="list-group list-group-flush mb-2"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button type="button" class="btn btn-sm text-primary p-0" onclick="window.addItem(${index}, 'meals')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="bi bi-plus-circle me-1"></i> Add Manual Meal
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <!-- Right: Logistics -->
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="col-md-5">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <!-- Hotels -->
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="mb-4 bg-light p-3 rounded-3 border">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <label class="text-uppercase small fw-bold text-muted mb-0">Hotels & Rooms</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <button type="button" class="btn btn-xs btn-primary select-hotel-btn" onclick="window.openInventoryModal(${index}, 'hotels')">Select Master</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div id="hotels-container-${index}" class="mb-2"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button type="button" class="btn btn-sm text-primary p-0 w-100 mt-2 border-top pt-2" onclick="window.addItem(${index}, 'hotels')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="bi bi-plus-circle me-1"></i> Add Manual Hotel/Room
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <!-- Transport -->
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="mb-3">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <label class="text-uppercase small fw-bold text-muted mb-0">Transport</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="window.openInventoryModal(${index}, 'transports')">Master</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div id="transports-container-${index}"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="mt-3">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <label class="small fw-bold text-muted text-uppercase letter-spacing-1 mb-1">Day Notes</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <textarea class="form-control form-control-sm bg-light" placeholder="Describe the day's flow..." rows="2"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    onchange="window.updateField(${index}, 'notes', this.value)"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                >${day.notes || ''}</textarea>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       `;
                return div;
            }


            function renderHotels(dayIndex, day) {
                const container = document.getElementById(`hotels-container-${dayIndex}`);
                if (!container) return;
                container.innerHTML = '';

                let hotels = ensureArray(day.hotels);

                // Backward compatibility
                if (day.hotel && day.hotel.name && hotels.length === 0) {
                    hotels = [day.hotel];
                    day.hotels = hotels;
                    delete day.hotel;
                }

                hotels.forEach((hotel, hIndex) => {
                    const row = document.createElement('div');
                    row.className = 'mb-2 p-2 bg-white rounded border';
                    row.innerHTML = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="d-flex justify-content-between gap-1 mb-1">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="text" class="form-control form-control-sm fw-bold border-0 p-0" placeholder="Hotel Name"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            value="${hotel.name || ''}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            onchange="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'name', this.value)">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button type="button" class="btn btn-xs text-success opacity-75 ms-1" onclick="window.shareHotelRequest(${dayIndex}, ${hIndex})" title="Share Booking">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="bi bi-whatsapp"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <button type="button" cl                ass="btn btn-xs text-danger opacity-50" onclick="window.removeItem(${dayIndex}, 'hotels', ${hIndex})">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="bi bi-x"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="row g-2 mt-1">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <input type="text" class="form-control form-control-sm border-0 p-0 text-muted" style="font-size: 0.75rem; height: auto" placeholder="Room Type (e.g. Deluxe Room)"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    value="${hotel.type || ''}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    onchange="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'type', this.value)">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="row g-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="input-group input-group-sm border rounded overflow-hidden">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span class="input-group-text bg-light border-0 py-0 px-1 text-muted" style="font-size: 0.6rem">Rooms</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <input type="number" class="form-control border-0 p-1" style="font-size: 0.75rem"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                value="${hotel.quantity || 1}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                onchange="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'quantity', this.value)">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="input-group input-group-sm border rounded overflow-hidden">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span class="input-group-text bg-light border-0 py-0 px-1 text-muted" style="font-size: 0.6rem">${currency}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <input type="number" class="form-control border-0 p-1" style="font-size: 0.75rem"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                value="${hotel.price_per_night || 0}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                onchange="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'price_per_night', this.value)">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="input-group input-group-sm border rounded overflow-hidden">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span class="input-group-text bg-light border-0 py-0 px-1 text-muted" style="font-size: 0.6rem">+Addon</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <input type="number" class="form-control border-0 p-1" style="font-size: 0.75rem"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                value="${hotel.add_on_price || 0}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                onchange="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'add_on_price', this.value)">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                `;
                    container.appendChild(row);
                });
            }
            function renderListItems(dayIndex, type, items) {
                const container = document.getElementById(`${type}-container-${dayIndex}`);
                if (!container) return;
                container.innerHTML = '';

                const safeItems = Array.isArray(items) ? items : [];

                safeItems.forEach((item, itemIndex) => {
                    const row = document.createElement('div');
                    row.className = 'mb-3 p-2 bg-light rounded border';

                    const nameField = type === 'tickets' ? 'attraction_name' : 'name';

                    let html = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="d-flex gap-2 mb-1 align-items-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <input type="text" class="form-control form-control-sm fw-bold border-0 bg-transparent" style="flex:1" 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    placeholder="Name" 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    value="${item[nameField] || ''}" 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    onchange="window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, '${nameField}', this.value)">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <button type="button" class="btn btn-sm text-muted opacity-50 px-1" onclick="window.removeItem(${dayIndex}, '${type}', ${itemIndex})">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <i class="bi bi-x-circle"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <textarea class="form-control form-control-sm border-0 bg-white shadow-none" 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                rows="1" placeholder="Add description..."
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                onchange="window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, 'description', this.value)">${item.description || ''}</textarea>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    `;

                    if (type !== 'spots' && type !== 'activities' && type !== 'tickets') {
                        const priceValue = (type === 'transports' || type === 'meals') ? (item.price || 0) : (item.entry_ticket?.price || 0);
                        const qtyValue = (type === 'meals') ? (item.quantity || 1) : 1;

                        const onPriceChange = (type === 'transports' || type === 'meals')
                            ? `window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, 'price', this.value)`
                            : `window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'price', this.value)`;

                        html += `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="d-flex gap-2 mt-1 px-1">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="input-group input-group-sm" style="width: 120px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="input-group-text bg-transparent border-0 text-muted small px-1">${currency}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <input type="number" class="form-control border-0 bg-white" placeholder="Price" style="font-size: 0.75rem;" value="${priceValue}" onchange="${onPriceChange}">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${type === 'meals' ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="input-group input-group-sm" style="width: 80px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="input-group-text bg-transparent border-0 text-muted small px-1">Qty</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <input type="number" class="form-control border-0 bg-white" style="font-size: 0.75rem;" value="${qtyValue}" onchange="window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, 'quantity', this.value)">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        `;
                    } else if (type === 'spots') {
                        html += `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="row g-2 mt-1 px-1">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-6">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="input-group input-group-sm">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span class="input-group-text py-0 bg-transparent small px-1 border-0 text-muted">Hrs</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <input type="number" class="form-control border p-1" style="font-size: 0.7rem" value="${item.hours || 0}" onchange="window.updateListItem(${dayIndex}, 'spots', ${itemIndex}, 'hours', this.value)">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-6">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="input-group input-group-sm">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span class="input-group-text py-0 bg-transparent small px-1 border-0 text-muted text-nowrap">${currency}/hr</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <input type="number" class="form-control border p-1" style="font-size: 0.7rem" value="${item.price_per_hour || 0}" onchange="window.updateListItem(${dayIndex}, 'spots', ${itemIndex}, 'price_per_hour', this.value)">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        `;
                    } else if (type === 'activities' || type === 'tickets') {
                        const et = item.entry_ticket || {};
                        html += `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="mt-2 border-top pt-2" style="font-size: 0.7rem">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="row g-1 mb-1 fw-bold text-muted text-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-4 text-start">Pax Type</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-3">Qty</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-5">Price (${currency})</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="row g-1 mb-1 align-items-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-4">Adult</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-3"><input type="number" class="form-control form-control-sm p-1" style="font-size: 0.7rem" value="${et.adult_qty || 0}" onchange="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'adult_qty', this.value)"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-5"><input type="number" class="form-control form-control-sm p-1" style="font-size: 0.7rem" value="${et.adult_price || et.price || 0}" onchange="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'adult_price', this.value)"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="row g-1 mb-1 align-items-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-4">Child 2-6</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-3"><input type="number" class="form-control form-control-sm p-1" style="font-size: 0.7rem" value="${et.child_2_6_qty || 0}" onchange="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'child_2_6_qty', this.value)"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-5"><input type="number" class="form-control form-control-sm p-1" style="font-size: 0.7rem" value="${et.child_2_6_price || 0}" onchange="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'child_2_6_price', this.value)"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="row g-1 mb-1 align-items-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-4">Child 6-11</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-3"><input type="number" class="form-control form-control-sm p-1" style="font-size: 0.7rem" value="${et.child_6_11_qty || 0}" onchange="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'child_6_11_qty', this.value)"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-5"><input type="number" class="form-control form-control-sm p-1" style="font-size: 0.7rem" value="${et.child_6_11_price || 0}" onchange="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'child_6_11_price', this.value)"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${type === 'activities' ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="row g-1 mt-1 border-top pt-1">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-6 text-muted">Extra Hourly Cost</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-3"><input type="number" class="form-control form-control-sm p-1" style="font-size: 0.7rem" placeholder="Hrs" value="${item.hours || ''}" onchange="window.updateListItem(${dayIndex}, 'activities', ${itemIndex}, 'hours', this.value)"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-3"><input type="number" class="form-control form-control-sm p-1" style="font-size: 0.7rem" placeholder="Rate" value="${item.price_per_hour || ''}" onchange="window.updateListItem(${dayIndex}, 'activities', ${itemIndex}, 'price_per_hour', this.value)"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        `;
                    }

                    row.innerHTML = html;
                    container.appendChild(row);
                });
            }

            // --- Inventory Master Fetching ---
            let currentDayIndex = 0;
            let currentType = '';
            const inventoryModalElement = document.getElementById('inventoryModal');
            let inventoryModal = null;
            if (inventoryModalElement) {
                inventoryModal = new bootstrap.Modal(inventoryModalElement);
            }

            // Add listener for place change in modal
            const pSelectElem = document.getElementById('inventoryPlaceId');
            if (pSelectElem) pSelectElem.addEventListener('change', fetchItems);

            window.openInventoryModal = function (dayIndex, type) {
                currentDayIndex = dayIndex;
                currentType = type;
                const titleElem = document.getElementById('inventoryModalTitle');
                if (titleElem) titleElem.innerText = 'Select ' + type.charAt(0).toUpperCase() + type.slice(1) + ' from Master';

                const pSelect = document.getElementById('inventoryPlaceId');
                if (pSelect) pSelect.value = placeId;

                fetchItems();
                if (inventoryModal) inventoryModal.show();
            };

            function fetchItems() {
                const pSelect = document.getElementById('inventoryPlaceId');
                const selectedPlaceId = pSelect ? pSelect.value : placeId;
                const url = `/api/inventory/${currentType}?place_id=${selectedPlaceId}`;
                const container = document.getElementById('inventoryResults');
                if (!container) return;
                container.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div><p class="mt-2 text-muted">Accessing master repository...</p></div>';

                fetch(url).then(res => res.json()).then(data => {
                    container.innerHTML = '';
                    if (!data || data.length === 0) {
                        container.innerHTML = '<div class="text-center p-4"><i class="bi bi-info-circle fs-2 text-muted"></i><p class="mt-2 text-muted">No tagged items found for this destination.</p></div>';
                        return;
                    }

                    if (currentType === 'transports') {
                        container.innerHTML = `
                                                                                    <div class="p-3">
                                                                                        <div class="mb-3">
                                                                                            <label class="form-label small text-muted">1. Select Vendor</label>
                                                                                            <select id="wiz-vendor" class="form-select form-select-sm"><option value="">Select Vendor</option></select>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <label class="form-label small text-muted">2. Select Service / Duration</label>
                                                                                            <select id="wiz-service" class="form-select form-select-sm" disabled><option value="">Select Service / Duration</option></select>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <label class="form-label small text-muted">3. Select Vehicle</label>
                                                                                            <select id="wiz-vehicle" class="form-select form-select-sm" disabled><option value="">Select Vehicle</option></select>
                                                                                        </div>
                                                                                        <div id="wiz-result" class="text-center mt-3" style="display:none;">
                                                                                            <div class="h4 text-primary fw-bold mb-2" id="wiz-price"></div>
                                                                                            <button class="btn btn-primary w-100" id="wiz-add-btn">Add to Itinerary</button>
                                                                                        </div>
                                                                                    </div>
                                                                                `;

                        const allTransports = data;
                        const vendors = [...new Set(data.map(d => d.supplier ? d.supplier.name : (d.supplier_id ? 'Vendor #' + d.supplier_id : 'General')))].sort();
                        const vSelect = document.getElementById('wiz-vendor');
                        const sSelect = document.getElementById('wiz-service');
                        const vehSelect = document.getElementById('wiz-vehicle');
                        const resDiv = document.getElementById('wiz-result');

                        vendors.forEach(v => vSelect.add(new Option(v, v)));

                        vSelect.addEventListener('change', function () {
                            sSelect.innerHTML = '<option value="">Select Service / Duration</option>';
                            vehSelect.innerHTML = '<option value="">Select Vehicle</option>';
                            sSelect.disabled = true;
                            vehSelect.disabled = true;
                            resDiv.style.display = 'none';

                            if (this.value) {
                                const filtered = allTransports.filter(d => (d.supplier ? d.supplier.name : (d.supplier_id ? 'Vendor #' + d.supplier_id : 'General')) === this.value);
                                const services = [...new Set(filtered.map(d => d.duration || d.name))].sort();
                                services.forEach(s => sSelect.add(new Option(s, s)));
                                sSelect.disabled = false;
                            }
                        });

                        sSelect.addEventListener('change', function () {
                            vehSelect.innerHTML = '<option value="">Select Vehicle</option>';
                            vehSelect.disabled = true;
                            resDiv.style.display = 'none';

                            if (this.value) {
                                const vendor = vSelect.value;
                                const filtered = allTransports.filter(d =>
                                    (d.supplier ? d.supplier.name : (d.supplier_id ? 'Vendor #' + d.supplier_id : 'General')) === vendor &&
                                    (d.duration || d.name) === this.value
                                );
                                const vehicles = [...new Set(filtered.map(d => d.vehicle_type))].sort();
                                vehicles.forEach(v => vehSelect.add(new Option(v, v)));
                                vehSelect.disabled = false;
                            }
                        });

                        vehSelect.addEventListener('change', function () {
                            if (this.value) {
                                const vendor = vSelect.value;
                                const service = sSelect.value;
                                const item = allTransports.find(d =>
                                    (d.supplier ? d.supplier.name : (d.supplier_id ? 'Vendor #' + d.supplier_id : 'General')) === vendor &&
                                    (d.duration || d.name) === service &&
                                    d.vehicle_type === this.value
                                );

                                if (item) {
                                    document.getElementById('wiz-price').innerText = ` "BLADE_VAR"  ${item.base_price}`;
                                    document.getElementById('wiz-add-btn').onclick = () => selectItem(item);
                                    resDiv.style.display = 'block';
                                }
                            } else {
                                resDiv.style.display = 'none';
                            }
                        });
                        return;
                    }

                    data.forEach(item => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'list-group-item list-group-item-action p-3 mb-2 border rounded shadow-sm hover-shadow transition';

                        if (currentType === 'hotels') {
                            btn.innerHTML = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <h6 class="mb-0 fw-bold text-dark">${item.name}</h6>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <span class="badge bg-warning text-dark">${item.star_rating} <i class="bi bi-star-fill small"></i></span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="room-options d-grid gap-1 mt-2"></div>`;
                            const roomContainer = btn.querySelector('.room-options');
                            if (item.rooms) {
                                item.rooms.forEach(room => {
                                    const rb = document.createElement('div');
                                    rb.className = 'btn btn-sm btn-light border text-start d-flex justify-content-between align-items-center';
                                    rb.innerHTML = `<span>${room.room_type}</span> <span class="fw-bold text-primary">${currency} ${room.base_price}</span>`;
                                    rb.onclick = (e) => { e.stopPropagation(); selectItem(item, room); };
                                    roomContainer.appendChild(rb);
                                });
                            }
                        } else if (currentType === 'activities') {
                            btn.innerHTML = `<div class="d-flex justify-content-between"><strong>${item.name}</strong> <span class="text-primary fw-bold">${currency} ${item.base_price}</span></div><small class="text-muted d-block">${item.duration || ''}</small><small class="text-muted">${item.description || ''}</small>`;
                            btn.onclick = () => selectItem(item);
                        } else if (currentType === 'tickets') {
                            btn.innerHTML = `<div class="d-flex justify-content-between"><strong>${item.attraction_name}</strong> <span class="text-primary fw-bold">${currency} ${item.adult_price}</span></div><small class="text-muted">Adult Entry Ticket</small>`;
                            btn.onclick = () => selectItem(item);
                        } else if (currentType === 'spots') {
                            btn.innerHTML = `<div class="d-flex justify-content-between"><strong>${item.name}</strong></div><small class="text-muted">${item.description || ''}</small>`;
                            btn.onclick = () => selectItem(item);
                        } else if (currentType === 'transports') {
                            btn.innerHTML = `<div class="d-flex justify-content-between"><strong>${item.name}</strong> <span class="text-primary fw-bold">${currency} ${item.base_price}</span></div><small class="text-muted">${item.vehicle_type} (Capacity: ${item.capacity})</small>`;
                            btn.onclick = () => selectItem(item);
                        } else if (currentType === 'meals') {
                            btn.innerHTML = `<div class="d-flex justify-content-between"><strong>${item.name}</strong> <span class="text-primary fw-bold">${currency} ${item.price}</span></div><small class="text-info text-uppercase fw-bold">${item.type}</small>`;
                            btn.onclick = () => selectItem(item);
                        }
                        container.appendChild(btn);
                    });
                }).catch(err => {
                    container.innerHTML = '<div class="alert alert-danger mx-3">Failed to load inventory data.</div>';
                    console.error(err);
                });
            }

            function selectItem(item, subItem = null) {
                if (!itinerary[currentDayIndex]) return;

                if (currentType === 'hotels') {
                    if (!itinerary[currentDayIndex].hotels) itinerary[currentDayIndex].hotels = [];
                    itinerary[currentDayIndex].hotels.push({
                        name: item.name,
                        type: subItem.room_type,
                        price_per_night: subItem.base_price,
                        currency: currency,
                        quantity: 1,
                        add_on_price: 0
                    });
                } else if (currentType === 'activities') {
                    if (!Array.isArray(itinerary[currentDayIndex].activities)) itinerary[currentDayIndex].activities = [];
                    const adults = parseInt(document.getElementById('pax-adults')?.value || 0);
                    const c1 = parseInt(document.getElementById('pax-child-small')?.value || 0);
                    const c2 = parseInt(document.getElementById('pax-child-large')?.value || 0);
                    itinerary[currentDayIndex].activities.push({
                        name: item.name,
                        description: item.description || '',
                        entry_ticket: {
                            adult_price: item.base_price,
                            adult_qty: adults,
                            child_2_6_price: item.child_price || (item.base_price * 0.5),
                            child_2_6_qty: c1,
                            child_6_11_price: item.child_price || (item.base_price * 0.75),
                            child_6_11_qty: c2,
                            currency: currency
                        }
                    });
                } else if (currentType === 'tickets') {
                    if (!Array.isArray(itinerary[currentDayIndex].places)) itinerary[currentDayIndex].places = [];
                    const adults = parseInt(document.getElementById('pax-adults')?.value || 0);
                    const c1 = parseInt(document.getElementById('pax-child-small')?.value || 0);
                    const c2 = parseInt(document.getElementById('pax-child-large')?.value || 0);
                    itinerary[currentDayIndex].places.push({
                        attraction_name: item.attraction_name,
                        description: 'Entry Ticket',
                        entry_ticket: {
                            adult_price: item.adult_price,
                            adult_qty: adults,
                            child_2_6_price: item.child_price || (item.adult_price * 0.5),
                            child_2_6_qty: c1,
                            child_6_11_price: item.child_price || (item.adult_price * 0.75),
                            child_6_11_qty: c2,
                            currency: currency
                        }
                    });
                } else if (currentType === 'spots') {
                    if (!Array.isArray(itinerary[currentDayIndex].spots)) itinerary[currentDayIndex].spots = [];
                    itinerary[currentDayIndex].spots.push({ name: item.name, description: item.description || '', hours: 2, price_per_hour: 0 });
                } else if (currentType === 'transports') {
                    if (!Array.isArray(itinerary[currentDayIndex].transport)) itinerary[currentDayIndex].transport = [];
                    itinerary[currentDayIndex].transport.push({ name: item.name, type: item.vehicle_type, price: item.base_price, currency: currency });
                } else if (currentType === 'meals') {
                    if (!Array.isArray(itinerary[currentDayIndex].meals)) itinerary[currentDayIndex].meals = [];
                    const adults = parseInt(document.getElementById('pax-adults')?.value || 1);
                    itinerary[currentDayIndex].meals.push({ name: '[' + item.type + '] ' + item.name, price: item.price, quantity: adults, currency: currency });
                }
                if (inventoryModal) inventoryModal.hide();
                renderBuilder();
            }

            // --- Data Helpers ---
            window.updateField = (index, field, value) => { itinerary[index][field] = value; calculateDynamicTotal(); };
            window.updateNestedField = (index, parent, field, value) => {
                if (!itinerary[index][parent]) itinerary[index][parent] = {};
                itinerary[index][parent][field] = value;
                calculateDynamicTotal();
            };
            window.updateListItem = (dayIndex, type, itemIndex, field, value) => {
                const listKey = type === 'tickets' ? 'places' : (type === 'transports' ? 'transport' : (type === 'meals' ? 'meals' : type));
                if (!Array.isArray(itinerary[dayIndex][listKey])) itinerary[dayIndex][listKey] = [];
                if (itinerary[dayIndex][listKey][itemIndex]) {
                    itinerary[dayIndex][listKey][itemIndex][field] = value;
                    calculateDynamicTotal();
                }
            };
            window.updateListItemNested = (dayIndex, type, itemIndex, parent, field, value) => {
                const listKey = type === 'tickets' ? 'places' : (type === 'transports' ? 'transport' : (type === 'meals' ? 'meals' : type));
                if (!Array.isArray(itinerary[dayIndex][listKey])) itinerary[dayIndex][listKey] = [];
                if (itinerary[dayIndex][listKey][itemIndex]) {
                    if (!itinerary[dayIndex][listKey][itemIndex][parent]) itinerary[dayIndex][listKey][itemIndex][parent] = { currency: currency };
                    itinerary[dayIndex][listKey][itemIndex][parent][field] = value;
                    calculateDynamicTotal();
                }
            };

            window.addItem = (dayIndex, type) => {
                const day = itinerary[dayIndex];
                if (!day) return;

                if (type === 'hotels') {
                    const hotels = ensureArray(day.hotels);
                    hotels.push({ name: '', type: '', price_per_night: 0, currency: currency, quantity: 1, add_on_price: 0 });
                    day.hotels = hotels;
                } else {
                    const listKey = type === 'tickets' ? 'places' : (type === 'transports' ? 'transport' : (type === 'meals' ? 'meals' : type));
                    if (!Array.isArray(day[listKey])) day[listKey] = [];
                    if (type === 'activities' || type === 'tickets') {
                        const adults = parseInt(document.getElementById('pax-adults')?.value || 0);
                        day[listKey].push({
                            name: '',
                            description: '',
                            entry_ticket: {
                                adult_price: 0, adult_qty: adults,
                                child_2_6_price: 0, child_2_6_qty: 0,
                                child_6_11_price: 0, child_6_11_qty: 0,
                                currency: currency
                            }
                        });
                    } else if (type === 'spots') {
                        day[listKey].push({ name: '', description: '', hours: 0, price_per_hour: 0 });
                    } else if (type === 'meals') {
                        const adults = parseInt(document.getElementById('pax-adults')?.value || 1);
                        day[listKey].push({ name: '', price: 0, quantity: adults, currency: currency });
                    } else {
                        day[listKey].push({ name: '', price: 0, currency: currency });
                    }
                }
                renderBuilder();
            };

            window.removeItem = (dayIndex, type, itemIndex) => {
                const listKey = type === 'tickets' ? 'places' : (type === 'transports' ? 'transport' : (type === 'meals' ? 'meals' : type));
                if (Array.isArray(itinerary[dayIndex][listKey])) {
                    itinerary[dayIndex][listKey].splice(itemIndex, 1);
                    renderBuilder();
                }
            };

            window.addDay = () => {
                const nextDay = itinerary.length + 1;
                itinerary.push({ day: nextDay, title: `Day ${nextDay} `, places: [], activities: [], spots: [], transport: [], meals: [], hotels: [], notes: '' });
                renderBuilder();
            };

            window.removeDay = (index) => {
                if (confirm('Delete this day and all its items?')) {
                    itinerary.splice(index, 1);
                    itinerary.forEach((d, i) => d.day = i + 1);
                    renderBuilder();
                }
            };

            // --- Sharing Logic ---
            window.copyPdfLink = () => {
                const url = " "BLADE_VAR" ?public=1";
                navigator.clipboard.writeText(url).then(() => {
                    alert('Customer PDF Link copied to clipboard!');
                });
            };

            window.shareCustomerQuote = () => {
                const title = document.getElementById('proposal-title').value;
                const clientName = document.getElementById('client-name').value;
                const adults = document.getElementById('pax-adults').value;
                const c1 = document.getElementById('pax-child-small').value;
                const c2 = document.getElementById('pax-child-large').value;
                const totalPrice = document.getElementById('preview-grand-total').innerText;
                const perPax = document.getElementById('preview-perpax-total').innerText;
                const perPaxVal = parseFloat(perPax.replace(/[^0-9.]/g, '') || 0);
                const currency = " "BLADE_VAR" ";

                const childRateS = (perPaxVal * 0.25).toFixed(2);
                const childRateL = (perPaxVal * 0.50).toFixed(2);

                let text = `*📋 BOOKING PROPOSAL: ${title.toUpperCase()}*\n`;
                text += `Guest: ${clientName}\n`;
                text += `Pax: ${adults} Adults`;
                if (c1 > 0) text += `, ${c1} Child(2-6y)`;
                if (c2 > 0) text += `, ${c2} Child(6-11y)`;
                text += `\n\n`;

                text += `*Full Itinerary Summary:*\n`;
                itinerary.forEach(day => {
                    text += `*Day ${day.day}: ${day.title || ''}*\n`;
                    const spots = [...ensureArray(day.spots), ...ensureArray(day.activities)];
                    if (spots.length > 0) {
                        spots.forEach(s => {
                            if (s.name) text += `📍 ${s.name}\n`;
                        });
                    }
                    text += `\n`;
                });

                text += `*Pricing Details:*\n`;
                text += `Total Final Quote: ${totalPrice}\n`;
                text += `Rate Per Adult: ${perPax}\n`;
                if (c1 > 0) text += `Rate Child (2-6y): ${currency} ${childRateS}\n`;
                if (c2 > 0) text += `Rate Child (6-11y): ${currency} ${childRateL}\n`;

                text += `\nLink:  "BLADE_VAR" ?public=1\n`;

                window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
            };

            // --- Day-Wise Driver Share ---
            window.shareDayToDriver = (dayIndex) => {
                const day = itinerary[dayIndex];
                if (!day) return;

                const clientName = document.getElementById('client-name').value || 'Guest';
                const arrivalDateStr = document.getElementById('arrival-date').value;
                let dateStr = 'TBA';

                if (arrivalDateStr) {
                    const date = new Date(arrivalDateStr);
                    date.setDate(date.getDate() + (day.day - 1));
                    dateStr = date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                }

                // Pickup Point Logic (Previous day's hotel or current day's hotel)
                const pickup = (dayIndex > 0 && ensureArray(itinerary[dayIndex - 1].hotels).length > 0)
                    ? itinerary[dayIndex - 1].hotels[0].name
                    : (ensureArray(day.hotels).length > 0 ? day.hotels[0].name : 'Hotel/Airport');

                let text = `*🚖 DRIVER JOB SHEET - ${dateStr}*\n`;
                text += `Guest: ${clientName}\n`;
                text += `Day: ${day.title || 'Day ' + day.day}\n`;
                text += `*Pickup Point:* ${pickup}\n\n`;

                const transports = ensureArray(day.transport || day.transports);
                if (transports.length > 0) {
                    text += `*Vehicle:* ${transports.map(t => t.type || t.name).join(', ')}\n`;
                }

                text += `\n*Day Program:*\n`;
                const spots = [...ensureArray(day.spots), ...ensureArray(day.activities)];
                if (spots.length > 0) {
                    spots.forEach(s => {
                        if (s.name) text += `📍 ${s.name}\n`;
                    });
                } else {
                    text += "As per guest instructions.\n";
                }

                window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
            };

            // --- Hotel Vendor Share ---
            window.shareHotelRequest = (dayIndex, hotelIndex) => {
                const day = itinerary[dayIndex];
                const h = ensureArray(day.hotels)[hotelIndex];
                if (!h) return;

                const clientName = document.getElementById('client-name').value || 'Guest';
                const adults = document.getElementById('pax-adults').value || 1;
                const c1 = document.getElementById('pax-child-small').value || 0;
                const c2 = document.getElementById('pax-child-large').value || 0;

                const arrivalDateStr = document.getElementById('arrival-date').value;
                let dateStr = 'TBA';
                if (arrivalDateStr) {
                    const date = new Date(arrivalDateStr);
                    date.setDate(date.getDate() + (day.day - 1));
                    dateStr = date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                }

                // Reference ID
                const refId = " "BLADE_VAR" ";

                let text = `*🏨 HOTEL BOOKING REQUEST*\n`;
                text += `Ref: #${refId}\n\n`;
                text += `Hotel: *${h.name}* \n`;
                text += `Guest: ${clientName}\n`;
                text += `Check-In: ${dateStr}\n`;
                text += `Rooms: ${h.quantity || 1} x ${h.type || 'Standard'}\n`;
                text += `Pax: ${adults} Adults`;
                if (c1 > 0 || c2 > 0) text += ` + ${parseInt(c1) + parseInt(c2)} Kids`;
                text += `\n`;

                // Net Price for vendor confirmation
                const cost = (safeFloat(h.price_per_night) + safeFloat(h.add_on_price)) * safeFloat(h.quantity || 1);
                text += `Net Rate: ${h.currency} ${cost.toFixed(2)}\n`;
                text += `Please confirm availability.`;

                window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
            };

            // --- General Vendor Share (Expenses) ---
            // --- General Vendor Share (Expenses) ---
            window.shareVendorWhatsapp = (expenseId, supplierId) => {
                // 1. If Expense ID exists, use backend logic
                if (expenseId && expenseId > 0) {
                    fetch(`/admin/expenses/${expenseId}/whatsapp-vendor`)
                        .then(res => res.json())
                        .then(data => {
                            window.open(`https://wa.me/?text=${encodeURIComponent(data.text)}`, '_blank');
                        })
                        .catch(err => alert("Error generating message."));
                    return;
                }

                // 2. Role-Based Template Logic for Suppliers
                if (supplierId) {
                    const vendor = allSuppliers.find(s => s.id == supplierId);
                    const vendorType = vendor ? (vendor.type || 'General') : 'General';

                    const clientName = document.getElementById('client-name').value || 'Guest';
                    const arrivalDate = document.getElementById('arrival-date').value;
                    const duration = parseInt(document.getElementById('trip-duration').value || 1);
                    const nights = Math.max(1, duration - 1);

                    const adults = document.getElementById('pax-adults').value || 1;
                    const c1 = document.getElementById('pax-child-small').value || 0;
                    const c2 = document.getElementById('pax-child-large').value || 0;
                    const refId = " "BLADE_VAR" ";

                    if (vendorType.toLowerCase().includes('hotel') || vendorType.toLowerCase().includes('accommodation')) {
                        // Template A: Hotel Vendor (STRICT PRIVACY - ISOLATION MODE)
                        let text = `Hi, checking availability for this proposal:\n`;
                        text += `*Ref ID:* #${refId}\n`;
                        text += `*Guest:* ${clientName}\n`;
                        text += `*Arrival:* ${arrivalDate}\n`;
                        text += `*Stay:* ${nights} Nights\n`;

                        let rQty = 0, rType = [];
                        let totalCost = 0;
                        itinerary.forEach(d => {
                            if (d.hotels) d.hotels.forEach(h => {
                                rQty += parseInt(h.quantity || 0);
                                if (h.type && !rType.includes(h.type)) rType.push(h.type);
                                // Calculate total cost strictly for this vendor
                                if (vendor && h.name && h.name.toLowerCase().includes(vendor.name.toLowerCase())) {
                                    totalCost += (safeFloat(h.price_per_night) + safeFloat(h.add_on_price)) * safeFloat(h.quantity || 1);
                                }
                            });
                        });

                        text += `*Rooms:* ${rQty} ${rType.join(', ') || 'Standard'}\n`;
                        text += `*Total Pax:* ${adults} Adults`;
                        if (parseInt(c1) + parseInt(c2) > 0) text += `, ${parseInt(c1) + parseInt(c2)} Kids`;
                        text += `\n`;

                        if (totalCost > 0) {
                            text += `*Total Amount:*  "BLADE_VAR"  ${totalCost.toFixed(2)}\n`;
                        }

                        text += `*Auth by:*  "BLADE_VAR" \n`;

                        window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
                    } else if (vendorType.toLowerCase().includes('transport') || vendorType.toLowerCase().includes('driver') || vendorType.toLowerCase().includes('taxi')) {
                        // Template C: Transport/Driver
                        let text = `*TRANSPORT REQUEST*\n`;
                        text += `*Ref ID:* #${refId}\n`;
                        text += `*Total Pax:* ${adults} Adults`;
                        if (parseInt(c1) + parseInt(c2) > 0) text += `, ${parseInt(c1) + parseInt(c2)} Kids`;
                        text += `\n\n*ITINERARY DETAILS:*\n`;

                        itinerary.forEach((d) => {
                            text += `*Day ${d.day}:* ${d.program || 'Itinerary flow'}\n`;
                            let hName = 'Own Arrangement';
                            if (d.hotels && d.hotels.length > 0 && d.hotels[0].name) {
                                hName = d.hotels[0].name;
                            }
                            text += `📍 *Stay:* ${hName}\n\n`;
                        });

                        text += `*Auth by:*  "BLADE_VAR" \n`;
                        window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
                    } else if (vendorType.toLowerCase().includes('activity') || vendorType.toLowerCase().includes('ticket') || vendorType.toLowerCase().includes('attraction') || vendorType.toLowerCase().includes('entry') || vendorType.toLowerCase().includes('sightseeing')) {
                        // Template: Activity/Ticket (Hotel Style)
                        // Try to find specific expense for "Individual" share
                        let specificExp = null;
                        if (currentExpenses && expenseId) {
                            specificExp = currentExpenses.find(e => e.id == expenseId);
                        }

                        let text = `Hi, checking availability for this proposal:\n`;
                        text += `*Ref ID:* #${refId}\n`;
                        text += `*Guest:* ${clientName}\n`;
                        text += `*Arrival:* ${arrivalDate}\n`;
                        text += `*Total Pax:* ${adults} Adults`;
                        if (parseInt(c1) + parseInt(c2) > 0) text += `, ${parseInt(c1) + parseInt(c2)} Kids`;
                        text += `\n`;

                        if (specificExp) {
                            // Individual Share Mode
                            text += `*Activity:* ${specificExp.description}\n`;
                            // Professional Date Format
                            const expDate = specificExp.expense_date ? (new Date(specificExp.expense_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })) : '';
                            if (expDate && expDate !== 'Invalid Date') text += `*Date:* ${expDate}\n`;

                            text += `*Total Amount:*  "BLADE_VAR"  ${parseFloat(specificExp.amount || 0).toFixed(2)}\n`;
                        } else {
                            // Aggregate Mode (Fallback)
                            let actList = [];
                            let totalCost = 0;
                            itinerary.forEach((d) => {
                                if (d.activities) d.activities.forEach(a => {
                                    if (a.name) {
                                        actList.push(a.name);
                                        if (vendor && a.name.toLowerCase().includes(vendor.name.toLowerCase())) {
                                            totalCost += safeFloat(a.price || 0) * (parseInt(adults) + parseInt(c1) + parseInt(c2));
                                        }
                                    }
                                });
                                if (d.spots) d.spots.forEach(s => {
                                    if (s.name) actList.push(s.name);
                                    if (vendor && s.name && s.name.toLowerCase().includes(vendor.name.toLowerCase())) {
                                        totalCost += safeFloat(s.price || 0);
                                    }
                                });
                            });

                            if (actList.length > 0) text += `*Activity:* ${actList.join(', ')}\n`;
                            if (totalCost > 0) text += `*Total Amount:*  "BLADE_VAR"  ${totalCost.toFixed(2)}\n`;
                        }

                        text += `*Auth by:*  "BLADE_VAR" \n`;
                        window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
                    } else {
                        // General Vendor Share
                        let text = `*AVAILABILITY CHECK*\n`;
                        text += `Ref ID: #${refId}\n`;
                        text += `Guest: ${clientName}\n`;
                        text += `Arrival: ${arrivalDate}\n`;
                        text += `Pax: ${adults} Adults + ${parseInt(c1) + parseInt(c2)} Kids\n`;
                        text += `\nRep:  "BLADE_VAR" `;
                        window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
                    }
                }
            };

            // Save Logic
            document.getElementById('saveBtn').addEventListener('click', function () {
                // Collect involved vendors
                const selectedVendors = [];
                $('.vendor-cb:checked').each(function () {
                    selectedVendors.push($(this).val());
                });

                // Set primary vendor
                const primaryVendorId = selectedVendors.length > 0 ? selectedVendors[0] : '';
                document.getElementById('formSupplier').value = primaryVendorId;

                // Set Involved Vendors JSON
                document.getElementById('formInvolvedVendors').value = JSON.stringify(selectedVendors);

                document.getElementById('itineraryData').value = JSON.stringify(itinerary);
                document.getElementById('formMarkup').value = document.getElementById('markup-percentage').value;
                document.getElementById('formTitle').value = document.getElementById('proposal-title').value;
                document.getElementById('formClient').value = document.getElementById('client-name').value;
                document.getElementById('formNotes').value = document.getElementById('proposal-notes').value;

                // Sync Pax & Payment
                document.getElementById('formAdults').value = document.getElementById('pax-adults').value;
                document.getElementById('formChildSmall').value = document.getElementById('pax-child-small').value;
                document.getElementById('formChildLarge').value = document.getElementById('pax-child-large').value;
                document.getElementById('formPaymentStatus').value = document.getElementById('payment-status').value;
                document.getElementById('formPaymentReceived').value = document.getElementById('payment-received').value;
                document.getElementById('formPaymentDetails').value = document.getElementById('payment-details').value;

                // Sync Followup & Lifecycle
                document.getElementById('formFollowupStatus').value = document.getElementById('followup-status').value;
                document.getElementById('formNextFollowup').value = document.getElementById('next-followup').value;
                document.getElementById('formLifecycle').value = document.getElementById('proposal-lifecycle').value;

                document.getElementById('formArrivalDate').value = document.getElementById('arrival-date').value;
                document.getElementById('formDuration').value = document.getElementById('trip-duration').value;

                document.getElementById('saveForm').submit();
            });

            // Initialize display
            renderBuilder();
        