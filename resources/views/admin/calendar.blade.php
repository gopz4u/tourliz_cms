@extends('layouts.admin')

@section('title', 'Arrivals & Follow-ups Calendar')

@push('styles')
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
<style>
    /* Premium FullCalendar Custom Styling */
    .fc {
        font-family: inherit;
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.02);
    }
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: rgba(0, 0, 0, 0.04);
    }
    .fc .fc-toolbar-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.5px;
    }
    .fc .fc-button-primary {
        background-color: var(--primary);
        border-color: var(--primary);
        border-radius: 12px;
        font-weight: 600;
        padding: 8px 16px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(90, 82, 229, 0.15);
    }
    .fc .fc-button-primary:hover,
    .fc .fc-button-primary:focus {
        background-color: var(--primary-hover) !important;
        border-color: var(--primary-hover) !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(90, 82, 229, 0.25) !important;
    }
    .fc .fc-button-primary:disabled {
        background-color: var(--text-muted) !important;
        border-color: var(--text-muted) !important;
    }
    .fc .fc-button-active {
        background-color: var(--primary-hover) !important;
        border-color: var(--primary-hover) !important;
    }
    .fc-event {
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
        border: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        margin: 2px 0;
        transition: transform 0.15s ease;
    }
    .fc-event:hover {
        transform: scale(1.02);
        opacity: 0.95;
    }
    .fc-col-header-cell {
        background: #f8fafc;
        padding: 12px 0 !important;
        font-weight: 700;
        color: var(--text-muted-darker);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .fc-daygrid-day-number {
        font-weight: 700;
        color: var(--text-muted-darker);
        font-size: 0.9rem;
        padding: 8px !important;
        text-decoration: none !important;
    }
    .fc-day-today {
        background: rgba(90, 82, 229, 0.03) !important;
    }
    .fc-daygrid-day:hover {
        background: #f8fafc;
        cursor: pointer;
    }

    /* Filter cards */
    .filter-card {
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(0, 0, 0, 0.02);
    }
    .color-legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="bi bi-calendar3 me-2 text-primary"></i>Arrivals & Follow-ups Calendar</h2>
        <p class="text-muted mb-0">Monitor upcoming client arrivals, departures, and scheduled follow-ups in a monthly view.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Sidebar Filters -->
    <div class="col-lg-3">
        <div class="card filter-card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0" style="font-size: 1.05rem;"><i class="bi bi-funnel me-2 text-primary"></i>Calendar Filters</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3" id="calendarFilters">
                    <!-- B2B -->
                    <div class="form-check form-switch d-flex align-items-center justify-content-between ps-0">
                        <label class="form-check-label d-flex align-items-center gap-2 fw-semibold text-dark cursor-pointer" for="filterB2B">
                            <span class="color-legend-dot" style="background-color: #3b82f6;"></span>
                            B2B Proposals
                        </label>
                        <input class="form-check-input ms-0" type="checkbox" id="filterB2B" checked>
                    </div>
                    <!-- B2C -->
                    <div class="form-check form-switch d-flex align-items-center justify-content-between ps-0">
                        <label class="form-check-label d-flex align-items-center gap-2 fw-semibold text-dark cursor-pointer" for="filterB2C">
                            <span class="color-legend-dot" style="background-color: #10b981;"></span>
                            B2C Proposals
                        </label>
                        <input class="form-check-input ms-0" type="checkbox" id="filterB2C" checked>
                    </div>
                    <!-- Groups -->
                    <div class="form-check form-switch d-flex align-items-center justify-content-between ps-0">
                        <label class="form-check-label d-flex align-items-center gap-2 fw-semibold text-dark cursor-pointer" for="filterGroup">
                            <span class="color-legend-dot" style="background-color: #8b5cf6;"></span>
                            Group Proposals
                        </label>
                        <input class="form-check-input ms-0" type="checkbox" id="filterGroup" checked>
                    </div>
                    <!-- Bookings -->
                    <div class="form-check form-switch d-flex align-items-center justify-content-between ps-0">
                        <label class="form-check-label d-flex align-items-center gap-2 fw-semibold text-dark cursor-pointer" for="filterBooking">
                            <span class="color-legend-dot" style="background-color: #f97316;"></span>
                            Client Bookings
                        </label>
                        <input class="form-check-input ms-0" type="checkbox" id="filterBooking" checked>
                    </div>
                    
                    <hr class="my-2 opacity-50">

                    <!-- Follow-ups -->
                    <div class="form-check form-switch d-flex align-items-center justify-content-between ps-0">
                        <label class="form-check-label d-flex align-items-center gap-2 fw-semibold text-dark cursor-pointer" for="filterFollowup">
                            <span class="color-legend-dot" style="background-color: #ef4444;"></span>
                            Follow-up Alerts
                        </label>
                        <input class="form-check-input ms-0" type="checkbox" id="filterFollowup" checked>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend Card -->
        <div class="card filter-card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold text-dark mb-3">Trip Phase Legend</h6>
                <div class="d-flex flex-column gap-2 text-muted" style="font-size: 0.85rem;">
                    <div class="d-flex align-items-center gap-2">
                        <span>🛫</span>
                        <span><strong>Arrival:</strong> Start date of itinerary / trip</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span>🛬</span>
                        <span><strong>Departure:</strong> Calculated end date</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span>🔔</span>
                        <span><strong>Follow-up:</strong> Next scheduled followup date</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Calendar Area -->
    <div class="col-lg-9">
        <div id="calendar"></div>
    </div>
</div>

<!-- Modal: Event Details -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="eventDetailModalLabel">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge" id="modalEventType">B2B</span>
                        <span class="badge bg-light text-dark border" id="modalEventCategory">Arrival</span>
                    </div>
                    
                    <div>
                        <h4 class="fw-bold text-dark mb-1" id="modalEventClient">Client Name</h4>
                        <span class="text-muted" id="modalEventQuoteId">QT-000</span>
                    </div>

                    <div class="bg-light p-3 rounded-3" style="font-size: 0.9rem;">
                        <div class="row g-2">
                            <div class="col-6 text-muted">Destination / Package:</div>
                            <div class="col-6 fw-bold text-dark text-end" id="modalEventDest">Malaysia</div>

                            <div class="col-6 text-muted">Date:</div>
                            <div class="col-6 fw-bold text-dark text-end" id="modalEventDate">2026-06-24</div>

                            <div class="col-6 text-muted" id="modalEventPriceLabel">Price Quoted:</div>
                            <div class="col-6 fw-bold text-primary text-end" id="modalEventPrice">MYR 0.00</div>

                            <div class="col-6 text-muted">Status:</div>
                            <div class="col-6 text-end" id="modalEventStatus">
                                <span class="badge bg-outline-info text-info border">DRAFT</span>
                            </div>

                            <div class="col-6 text-muted" id="modalEventAgencyLabel">Agency Partner:</div>
                            <div class="col-6 fw-bold text-dark text-end" id="modalEventAgency">N/A</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary btn-sm px-3" id="modalEditBtn">View / Edit Details</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Daily Summary -->
<div class="modal fade" id="dayDetailModal" tabindex="-1" aria-labelledby="dayDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="dayDetailModalLabel">Schedule for <span id="modalSelectedDate">2026-06-24</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="bg-light">
                            <tr>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Client / Customer</th>
                                <th>Quote ID / Info</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="dayEventsTableBody">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
                <div class="text-center py-4 text-muted d-none" id="noDayEventsAlert">
                    <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                    No arrivals, departures, or follow-ups scheduled for this day.
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- FullCalendar JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        let calendar;

        const filters = {
            b2b: document.getElementById('filterB2B'),
            b2c: document.getElementById('filterB2C'),
            group: document.getElementById('filterGroup'),
            booking: document.getElementById('filterBooking'),
            followup: document.getElementById('filterFollowup')
        };

        // Bootstrap Modals
        const eventModal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
        const dayModal = new bootstrap.Modal(document.getElementById('dayDetailModal'));

        // Initialize FullCalendar
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            events: '{{ route("admin.calendar.events") }}',
            eventClick: function(info) {
                info.jsEvent.stopPropagation();
                info.jsEvent.preventDefault();
                showEventDetails(info.event);
            },
            dateClick: function(info) {
                showDaySummary(info.dateStr);
            },
            eventDidMount: function(info) {
                // Apply filter visibility
                const props = info.event.extendedProps;
                let visible = true;

                if (props.category === 'followup') {
                    visible = filters.followup.checked;
                } else {
                    const type = props.type.toLowerCase();
                    if (type === 'b2b' && !filters.b2b.checked) visible = false;
                    if (type === 'b2c' && !filters.b2c.checked) visible = false;
                    if (type === 'group' && !filters.group.checked) visible = false;
                    if (type === 'booking' && !filters.booking.checked) visible = false;
                }

                if (!visible) {
                    info.el.style.display = 'none';
                }
            }
        });

        calendar.render();

        // Re-evaluate filters on switch toggle
        Object.values(filters).forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                calendar.refetchEvents();
            });
        });

        // Show Single Event Detail in Modal
        function showEventDetails(event) {
            const props = event.extendedProps;
            const type = props.type;
            const category = props.category;

            // Set Type badges
            const typeBadge = document.getElementById('modalEventType');
            typeBadge.innerText = type;
            typeBadge.className = 'badge';
            if (type === 'B2B') typeBadge.classList.add('bg-primary');
            else if (type === 'B2C') typeBadge.classList.add('bg-success');
            else if (type === 'Group') typeBadge.classList.add('bg-warning', 'text-dark');
            else if (type === 'Booking') typeBadge.classList.add('bg-danger');

            const catBadge = document.getElementById('modalEventCategory');
            catBadge.innerText = category.toUpperCase();
            if (category === 'arrival') {
                catBadge.className = 'badge bg-outline-info text-info border border-info';
            } else if (category === 'departure') {
                catBadge.className = 'badge bg-outline-secondary text-secondary border border-secondary';
            } else {
                catBadge.className = 'badge bg-outline-danger text-danger border border-danger';
            }

            document.getElementById('modalEventClient').innerText = props.client;
            document.getElementById('modalEventQuoteId').innerText = props.quote_id;
            
            // Destination or Package
            const destLabel = document.getElementById('modalEventDest');
            if (type === 'Booking') {
                document.getElementById('modalEventPriceLabel').innerText = 'Amount Paid:';
                document.getElementById('modalEventAgencyLabel').style.display = 'none';
                document.getElementById('modalEventAgency').style.display = 'none';
                destLabel.innerText = props.package;
            } else {
                document.getElementById('modalEventPriceLabel').innerText = 'Price Quoted:';
                document.getElementById('modalEventAgencyLabel').style.display = 'table-row';
                document.getElementById('modalEventAgency').style.display = 'table-row';
                destLabel.innerText = props.destination;
                document.getElementById('modalEventAgency').innerText = props.agency || 'N/A';
            }

            // Date
            document.getElementById('modalEventDate').innerText = event.startStr || event.start.toISOString().split('T')[0];
            
            // Price
            document.getElementById('modalEventPrice').innerText = props.price;

            // Status Badge
            const statusContainer = document.getElementById('modalEventStatus');
            statusContainer.innerHTML = '';
            const stBadge = document.createElement('span');
            stBadge.className = 'badge px-2';
            stBadge.innerText = props.status.toUpperCase();
            if (props.status.toLowerCase() === 'approved' || props.status.toLowerCase() === 'confirmed' || props.status.toLowerCase() === 'paid') {
                stBadge.classList.add('bg-success');
            } else if (props.status.toLowerCase() === 'draft' || props.status.toLowerCase() === 'pending') {
                stBadge.classList.add('bg-warning', 'text-dark');
            } else {
                stBadge.classList.add('bg-secondary');
            }
            statusContainer.appendChild(stBadge);

            // Edit button link
            document.getElementById('modalEditBtn').setAttribute('href', props.edit_url);

            eventModal.show();
        }

        // Show Daily Summary Modal
        function showDaySummary(dateStr) {
            document.getElementById('modalSelectedDate').innerText = formatDateReadable(dateStr);
            const events = calendar.getEvents();
            const filteredEvents = events.filter(event => {
                const eventDate = event.startStr || event.start.toISOString().split('T')[0];
                if (eventDate !== dateStr) return false;
                
                // Also respect current active filters
                const props = event.extendedProps;
                if (props.category === 'followup') {
                    return filters.followup.checked;
                } else {
                    const type = props.type.toLowerCase();
                    if (type === 'b2b' && !filters.b2b.checked) return false;
                    if (type === 'b2c' && !filters.b2c.checked) return false;
                    if (type === 'group' && !filters.group.checked) return false;
                    if (type === 'booking' && !filters.booking.checked) return false;
                }
                return true;
            });

            const tableBody = document.getElementById('dayEventsTableBody');
            const emptyAlert = document.getElementById('noDayEventsAlert');
            tableBody.innerHTML = '';

            if (filteredEvents.length === 0) {
                emptyAlert.classList.remove('d-none');
                tableBody.closest('table').classList.add('d-none');
            } else {
                emptyAlert.classList.add('d-none');
                tableBody.closest('table').classList.remove('d-none');

                filteredEvents.forEach(event => {
                    const props = event.extendedProps;
                    const type = props.type;
                    const cat = props.category;

                    const row = document.createElement('tr');

                    // Type Badge
                    let typeBadgeClass = 'badge bg-primary';
                    if (type === 'B2C') typeBadgeClass = 'badge bg-success';
                    else if (type === 'Group') typeBadgeClass = 'badge bg-warning text-dark';
                    else if (type === 'Booking') typeBadgeClass = 'badge bg-danger';
                    
                    // Category Badge
                    let catBadgeClass = 'badge bg-outline-info text-info border border-info';
                    if (cat === 'departure') catBadgeClass = 'badge bg-outline-secondary text-secondary border border-secondary';
                    else if (cat === 'followup') catBadgeClass = 'badge bg-outline-danger text-danger border border-danger';

                    // Status Badge
                    let statusClass = 'badge bg-warning text-dark';
                    if (['approved', 'confirmed', 'paid'].includes(props.status.toLowerCase())) {
                        statusClass = 'badge bg-success';
                    } else if (['draft', 'pending'].includes(props.status.toLowerCase())) {
                        statusClass = 'badge bg-warning text-dark';
                    } else {
                        statusClass = 'badge bg-secondary';
                    }

                    row.innerHTML = `
                        <td><span class="${typeBadgeClass}">${type}</span></td>
                        <td><span class="${catBadgeClass}">${cat.toUpperCase()}</span></td>
                        <td><div class="fw-bold">${props.client}</div></td>
                        <td>
                            <div><code class="fw-bold">${props.quote_id}</code></div>
                            <small class="text-muted">${type === 'Booking' ? props.package : props.destination}</small>
                        </td>
                        <td><span class="fw-bold text-dark">${props.price}</span></td>
                        <td><span class="${statusClass}">${props.status.toUpperCase()}</span></td>
                        <td class="text-end">
                            <a href="${props.edit_url}" class="btn btn-sm btn-outline-primary" title="View/Edit"><i class="bi bi-pencil-square"></i></a>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            }

            dayModal.show();
        }

        // Format Date to: "Wednesday, 24 Jun 2026"
        function formatDateReadable(dateStr) {
            const date = new Date(dateStr + 'T00:00:00'); // Prevent timezone shift issues
            return date.toLocaleDateString('en-GB', {
                weekday: 'long',
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }
    });
</script>
@endpush
