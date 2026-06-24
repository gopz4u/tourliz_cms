@extends('layouts.admin')

@section('title', 'Edit group Proposal')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-pencil-square me-2"></i>Edit group Proposal</h2>
                <p class="text-muted mb-0">Building direct itinerary for
                    <strong>{{ data_get($itinerary, 'client_name') }}</strong>
                    ({{ data_get($itinerary, 'destination.name') }})
                </p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-dark" onclick="openVendorShareModal()">
                    <i class="bi bi-people me-1"></i> Vendor
                </button>
                <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#shareModal">
                    <i class="bi bi-share me-1"></i> Share
                </button>
                <a href="{{ route('admin.group-itineraries.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <button id="saveBtn" class="btn btn-success px-4 fw-bold">
                    <i class="bi bi-check2-circle me-1"></i> Save Proposal
                </button>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Share with Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
                        <p class="mt-2 fw-bold mb-3">PDF Proposal</p>

                        <div class="row g-2 px-3">
                            <div class="col-6">
                                <a href="{{ route('admin.group-itineraries.pdf', data_get($itinerary, 'id')) }}?public=1"
                                    class="btn btn-primary btn-sm w-100 py-2">
                                    <i class="bi bi-file-person me-1"></i> Customer Copy
                                </a>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-primary btn-sm w-100 py-2" onclick="copyPdfLink()">
                                    <i class="bi bi-link-45deg me-1"></i> Copy Link
                                </button>
                            </div>
                            @if(auth()->user()->isSuperAdmin())
                                <div class="col-12 mt-2">
                                    <a href="{{ route('admin.group-itineraries.pdf', data_get($itinerary, 'id')) }}"
                                        class="btn btn-outline-dark btn-sm w-100">
                                        <i class="bi bi-shield-lock me-1"></i> Download Internal Copy (Admin Only)
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="mt-4">
                        <i class="bi bi-whatsapp fs-1 text-success"></i>
                        <p class="mt-2 fw-bold">WhatsApp Summary</p>
                        <button class="btn btn-success" onclick="shareCustomerQuote()">
                            <i class="bi bi-send me-1"></i> Share as Text
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Sidebar: Settings -->
        <div class="col-lg-3">
            <!-- Client Info Card -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white fw-bold">Customer Profile</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Full Name</label>
                        <input type="text" id="client-name" class="form-control form-control-sm fw-bold"
                            value="{{ data_get($itinerary, 'client_name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Email Address</label>
                        <input type="email" id="client-email" class="form-control form-control-sm"
                            value="{{ data_get($itinerary, 'email') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Primary Phone</label>
                        <input type="text" id="client-phone" class="form-control form-control-sm"
                            value="{{ data_get($itinerary, 'phone') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Secondary Contact</label>
                        <input type="text" id="client-phone-secondary" class="form-control form-control-sm"
                            value="{{ data_get($itinerary, 'secondary_phone') }}" placeholder="Alternate Number">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted">Lead Source</label>
                        <select id="lead-source" class="form-select form-select-sm">
                            <option value="walk_in" {{ data_get($itinerary, 'lead_source') == 'walk_in' ? 'selected' : '' }}>
                                Walk-in
                            </option>
                            <option value="call" {{ data_get($itinerary, 'lead_source') == 'call' ? 'selected' : '' }}>
                                Phone/WhatsApp
                            </option>
                            <option value="social" {{ data_get($itinerary, 'lead_source') == 'social' ? 'selected' : '' }}>
                                Social Media
                            </option>
                            <option value="reference" {{ data_get($itinerary, 'lead_source') == 'reference' ? 'selected' : '' }}>Reference
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light fw-bold">Trip Details</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Proposal Title</label>
                        <input type="text" id="proposal-title" class="form-control form-control-sm"
                            value="{{ data_get($itinerary, 'title') }}">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small text-muted">Arrival Date</label>
                            <input type="date" id="arrival-date" class="form-control form-control-sm"
                                value="{{ data_get($itinerary, 'start_date') ? \Carbon\Carbon::parse(data_get($itinerary, 'start_date'))->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small text-muted">Days</label>
                            <input type="number" id="trip-duration" class="form-control form-control-sm"
                                value="{{ data_get($itinerary, 'duration_days') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Markup (%)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" id="markup-percentage" class="form-control"
                                value="{{ data_get($itinerary, 'markup_percentage') }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Involved Vendors</label>
                        <div id="vendor-checkboxes" class="border rounded p-2 bg-white mb-1"
                            style="max-height: 120px; overflow-y: auto;">
                            {{-- Checkboxes loaded via JS --}}
                            <div class="text-muted small py-1">Loading partners...</div>
                        </div>
                        <button type="button" class="btn btn-link btn-xs p-0 small text-decoration-none" onclick="loadSuppliers()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh List
                        </button>
                        <input type="hidden" id="supplier_id" value="{{ data_get($itinerary, 'supplier_id') }}">
                    </div>
                    <div class="row g-2 mb-3 border p-2 rounded bg-light">
                        <div class="col-4">
                            <label class="form-label small text-muted mb-1">Adults</label>
                            <input type="number" id="pax-adults" class="form-control form-control-sm"
                                value="{{ data_get($itinerary, 'adults', 1) }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label small text-muted mb-1">Child 2-6</label>
                            <input type="number" id="pax-child-small" class="form-control form-control-sm"
                                value="{{ data_get($itinerary, 'children_2_6', 0) }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label small text-muted mb-1">Child 6-11</label>
                            <input type="number" id="pax-child-large" class="form-control form-control-sm"
                                value="{{ data_get($itinerary, 'children_6_11', 0) }}">
                        </div>
                        <div class="col-12 mt-1">
                            <small class="text-info" style="font-size: 0.7rem;">Child 2-6: -75% | Child 6-11: -50%</small>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted">Internal Notes</label>
                        <textarea id="proposal-notes" class="form-control form-control-sm"
                            rows="2">{{ data_get($itinerary, 'notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light fw-bold">Lead & Followup</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Assigned To (Managed By)</label>
                        <select id="assigned-user" class="form-select form-select-sm">
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" {{ (data_get($itinerary, 'user_id', 0) == $admin->id) ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Lead Stage</label>
                        <select id="followup-status" class="form-select form-select-sm">
                            <option value="leads" {{ (data_get($itinerary, 'followup_status', 'leads') == 'leads') ? 'selected' : '' }}>
                                New lead</option>
                            <option value="interested" {{ data_get($itinerary, 'followup_status') == 'interested' ? 'selected' : '' }}>
                                Interested</option>
                            <option value="converted" {{ data_get($itinerary, 'followup_status') == 'converted' ? 'selected' : '' }}>
                                Booking Confirmed</option>
                            <option value="dead" {{ data_get($itinerary, 'followup_status') == 'dead' ? 'selected' : '' }}>
                                Lost Lead
                            </option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted">Next Followup</label>
                        <input type="date" id="next-followup" class="form-control form-control-sm"
                            value="{{ data_get($itinerary, 'next_followup_date') ? \Carbon\Carbon::parse(data_get($itinerary, 'next_followup_date'))->format('Y-m-d') : '' }}">
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light fw-bold">Direct Payment</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Status</label>
                        <select id="payment-status" class="form-select form-select-sm">
                            <option value="pending" {{ data_get($itinerary, 'payment_status') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="partially_paid" {{ data_get($itinerary, 'payment_status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                            <option value="paid" {{ data_get($itinerary, 'payment_status') == 'paid' ? 'selected' : '' }}>
                                Fully Paid
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Total Received</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">{{ data_get($itinerary, 'currency') }}</span>
                            <input type="number" id="payment-received" class="form-control fw-bold"
                                value="{{ data_get($itinerary, 'total_amount_received', 0) }}">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted">Payment Notes</label>
                        <textarea id="payment-details" class="form-control form-control-sm"
                            rows="2">{{ data_get($itinerary, 'payment_details') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4 border-infoshadow-sm">
                    <div class="card-header bg-info text-white fw-bold py-2 d-flex justify-content-between">
                        <span>Price Breakdown</span>
                        <i class="bi bi-calculator"></i>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Hotels & Rooms:</span>
                            <span id="preview-hotels">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Transport:</span>
                            <span id="preview-transport">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Activities & Tickets:</span>
                            <span id="preview-activities">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Meals:</span>
                            <span id="preview-meals">0.00</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between mb-1 small fw-bold">
                            <span>Base Cost:</span>
                            <span id="preview-base-total">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small text-info">
                            <span>Per Pax Estimate:</span>
                            <span id="preview-perpax-total">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small text-primary">
                            <span>Markup Cost (<span id="preview-markup-perc">0</span>%):</span>
                            <span id="preview-markup">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold border-top pt-2 text-dark fs-6">
                            <span>TOTAL QUOTE:</span>
                            <span id="preview-grand-total">0.00</span>
                        </div>
                        <div class="mt-2 text-center">
                            <small class="text-muted" style="font-size: 0.65rem;">* Updates in real-time. Save to finalize.</small>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                        <span>Actual Expenses</span>
                        <button type="button" class="btn btn-xs btn-light" data-bs-toggle="modal"
                            data-bs-target="#expenseModal">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" id="expense-table" style="font-size: 0.85rem;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-end">Amount</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loaded via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light py-2">
                        <div class="d-flex justify-content-between small fw-bold">
                            <span>Total Actual Cost:</span>
                            <span id="total-actual-cost">{{ data_get($itinerary, 'currency') }} 0.00</span>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-dark text-white fw-bold">Financial Summary</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-1 small">
                            <span>Total Quoted:</span>
                            <span id="summary-quoted-total">{{ data_get($itinerary, 'currency') }} {{ number_format(data_get($itinerary, 'total_price', 0), 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small text-danger">
                            <span>Actual Costs:</span>
                            <span id="summary-actual-cost">{{ data_get($itinerary, 'currency') }} 0.00</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold text-success fs-5">
                            <span>ACTUAL PROFIT:</span>
                            <span id="actual-profit">{{ data_get($itinerary, 'currency') }} 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small mt-1">
                            <span class="text-muted">Profit Margin:</span>
                            <span id="actual-margin" class="fw-bold">0.00%</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light fw-bold">Payment Progress</div>
                    <div class="card-body">
                        @php 
                                                    $tPrice = (float) data_get($itinerary, 'total_price', 0);
                            $tPaid = (float) data_get($itinerary, 'total_amount_received', 0);
                            $balance = $tPrice - $tPaid;
                            $percent = $tPrice > 0 ? ($tPaid / $tPrice) * 100 : 0;
                        @endphp
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Paid: {{ number_format($percent, 0) }}%</span>
                            <span class="fw-bold {{ $balance <= 0 ? 'text-success' : 'text-danger' }}">
                                Due: {{ data_get($itinerary, 'currency') }} {{ number_format($balance, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content: Builder -->
            <div class="col-lg-9">
                <div id="itinerary-builder"></div>
                <div class="text-center mt-4">
                    <button class="btn btn-primary px-5 shadow-sm" onclick="addDay()">
                        <i class="bi bi-plus-circle me-2"></i>Add Day {{ count(data_get($itinerary, 'itinerary', [])) + 1 }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden Save Form -->
        <form id="saveForm" action="{{ route('admin.group-itineraries.update', data_get($itinerary, 'id')) }}" method="POST"
            style="display:none;">
            @csrf @method('PUT')
            <input type="hidden" name="itinerary" id="itineraryData">
            <input type="hidden" name="title" id="formTitle">
            <input type="hidden" name="client_name" id="formClient">
            <input type="hidden" name="email" id="formEmail">
            <input type="hidden" name="phone" id="formPhone">
            <input type="hidden" name="secondary_phone" id="formPhoneSecondary">
            <input type="hidden" name="lead_source" id="formLeadSource">
            <input type="hidden" name="markup_percentage" id="formMarkup">
            <input type="hidden" name="adults" id="formAdults">
            <input type="hidden" name="children_2_6" id="formChildSmall">
            <input type="hidden" name="children_6_11" id="formChildLarge">
            <input type="hidden" name="payment_status" id="formPaymentStatus">
            <input type="hidden" name="total_amount_received" id="formPaymentReceived">
            <input type="hidden" name="payment_details" id="formPaymentDetails">
            <input type="hidden" name="followup_status" id="formFollowupStatus">
            <input type="hidden" name="next_followup_date" id="formNextFollowup">

            <input type="hidden" name="start_date" id="formArrivalDate">
            <input type="hidden" name="duration_days" id="formDuration">
            <input type="hidden" name="supplier_id" id="formSupplier">

            <input type="hidden" name="notes" id="formNotes">
            <input type="hidden" name="user_id" id="formAssignedUser">
            <input type="hidden" name="involved_vendors" id="formInvolvedVendors">
        </form>

        @include('admin.b2b.modals') <!-- Expense Modal -->
        <div class="modal fade" id="expenseModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header py-2">
                        <h5 class="modal-title fs-6">Add Actual Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="expenseForm">
                            <input type="hidden" name="itinerary_id" value="{{ data_get($itinerary, 'id') }}">
                            <input type="hidden" name="itinerary_type" value="group">
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Category</label>
                                <select name="category" class="form-select form-select-sm" required>
                                    <option value="Hotel">Hotel</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Activity">Activity/Sightseeing</option>
                                    <option value="Meal">Meal</option>
                                    <option value="Flight">Flight</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Amount ({{ data_get($itinerary, 'currency') }})</label>
                                <input type="number" step="0.01" name="amount" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Supplier <a href="#"
                                        class="float-end text-decoration-none" data-bs-toggle="modal"
                                        data-bs-target="#newSupplierModal">+ New</a></label>
                                <select name="supplier_id" id="expense-supplier-id" class="form-select form-select-sm">
                                    <option value="">Select Supplier (Optional)</option>
                                    {{-- Loaded via JS --}}
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Expense Date</label>
                                <input type="date" name="expense_date" class="form-control form-control-sm"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small text-muted mb-1">Description / Notes</label>
                                <input type="text" name="description" class="form-control form-control-sm"
                                    placeholder="e.g. Booking ref #123">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer py-1">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="saveExpense()">Save Expense</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Supplier Modal -->
        <div class="modal fade" id="newSupplierModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header py-2">
                        <h5 class="modal-title fs-6">Quick Add Supplier</h5>
                    </div>
                    <div class="modal-body">
                        <form id="quickSupplierForm">
                            <input type="hidden" name="destination_id" value="{{ data_get($itinerary, 'destination_id') }}">
                            <div class="mb-2">
                                <label class="small text-muted mb-1">Supplier Name</label>
                                <input type="text" name="name" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="small text-muted mb-1">Type</label>
                                <select name="type" class="form-select form-select-sm" required>
                                    <option value="Hotel">Hotel</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Activity">Activity</option>
                                    <option value="Agent">Agent</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer py-1">
                        <button type="button" class="btn btn-sm btn-secondary"
                            onclick="$('#newSupplierModal').modal('hide'); $('#expenseModal').modal('show');">Back</button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="quickSaveSupplier()">Save</button>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
    <script>
        let itinerary = @json(data_get($itinerary, 'itinerary', []));
        let currentDayIndex = 0;
        let currentType = '';
        const currency = "{{ data_get($itinerary, 'currency', 'INR') }}";
        const destinationId = "{{ data_get($itinerary, 'destination_id') }}";

        function ensureArray(arr) { return Array.isArray(arr) ? arr : []; }
        function safeFloat(val) {
            if (val === null || val === undefined || String(val).trim() === '') return 0;
            const n = parseFloat(val);
            return isNaN(n) ? 0 : n;
        }

        window.pushToExpenses = function (dayIndex, type, itemIndex) {
            const day = itinerary[dayIndex];
            const k = type === 'tickets' ? 'places' : (type === 'transports' ? 'transport' : type);
            const item = (type === 'hotels') ? day.hotels[itemIndex] : day[k][itemIndex];

            if (!item) return;

            let amount = 0, description = '', category = 'Other';

            if (type === 'hotels') {
                amount = (safeFloat(item.price_per_night) + safeFloat(item.add_on_price)) * safeFloat(item.quantity || 1);
                description = `Day ${day.day}: ${item.name} (${item.type})`;
                category = 'Hotel';
            } else if (type === 'activities' || type === 'tickets' || type === 'places') {
                if (item.entry_ticket) {
                    const et = item.entry_ticket;
                    const adultRate = safeFloat(et.adult_price || et.price);
                    amount = (adultRate * safeFloat(et.adult_qty || 0)) +
                        (safeFloat(et.child_2_6_price || 0) * safeFloat(et.child_2_6_qty || 0)) +
                        (safeFloat(et.child_6_11_price || 0) * safeFloat(et.child_6_11_qty || 0));
                    if ((safeFloat(et.adult_qty) + safeFloat(et.child_2_6_qty) + safeFloat(et.child_6_11_qty)) === 0) {
                        amount += adultRate;
                    }
                }
                if (item.hours && item.price_per_hour) {
                    amount += (safeFloat(item.hours) * safeFloat(item.price_per_hour));
                }
                description = `Day ${day.day}: ${item.name || item.attraction_name}`;
                category = 'Activity';
            } else if (type === 'spots') {
                amount = (safeFloat(item.hours || 0) * safeFloat(item.price_per_hour || 0));
                description = `Day ${day.day}: Spot - ${item.name}`;
                category = 'Activity';
            } else if (type === 'transports' || type === 'meals') {
                amount = safeFloat(item.price) * safeFloat(item.quantity || 1);
                description = `Day ${day.day}: ${type} - ${item.name}`;
                category = type === 'meals' ? 'Meal' : 'Transport';
            }

            if (!confirm(`Record this as an actual expense? \nAmount: ${amount.toFixed(2)}`)) return;

            const arrivalDateStr = document.getElementById('arrival-date').value;
            let expenseDate = new Date().toISOString().split('T')[0];
            if (arrivalDateStr) {
                const date = new Date(arrivalDateStr);
                date.setDate(date.getDate() + (day.day - 1));
                expenseDate = date.toISOString().split('T')[0];
            }

            const payload = {
                itinerary_id: "{{ data_get($itinerary, 'id') }}",
                itinerary_type: 'group',
                category: category,
                amount: amount,
                expense_date: expenseDate,
                description: description,
                supplier_id: item.supplier_id || null,
                _token: "{{ csrf_token() }}"
            };

            $.post("{{ route('admin.expenses.store') }}", payload, function (res) {
                if (res.success) {
                    alert('Expense recorded successfully!');
                    if (typeof loadExpenses === 'function') loadExpenses();
                }
            }).fail(function (xhr) {
                alert('Error recording expense.');
            });
        };

        document.addEventListener('DOMContentLoaded', function () {
            renderBuilder();
            loadExpenses();
            loadSuppliers();

            // Sync Pax to items
            const syncPax = () => {
                const adults = parseInt(document.getElementById('pax-adults').value || 1);
                const childS = parseInt(document.getElementById('pax-child-small').value || 0);
                const childL = parseInt(document.getElementById('pax-child-large').value || 0);

                itinerary.forEach(day => {
                    const keys = ['activities', 'places', 'transport', 'meals', 'hotels'];
                    keys.forEach(k => {
                        if (day[k]) {
                            day[k].forEach(item => {
                                if (item.entry_ticket) {
                                    item.entry_ticket.adult_qty = adults;
                                    item.entry_ticket.child_2_6_qty = childS;
                                    item.entry_ticket.child_6_11_qty = childL;
                                }
                                if (k === 'meals') {
                                    item.quantity = adults;
                                }
                            });
                        }
                    });
                });
                renderBuilder();
            };

            document.getElementById('pax-adults').addEventListener('change', syncPax);
            document.getElementById('pax-child-small').addEventListener('change', syncPax);
            document.getElementById('pax-child-large').addEventListener('change', syncPax);
            document.getElementById('markup-percentage')?.addEventListener('change', calculateDynamicTotal);
        });

        window.allSuppliers = [];
        function loadSuppliers() {
            $.get("{{ route('admin.suppliers.index') }}", function (data) {
                window.allSuppliers = data;
                // Populate Expense modal select
                const expenseSelect = $('#expense-supplier-id');
                expenseSelect.find('option:not(:first)').remove();

                // Populate Main proposal checkboxes
                const checkboxContainer = $('#vendor-checkboxes');
                checkboxContainer.empty();

                // Get already selected vendor IDs (from itinerary.settings or similar)
                const currentVendorId = parseInt("{{ data_get($itinerary, 'supplier_id', 0) }}");
                // We'll also check if there are multiple stored in the JSON metadata if you decide to implement that.
                // For now, let's just use the primary one for backward compatibility.

                data.forEach(s => {
                    const isChecked = (s.id == currentVendorId) ? 'checked' : '';

                    const optionHtml = `<option value="${s.id}">${s.name} (${s.type})</option>`;
                    expenseSelect.append(optionHtml);

                    const cbHtml = `
                        <div class="form-check small mb-1">
                            <input class="form-check-input vendor-cb" type="checkbox" value="${s.id}" id="vcb-${s.id}" ${isChecked}>
                            <label class="form-check-label text-truncate d-block" for="vcb-${s.id}" title="${s.name}">
                                ${s.name} <span class="text-muted" style="font-size: 0.6rem;">(${s.type})</span>
                            </label>
                        </div>
                    `;
                    checkboxContainer.append(cbHtml);
                });

                if (data.length === 0) {
                    checkboxContainer.html('<div class="text-muted small py-1">No vendors found</div>');
                }
            });
        }

        function quickSaveSupplier() {
            const formData = $('#quickSupplierForm').serialize();
            $.post("{{ route('admin.suppliers.store') }}", formData, function (res) {
                if (res.success) {
                    $('#newSupplierModal').modal('hide');
                    $('#expenseModal').modal('show');
                    $('#quickSupplierForm')[0].reset();
                    loadSuppliers();
                }
            });
        }

        function loadExpenses() {
            $.get("{{ route('admin.expenses.index') }}", {
                itinerary_id: "{{ data_get($itinerary, 'id') }}",
                itinerary_type: 'group'
            }, function (data) {
                const tbody = $('#expense-table tbody');
                tbody.empty();
                let total = 0;

                data.forEach(exp => {
                    total += parseFloat(exp.amount);
                    const vendorName = exp.supplier ? `<span class="badge bg-light text-dark border ms-1">${exp.supplier.name}</span>` : '';
                    tbody.append(`
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <div class="fw-bold">${exp.category}${vendorName}</div>
                                                                                                                <div class="text-muted" style="font-size: 0.7rem;">${exp.description || ''}</div>
                                                                                                            </td>
                                                                                                            <td class="text-end fw-bold text-danger">-${exp.amount}</td>
                                                                                                            <td class="text-end">
                                                                                                                <button class="btn btn-link text-danger p-0" onclick="deleteExpense(${exp.id})">
                                                                                                                    <i class="bi bi-x-circle"></i>
                                                                                                                </button>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    `);
                });

                if (data.length === 0) {
                    tbody.append('<tr><td colspan="3" class="text-center text-muted py-3">No expenses recorded yet</td></tr>');
                }

                updateFinancialSummary(total);
            });
        }

        function saveExpense() {
            const formData = $('#expenseForm').serialize();
            $.post("{{ route('admin.expenses.store') }}", formData, function (res) {
                if (res.success) {
                    $('#expenseModal').modal('hide');
                    $('#expenseForm')[0].reset();
                    loadExpenses();
                }
            }).fail(function (xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Check your inputs'));
            });
        }

        function deleteExpense(id) {
            if (!confirm('Remove this expense?')) return;
            $.ajax({
                url: `/admin/expenses/${id}`,
                type: 'DELETE',
                success: function (res) {
                    loadExpenses();
                }
            });
        }

        function updateFinancialSummary(actualCost) {
            const totalQuoted = parseFloat("{{ data_get($itinerary, 'total_price', 0) }}");
            const profit = totalQuoted - actualCost;
            const currencyStr = "{{ data_get($itinerary, 'currency') }} ";

            $('#total-actual-cost').text(currencyStr + actualCost.toFixed(2));
            $('#summary-actual-cost').text(currencyStr + actualCost.toFixed(2));
            $('#actual-profit').text(currencyStr + profit.toFixed(2));

            const profitEl = $('#actual-profit');
            if (profit >= 0) {
                profitEl.removeClass('text-danger').addClass('text-success');
            } else {
                profitEl.removeClass('text-success').addClass('text-danger');
            }
        }

        function renderBuilder() {
            const container = document.getElementById('itinerary-builder');
            if (!container) return;
            container.innerHTML = '';
            if (itinerary.length === 0) {
                container.innerHTML = '<div class="alert alert-info text-center py-5 border-0 shadow-sm"><i class="bi bi-info-circle fs-1 d-block mb-3"></i>No days added. Click "Add Day" below to start building.</div>';
                return;
            }
            itinerary.forEach((day, index) => {
                const card = createDayCard(day, index);
                container.appendChild(card);
                renderHotels(index, day);
                renderListItems(index, 'activities', ensureArray(day.activities));
                renderListItems(index, 'spots', ensureArray(day.spots));
                renderListItems(index, 'tickets', ensureArray(day.places));
                renderListItems(index, 'transports', ensureArray(day.transport));
                renderListItems(index, 'meals', ensureArray(day.meals));
            });

            calculateDynamicTotal();
        }

        function calculateDynamicTotal() {
            let totals = { hotels: 0, transport: 0, activities: 0, tickets: 0, meals: 0 };
            const currency = "{{ data_get($itinerary, 'currency', 'INR') }} ";

            itinerary.forEach(day => {
                // 1. Hotels
                let hotels = ensureArray(day.hotels);
                if (day.hotel && day.hotel.name && hotels.length === 0) hotels = [day.hotel];

                hotels.forEach(h => {
                    totals.hotels += (parseFloat(h.price_per_night || 0) + parseFloat(h.add_on_price || 0)) * parseFloat(h.quantity || 1);
                });

                // 2. Transport
                ensureArray(day.transport || day.transports).forEach(t => totals.transport += parseFloat(t.price || 0));

                // 3. Activities
                ensureArray(day.activities).forEach(a => {
                    if (a.entry_ticket) {
                        const et = a.entry_ticket;
                        const adultRate = parseFloat(et.adult_price || et.price || 0);
                        totals.activities += (adultRate * parseFloat(et.adult_qty || 0)) +
                            (parseFloat(et.child_2_6_price || 0) * parseFloat(et.child_2_6_qty || 0)) +
                            (parseFloat(et.child_6_11_price || 0) * parseFloat(et.child_6_11_qty || 0));
                        if ((parseFloat(et.adult_qty || 0) + parseFloat(et.child_2_6_qty || 0) + parseFloat(et.child_6_11_qty || 0)) === 0) {
                            totals.activities += adultRate;
                        }
                    }
                    if (a.hours && a.price_per_hour) {
                        totals.activities += (parseFloat(a.hours) * parseFloat(a.price_per_hour));
                    }
                });

                // 4. Tickets (places)
                ensureArray(day.places).forEach(p => {
                    if (p.entry_ticket) {
                        const et = p.entry_ticket;
                        const adultRate = parseFloat(et.adult_price || et.price || 0);
                        totals.tickets += (adultRate * parseFloat(et.adult_qty || 0)) +
                            (parseFloat(et.child_2_6_price || 0) * parseFloat(et.child_2_6_qty || 0)) +
                            (parseFloat(et.child_6_11_price || 0) * parseFloat(et.child_6_11_qty || 0));
                        if ((parseFloat(et.adult_qty || 0) + parseFloat(et.child_2_6_qty || 0) + parseFloat(et.child_6_11_qty || 0)) === 0) {
                            totals.tickets += adultRate;
                        }
                    }
                    if (p.hours && p.price_per_hour) {
                        totals.tickets += (parseFloat(p.hours) * parseFloat(p.price_per_hour));
                    }
                });

                // 5. Spots
                ensureArray(day.spots).forEach(s => {
                    totals.tickets += (parseFloat(s.hours || 0) * parseFloat(s.price_per_hour || 0));
                });

                // 6. Meals
                ensureArray(day.meals).forEach(m => totals.meals += parseFloat(m.price || 0) * parseFloat(m.quantity || 1));
            });

            const baseCost = totals.hotels + totals.transport + totals.activities + totals.tickets + totals.meals;
            const markupPerc = parseFloat(document.getElementById('markup-percentage')?.value || 0);
            const markupAmount = (baseCost * markupPerc) / 100;
            const grandTotal = baseCost + markupAmount;

            const safeSet = (id, val) => { const el = document.getElementById(id); if (el) el.innerText = currency + val.toFixed(2); };
            safeSet('preview-hotels', totals.hotels);
            safeSet('preview-transport', totals.transport);
            safeSet('preview-activities', totals.activities + totals.tickets);
            safeSet('preview-meals', totals.meals);
            safeSet('preview-base-total', baseCost);
            safeSet('preview-markup-perc', markupPerc);
            safeSet('preview-markup', markupAmount);
            safeSet('preview-markup', markupAmount);
            safeSet('preview-grand-total', grandTotal);

            // Per Pax Calculation
            const adults = parseInt(document.getElementById('pax-adults')?.value || 1);
            const c1 = parseInt(document.getElementById('pax-child-small')?.value || 0);
            const c2 = parseInt(document.getElementById('pax-child-large')?.value || 0);
            const totalPax = adults + c1 + c2;
            const perPax = totalPax > 0 ? (grandTotal / totalPax) : 0;
            safeSet('preview-perpax-total', perPax);

            // Update Financial Summary Card Live
            const summaryQuotedEl = document.getElementById('summary-quoted-total');
            if (summaryQuotedEl) summaryQuotedEl.innerText = currency + grandTotal.toFixed(2);
            
            const actualCostStr = document.getElementById('summary-actual-cost')?.innerText || '0.00';
            const actualCost = parseFloat(actualCostStr.replace(/[^0-9.]/g, '')) || 0;
            
            // PROFIT CALCULATION: Total Quote - Max(Estimated Base Cost, Actual Costs logged)
            const projectedCost = Math.max(baseCost, actualCost);
            const profit = grandTotal - projectedCost;
            const margin = grandTotal > 0 ? (profit / grandTotal) * 100 : 0;
            
            $('#actual-profit').text(currency + profit.toFixed(2));
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

        function renderHotels(dayIndex, day) {
            const container = document.getElementById(`hotels-container-${dayIndex}`);
            if (!container) return;
            container.innerHTML = '';

            let hotels = ensureArray(day.hotels);

            if (day.hotel && day.hotel.name && hotels.length === 0) {
                hotels = [day.hotel];
                day.hotels = hotels;
                delete day.hotel;
            }

            if (hotels.length === 0) {
                const placeholder = document.createElement('div');
                placeholder.className = 'text-center p-3 border border-dashed rounded-3 text-muted mb-2';
                placeholder.style.cssText = 'font-size: 0.8rem; background-color: #fafafa; border-color: #dee2e6 !important;';
                placeholder.innerHTML = `
                    <i class="bi bi-building fs-5 d-block mb-1 text-primary"></i>
                    No hotels added yet — click "Master" to search inventory or "Add Manual" to enter custom details.
                `;
                container.appendChild(placeholder);
                return;
            }

            hotels.forEach((hotel, hIndex) => {
                const row = document.createElement('div');
                row.className = 'mb-3 bg-light rounded-3 p-2 border position-relative';
                row.innerHTML = `
                    <div class="d-flex flex-wrap flex-md-nowrap gap-2 mb-2">
                        <input type="text" class="form-control fw-bold border-0 bg-transparent flex-grow-1" placeholder="Hotel Name"
                            value="${hotel.name || ''}" style="box-shadow:none; font-size: 1rem;"
                            onchange="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'name', this.value)">
                        
                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-circle border-0" onclick="window.pushToExpenses(${dayIndex}, 'hotels', ${hIndex})" title="Log as Expense"><i class="bi bi-cash-coin"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-circle border-0" onclick="window.shareHotelRequest(${dayIndex}, ${hIndex})" title="Share Booking"><i class="bi bi-whatsapp"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle border-0" onclick="window.removeItem(${dayIndex}, 'hotels', ${hIndex})"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                    <input type="text" class="form-control form-control-sm border-0 mb-2 bg-white" placeholder="Room Type (e.g. Deluxe Room)"
                        value="${hotel.type || ''}"
                        onchange="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'type', this.value)">
                    
                    <div class="d-flex flex-wrap gap-2 text-nowrap">
                        <div class="input-group input-group-sm flex-fill" style="min-width: 90px;">
                            <span class="input-group-text bg-white border-0 text-muted px-2"><i class="bi bi-key"></i></span>
                            <input type="number" class="form-control border-0 pt-1" placeholder="Qty"
                                value="${hotel.quantity || 1}"
                                onchange="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'quantity', this.value)">
                        </div>
                        <div class="input-group input-group-sm flex-fill" style="min-width: 120px;">
                            <span class="input-group-text bg-white border-0 text-muted px-2">${currency}</span>
                            <input type="number" class="form-control border-0 pt-1" placeholder="Rate/Night"
                                value="${hotel.price_per_night || 0}"
                                onchange="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'price_per_night', this.value)">
                        </div>
                        <div class="input-group input-group-sm flex-fill" style="min-width: 100px;">
                            <span class="input-group-text bg-white border-0 text-muted px-2">+Addon</span>
                            <input type="number" class="form-control border-0 pt-1" placeholder="Addon"
                                value="${hotel.add_on_price || 0}"
                                onchange="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'add_on_price', this.value)">
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top border-white">
                        <div class="d-flex align-items-center gap-2">
                            <label class="small text-secondary fw-semibold text-nowrap"><i class="bi bi-shop me-1"></i> Vendor:</label>
                            <select class="form-select form-select-sm border-0 shadow-sm" style="background: rgba(255,255,255,0.7);" onchange="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'supplier_id', this.value)">
                                <option value="">-- No Vendor --</option>
                                ${(window.allSuppliers || []).map(s => `<option value="${s.id}" ${hotel.supplier_id == s.id ? 'selected' : ''}>${s.name} (${s.type})</option>`).join('')}
                            </select>
                        </div>
                    </div>`;
                container.appendChild(row);
            });
        }

        function createDayCard(day, index) {
            const div = document.createElement('div');
            div.className = 'card mb-4 border-0 shadow-sm rounded-4 overflow-hidden';
            div.innerHTML = `
                <div class="card-header bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 border-bottom gap-3">
                    <div class="d-flex align-items-center flex-grow-1">
                        <span class="badge bg-primary rounded-pill me-3 px-3 py-2 shadow-sm" style="font-size:0.9rem;">Day ${day.day}</span>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0 text-muted px-0"><i class="bi bi-pencil-square"></i></span>
                            <input type="text" class="form-control fw-bold border-0 bg-transparent fs-5 px-2" 
                                value="${day.title || ''}"
                                onchange="window.updateField(${index}, 'title', this.value)"
                                placeholder="Enter Day Title..." style="box-shadow: none;">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light text-success fw-bold px-3 rounded-pill shadow-sm" onclick="window.shareDayToDriver(${index})" title="Share Job Sheet to Driver">
                            <i class="bi bi-whatsapp"></i> <span class="d-none d-md-inline">Job Sheet</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm" style="width:32px;height:32px;padding:0;" onclick="window.removeDay(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4 bg-light">
                    <div class="row g-4">
                        <!-- Left: Logistics (Hotels & Transport first for logic flow) -->
                        <div class="col-lg-5 col-md-12 order-2 order-lg-1">
                            <!-- Hotels -->
                            <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="text-uppercase small fw-bold mb-0" style="color:var(--bs-primary);"><i class="bi bi-building me-2"></i>Hotels & Rooms</label>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-sm" style="font-size:0.75rem;" onclick="window.openInventoryModal(${index}, 'hotels')">Select Master</button>
                                </div>
                                <div id="hotels-container-${index}" class="mb-2"></div>
                                <button type="button" class="btn btn-sm btn-outline-primary w-100 rounded-pill border-dashed mt-2" onclick="window.addItem(${index}, 'hotels')">
                                    <i class="bi bi-plus"></i> Add Manual Hotel
                                </button>
                            </div>

                            <!-- Transport -->
                            <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="text-uppercase small fw-bold mb-0 text-secondary"><i class="bi bi-car-front me-2"></i>Transport</label>
                                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3 py-1 shadow-sm" style="font-size:0.75rem;" onclick="window.openInventoryModal(${index}, 'transports')">Master</button>
                                </div>
                                <div id="transports-container-${index}"></div>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 rounded-pill border-dashed mt-2" onclick="window.addItem(${index}, 'transports')">
                                    <i class="bi bi-plus"></i> Add Manual Transport
                                </button>
                            </div>
                        </div>

                        <!-- Right: Daily Itinerary Items -->
                        <div class="col-lg-7 col-md-12 order-1 order-lg-2">
                            <!-- Destinations / Spots -->
                            <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="text-uppercase small fw-bold mb-0 text-info"><i class="bi bi-geo-alt me-2"></i>Tourist Spots</label>
                                    <button type="button" class="btn btn-sm btn-info text-white rounded-pill px-3 py-1 shadow-sm" style="font-size:0.75rem;" onclick="window.openInventoryModal(${index}, 'spots')"><i class="bi bi-search"></i> Master</button>
                                </div>
                                <div id="spots-container-${index}" class="mb-2"></div>
                                <button type="button" class="btn btn-sm btn-outline-info w-100 rounded-pill border-dashed" onclick="window.addItem(${index}, 'spots')">
                                    <i class="bi bi-plus"></i> Add Tourist Spot
                                </button>
                            </div>

                            <!-- Activities -->
                            <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="text-uppercase small fw-bold mb-0 text-warning"><i class="bi bi-lightning me-2"></i>Activities</label>
                                    <button type="button" class="btn btn-sm btn-warning text-dark rounded-pill px-3 py-1 shadow-sm" style="font-size:0.75rem;" onclick="window.openInventoryModal(${index}, 'activities')"><i class="bi bi-search"></i> Master</button>
                                </div>
                                <div id="activities-container-${index}" class="mb-2"></div>
                                <button type="button" class="btn btn-sm btn-outline-warning text-dark w-100 rounded-pill border-dashed" onclick="window.addItem(${index}, 'activities')">
                                    <i class="bi bi-plus"></i> Add Activity
                                </button>
                            </div>

                            <!-- Tickets -->
                            <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="text-uppercase small fw-bold mb-0 text-danger"><i class="bi bi-ticket-perforated me-2"></i>Entry Tickets</label>
                                    <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1 shadow-sm" style="font-size:0.75rem;" onclick="window.openInventoryModal(${index}, 'tickets')"><i class="bi bi-search"></i> Master</button>
                                </div>
                                <div id="tickets-container-${index}" class="mb-2"></div>
                                <button type="button" class="btn btn-sm btn-outline-danger w-100 rounded-pill border-dashed" onclick="window.addItem(${index}, 'tickets')">
                                    <i class="bi bi-plus"></i> Add Ticket
                                </button>
                            </div>

                            <!-- Meals -->
                            <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="text-uppercase small fw-bold mb-0 text-success"><i class="bi bi-cup-hot me-2"></i>Meals</label>
                                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1 shadow-sm" style="font-size:0.75rem;" onclick="window.openInventoryModal(${index}, 'meals')"><i class="bi bi-search"></i> Master</button>
                                </div>
                                <div id="meals-container-${index}" class="mb-2"></div>
                                <button type="button" class="btn btn-sm btn-outline-success w-100 rounded-pill border-dashed" onclick="window.addItem(${index}, 'meals')">
                                    <i class="bi bi-plus"></i> Add Meal
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 bg-white p-3 rounded-4 shadow-sm border-0">
                        <label class="small fw-bold text-muted text-uppercase mb-2"><i class="bi bi-journal-text me-1"></i> Day Flow / Notes</label>
                        <textarea class="form-control bg-light border-0" placeholder="Describe the day's flow..." rows="2" style="resize:none;"
                        onchange="window.updateField(${index}, 'notes', this.value)">${day.notes || ''}</textarea>
                    </div>
                </div>`;
            return div;
        }

        function renderListItems(dayIndex, type, items) {
            const container = document.getElementById(`${type}-container-${dayIndex}`);
            if (!container) return;
            container.innerHTML = '';

            const safeItems = Array.isArray(items) ? items : [];

            if (safeItems.length === 0) {
                const placeholder = document.createElement('div');
                placeholder.className = 'text-center p-3 border border-dashed rounded-3 text-muted mb-2';
                placeholder.style.cssText = 'font-size: 0.8rem; background-color: #fafafa; border-color: #dee2e6 !important;';
                
                let icon = '';
                let colorClass = '';
                let searchBtn = 'the search icon';
                let addBtn = '';
                let textName = '';

                switch (type) {
                    case 'transports':
                        icon = 'bi-car-front';
                        colorClass = 'text-secondary';
                        searchBtn = '"Master"';
                        addBtn = '"Add Manual"';
                        textName = 'transport';
                        break;
                    case 'spots':
                        icon = 'bi-geo-alt';
                        colorClass = 'text-info';
                        searchBtn = 'the search icon';
                        addBtn = '"Add Tourist Spot"';
                        textName = 'tourist spots';
                        break;
                    case 'activities':
                        icon = 'bi-lightning';
                        colorClass = 'text-warning';
                        searchBtn = 'the search icon';
                        addBtn = '"Add Activity"';
                        textName = 'activities';
                        break;
                    case 'tickets':
                        icon = 'bi-ticket-perforated';
                        colorClass = 'text-danger';
                        searchBtn = 'the search icon';
                        addBtn = '"Add Ticket"';
                        textName = 'entry tickets';
                        break;
                    case 'meals':
                        icon = 'bi-cup-hot';
                        colorClass = 'text-success';
                        searchBtn = 'the search icon';
                        addBtn = '"Add Meal"';
                        textName = 'meals';
                        break;
                }

                placeholder.innerHTML = `
                    <i class="bi ${icon} fs-5 d-block mb-1 ${colorClass}"></i>
                    No ${textName} added yet — click ${searchBtn} to search inventory or ${addBtn} to enter custom details.
                `;
                container.appendChild(placeholder);
                return;
            }

            safeItems.forEach((item, itemIndex) => {
                const row = document.createElement('div');
                row.className = 'mb-2 p-2 bg-light rounded-3 border';

                const nameField = type === 'tickets' ? 'attraction_name' : 'name';

                let html = `
                    <div class="d-flex gap-2 mb-2 align-items-center flex-wrap flex-md-nowrap">
                        <input type="text" class="form-control fw-bold border-0 bg-white shadow-sm flex-grow-1" placeholder="Name" 
                            value="${item[nameField] || ''}" 
                            onchange="window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, '${nameField}', this.value)">
                        
                        <div class="d-flex align-items-center flex-shrink-0">
                            <button type="button" class="btn btn-sm btn-outline-primary border-0 rounded-circle" onclick="window.pushToExpenses(${dayIndex}, '${type}', ${itemIndex})" title="Log as Expense"><i class="bi bi-cash-coin"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="window.removeItem(${dayIndex}, '${type}', ${itemIndex})"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                    <input type="text" class="form-control form-control-sm border-0 bg-transparent mb-2" placeholder="Description... (optional)"
                        value="${item.description || ''}"
                        onchange="window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, 'description', this.value)">
                `;

                if (type !== 'spots' && type !== 'activities' && type !== 'tickets') {
                    let priceValue = (type === 'transports' || type === 'meals') ? (item.price || 0) : (item.entry_ticket?.price || 0);
                    let qtyValue = (type === 'meals') ? (item.quantity || 1) : 1;

                    let onPriceChange = (type === 'tickets')
                        ? `window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'price', this.value)`
                        : `window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, 'price', this.value)`;

                    html += `
                        <div class="d-flex flex-wrap gap-2 mt-2 px-1">
                            <div class="input-group input-group-sm flex-fill shadow-sm" style="min-width: 120px;">
                                <span class="input-group-text bg-white border-0 text-muted px-2">${currency}</span>
                                <input type="number" class="form-control border-0 pt-1" placeholder="Price" value="${priceValue}" onchange="${onPriceChange}">
                            </div>
                            ${type === 'meals' ? `
                            <div class="input-group input-group-sm flex-fill shadow-sm" style="min-width: 80px;">
                                <span class="input-group-text bg-white border-0 text-muted px-2">Qty</span>
                                <input type="number" class="form-control border-0 pt-1" value="${qtyValue}" onchange="window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, 'quantity', this.value)">
                            </div>` : ''}
                        </div>
                    `;
                } else if (type === 'spots') {
                    html += `
                        <div class="d-flex flex-wrap flex-md-nowrap gap-2 mt-2 px-1">
                            <div class="input-group input-group-sm flex-fill shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted px-2"><i class="bi bi-clock me-1"></i> Hrs</span>
                                <input type="number" class="form-control border-0 pt-1" value="${item.hours || 0}" onchange="window.updateListItem(${dayIndex}, 'spots', ${itemIndex}, 'hours', this.value)">
                            </div>
                            <div class="input-group input-group-sm flex-fill shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted px-2 text-nowrap">${currency}/hr</span>
                                <input type="number" class="form-control border-0 pt-1" value="${item.price_per_hour || 0}" onchange="window.updateListItem(${dayIndex}, 'spots', ${itemIndex}, 'price_per_hour', this.value)">
                            </div>
                        </div>
                    `;
                } else if (type === 'activities' || type === 'tickets') {
                    const et = item.entry_ticket || {};
                    html += `
                        <div class="mt-2 pt-2 border-top border-light border-2">
                            <div class="row g-2 text-center text-muted small fw-bold mb-1">
                                <div class="col-4 text-start">Pax Type</div>
                                <div class="col-3">Qty</div>
                                <div class="col-5">Price (${currency})</div>
                            </div>
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-4 small text-secondary fw-semibold">Adult</div>
                                <div class="col-3"><input type="number" class="form-control form-control-sm border-0 shadow-sm px-2 text-center" value="${et.adult_qty || 0}" onchange="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'adult_qty', this.value)"></div>
                                <div class="col-5"><input type="number" class="form-control form-control-sm border-0 shadow-sm px-2" value="${et.adult_price || et.price || 0}" onchange="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'adult_price', this.value)"></div>
                            </div>
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-4 small text-secondary fw-semibold">Child 2-6</div>
                                <div class="col-3"><input type="number" class="form-control form-control-sm border-0 shadow-sm px-2 text-center" value="${et.child_2_6_qty || 0}" onchange="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'child_2_6_qty', this.value)"></div>
                                <div class="col-5"><input type="number" class="form-control form-control-sm border-0 shadow-sm px-2" value="${et.child_2_6_price || 0}" onchange="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'child_2_6_price', this.value)"></div>
                            </div>
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-4 small text-secondary fw-semibold">Child 6-11</div>
                                <div class="col-3"><input type="number" class="form-control form-control-sm border-0 shadow-sm px-2 text-center" value="${et.child_6_11_qty || 0}" onchange="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'child_6_11_qty', this.value)"></div>
                                <div class="col-5"><input type="number" class="form-control form-control-sm border-0 shadow-sm px-2" value="${et.child_6_11_price || 0}" onchange="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'child_6_11_price', this.value)"></div>
                            </div>
                            ${type === 'activities' ? `
                            <div class="row g-2 mt-2 pt-2 border-top border-light border-2 align-items-center">
                                <div class="col-12 col-md-4 small text-secondary fw-semibold"><i class="bi bi-stopwatch text-warning me-1"></i> Extra Hour</div>
                                <div class="col-6 col-md-3">
                                    <div class="input-group input-group-sm shadow-sm"><span class="input-group-text bg-white border-0 text-muted px-2">Hrs</span><input type="number" class="form-control border-0 px-2" value="${item.hours || ''}" onchange="window.updateListItem(${dayIndex}, 'activities', ${itemIndex}, 'hours', this.value)"></div>
                                </div>
                                <div class="col-6 col-md-5">
                                    <div class="input-group input-group-sm shadow-sm"><span class="input-group-text bg-white border-0 text-muted px-2">Rate</span><input type="number" class="form-control border-0 px-2" value="${item.price_per_hour || ''}" onchange="window.updateListItem(${dayIndex}, 'activities', ${itemIndex}, 'price_per_hour', this.value)"></div>
                                </div>
                            </div>` : ''}
                            
                            <div class="mt-2 pt-2 border-top border-light border-2">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="small text-secondary fw-semibold text-nowrap"><i class="bi bi-shop me-1"></i> Vendor:</label>
                                    <select class="form-select form-select-sm border-0 shadow-sm" onchange="window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, 'supplier_id', this.value)">
                                        <option value="">-- No Vendor --</option>
                                        ${(window.allSuppliers || []).map(s => `<option value="${s.id}" ${item.supplier_id == s.id ? 'selected' : ''}>${s.name} (${s.type})</option>`).join('')}
                                    </select>
                                </div>
                            </div>
                        </div>
                    `;
                }

                row.innerHTML = html;
                container.appendChild(row);
            });
        }

        // Inventory Modal Logic
        const inventoryModal = new bootstrap.Modal(document.getElementById('inventoryModal'));

        // Add listener for filter changes
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('inventoryDestinationId')?.addEventListener('change', fetchItems);
        });

        window.filterInventoryCities = function() {
            const country = document.getElementById('inventoryCountrySelect').value;
            const citySelect = document.getElementById('inventoryDestinationId');
            if(!citySelect) return;
            const options = citySelect.options;

            citySelect.value = ""; // Reset city selection when country changes

            for (let i = 1; i < options.length; i++) {
                const opt = options[i];
                if (!country || opt.getAttribute('data-country') === country) {
                    opt.style.display = "";
                } else {
                    opt.style.display = "none";
                }
            }
            fetchItems(); // Refresh items for "All Cities" in this country
        };

        window.openInventoryModal = function (idx, type) {
            currentDayIndex = idx;
            currentType = type;
            document.getElementById('inventoryModalTitle').innerText = 'Select ' + type.charAt(0).toUpperCase() + type.slice(1) + ' from Master';

            // On open, ensure cities are filtered by the current country selection
            filterInventoryCities();

            inventoryModal.show();
        };

        function fetchItems() {
            const dSelect = document.getElementById('inventoryDestinationId');
            const selectedDestinationId = dSelect ? dSelect.value : "";
            const countryFilter = document.getElementById('inventoryCountrySelect')?.value || "";
            const search = document.getElementById('inventorySearch')?.value || "";

            let url = `/api/inventory/${currentType}?search=${encodeURIComponent(search)}`;
            if (selectedDestinationId) {
                url += `&destination_id=${selectedDestinationId}`;
            } else if (countryFilter) {
                url += `&country=${encodeURIComponent(countryFilter)}`;
            }
            
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
                    const vendors = [...new Set(data.map(d => d.supplier ? d.supplier.name : (d.supplier_id ? 'Vendor #'+d.supplier_id : 'General')))].sort();
                    const vSelect = document.getElementById('wiz-vendor');
                    const sSelect = document.getElementById('wiz-service');
                    const vehSelect = document.getElementById('wiz-vehicle');
                    const resDiv = document.getElementById('wiz-result');

                    vendors.forEach(v => vSelect.add(new Option(v, v)));

                    vSelect.addEventListener('change', function() {
                        sSelect.innerHTML = '<option value="">Select Service / Duration</option>';
                        vehSelect.innerHTML = '<option value="">Select Vehicle</option>';
                        sSelect.disabled = true;
                        vehSelect.disabled = true;
                        resDiv.style.display = 'none';

                        if(this.value) {
                            const filtered = allTransports.filter(d => (d.supplier ? d.supplier.name : (d.supplier_id ? 'Vendor #'+d.supplier_id : 'General')) === this.value);
                            const services = [...new Set(filtered.map(d => d.duration || d.name))].sort();
                            services.forEach(s => sSelect.add(new Option(s, s)));
                            sSelect.disabled = false;
                        }
                    });

                    sSelect.addEventListener('change', function() {
                        vehSelect.innerHTML = '<option value="">Select Vehicle</option>';
                        vehSelect.disabled = true;
                        resDiv.style.display = 'none';

                        if(this.value) {
                                const vendor = vSelect.value;
                                const filtered = allTransports.filter(d => 
                                (d.supplier ? d.supplier.name : (d.supplier_id ? 'Vendor #'+d.supplier_id : 'General')) === vendor && 
                                (d.duration || d.name) === this.value
                                );
                                const vehicles = [...new Set(filtered.map(d => d.vehicle_type))].sort();
                                vehicles.forEach(v => vehSelect.add(new Option(v, v)));
                                vehSelect.disabled = false;
                        }
                    });

                    vehSelect.addEventListener('change', function() {
                        if(this.value) {
                            const vendor = vSelect.value;
                            const service = sSelect.value;
                            const item = allTransports.find(d => 
                                (d.supplier ? d.supplier.name : (d.supplier_id ? 'Vendor #'+d.supplier_id : 'General')) === vendor && 
                                (d.duration || d.name) === service && 
                                d.vehicle_type === this.value
                            );

                            if(item) {
                                const currencyStr = "{{ data_get($itinerary, 'currency', 'INR') }}";
                                document.getElementById('wiz-price').innerText = `${currencyStr} ${item.base_price}`;
                                document.getElementById('wiz-add-btn').onclick = () => {               
                                    // group specific selectItem call structure might differ locally if logic differs
                                    // Checks group's selectItem implementation if needed. 
                                    // group usually has: selectItem(item, subItem).
                                    // But Wait, group code snippets didn't show selectItem definition clearly.
                                    // Oh, I can just define the onClick inline or rely on existing global `selectItem`? 
                                    // Wait, group view snippet above (lines 1091+) DOES NOT SHOW selectItem being defined there, 
                                    // but it shows `data.forEach`...
                                    // Ah, line 1091+ calls `selectItem`? No, I need to see the buttons.
                                    // Ah, I need to verify `selectItem` exists in group.
                                    // Line 847 in B2B defined selectItem.
                                    // group probably defines it somewhere too. 
                                    // IF NOT, I might break group.
                                    // Let's look for `function selectItem` in group.
                                    if(typeof selectItem === 'function') {
                                        selectItem(item);
                                    } else {
                                        // Fallback manual add if selectItem is missing?
                                        window.addItem(currentDayIndex, 'transports');
                                        // But addItem adds empty. We want to populate.
                                        // Let's assume selectItem exists because other buttons use it in the loop likely.
                                        // Wait, the snippet above from 1091 shows creation of buttons but cuts off before onclick assignment.
                                        // Let's assume validation.
                                        // Re-reading prior B2B snippet: `selectItem` handles everything.
                                        selectItem(item);
                                    }
                                };
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
                        if (item.rooms && item.rooms.length > 0) {
                            item.rooms.forEach(room => {
                                const rb = document.createElement('div');
                                rb.className = 'btn btn-sm btn-light border text-start d-flex justify-content-between align-items-center';
                                rb.innerHTML = `<span>${room.room_type}</span> <span class="fw-bold text-primary">${currency} ${room.base_price}</span>`;
                                rb.onclick = (e) => { e.stopPropagation(); selectItem(item, room); };
                                roomContainer.appendChild(rb);
                            });
                        } else {
                            const rb = document.createElement('div');
                            rb.className = 'btn btn-sm btn-light border text-start text-muted';
                            rb.innerHTML = `<span>No rooms added yet</span>`;
                            roomContainer.appendChild(rb);
                        }
                    } else if (currentType === 'activities') {
                        btn.innerHTML = `<div class="d-flex justify-content-between"><strong>${item.name}</strong> <span class="text-primary fw-bold">${currency} ${item.base_price}</span></div><small class="text-muted d-block">${item.duration || ''}</small><small class="text-muted text-truncate d-block" style="max-width: 90%">${item.description || ''}</small>`;
                        btn.onclick = () => selectItem(item);
                    } else if (currentType === 'tickets') {
                        btn.innerHTML = `<div class="d-flex justify-content-between"><strong>${item.attraction_name}</strong> <span class="text-primary fw-bold">${currency} ${item.adult_price}</span></div><small class="text-muted">Adult Entry Ticket</small>`;
                        btn.onclick = () => selectItem(item);
                    } else if (currentType === 'spots') {
                        btn.innerHTML = `<div class="d-flex justify-content-between"><strong>${item.name}</strong></div><small class="text-muted text-truncate d-block" style="max-width: 90%">${item.description || ''}</small>`;
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
                    currency: currency
                    ,
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
                itinerary[currentDayIndex].transport.push({ name: item.name, price: item.base_price, currency: currency });
            } else if (currentType === 'meals') {
                if (!Array.isArray(itinerary[currentDayIndex].meals)) itinerary[currentDayIndex].meals = [];
                const adults = parseInt(document.getElementById('pax-adults')?.value || 1);
                itinerary[currentDayIndex].meals.push({ name: `[${item.type}] ${item.name}`, price: item.price, quantity: adults, currency: currency });
            }
            inventoryModal.hide();
            renderBuilder();
        }

        window.updateField = (idx, f, v) => { itinerary[idx][f] = v; calculateDynamicTotal(); };
        window.updateNestedField = (idx, p, f, v) => { if (!itinerary[idx][p]) itinerary[idx][p] = {}; itinerary[idx][p][f] = v; calculateDynamicTotal(); };
        window.updateListItem = (dI, t, iI, f, v) => {
            const k = t === 'transports' ? 'transport' : (t === 'tickets' ? 'places' : t);
            if (!Array.isArray(itinerary[dI][k])) itinerary[dI][k] = [];
            itinerary[dI][k][iI][f] = v;
            calculateDynamicTotal();
        };
        window.updateListItemNested = (dI, t, iI, p, f, v) => {
            const k = t === 'tickets' ? 'places' : (t === 'transports' ? 'transport' : t);
            if (!Array.isArray(itinerary[dI][k])) itinerary[dI][k] = [];
            if (!itinerary[dI][k][iI][p]) itinerary[dI][k][iI][p] = { currency: currency };
            itinerary[dI][k][iI][p][f] = v;
            calculateDynamicTotal();
        };

        window.addItem = (dI, t) => {
            const day = itinerary[dI];
            if (!day) return;

            if (t === 'hotels') {
                const hotels = ensureArray(day.hotels);
                hotels.push({ name: '', type: '', price_per_night: 0, currency: currency, quantity: 1, add_on_price: 0 });
                day.hotels = hotels;
            } else {
                const k = t === 'tickets' ? 'places' : (t === 'transports' ? 'transport' : t);
                if (!Array.isArray(day[k])) day[k] = [];
                if (t === 'activities' || t === 'tickets') {
                    const adults = parseInt(document.getElementById('pax-adults')?.value || 0);
                    day[k].push({
                        name: '',
                        description: '',
                        entry_ticket: {
                            adult_price: 0, adult_qty: adults,
                            child_2_6_price: 0, child_2_6_qty: 0,
                            child_6_11_price: 0, child_6_11_qty: 0,
                            currency: currency
                        }
                    });
                } else if (t === 'spots') {
                    day[k].push({ name: '', description: '', hours: 0, price_per_hour: 0 });
                } else if (t === 'meals') {
                    const adults = parseInt(document.getElementById('pax-adults')?.value || 1);
                    day[k].push({ name: '', price: 0, quantity: adults, currency: currency });
                } else {
                    day[k].push({ name: '', price: 0, currency: currency });
                }
            }
            renderBuilder();
        };
        window.removeItem = (dI, t, iI) => {
            const k = t === 'transports' ? 'transport' : (t === 'tickets' ? 'places' : t);
            itinerary[dI][k].splice(iI, 1);
            renderBuilder();
        };
        window.addDay = () => { itinerary.push({ day: itinerary.length + 1, title: 'Day ' + (itinerary.length + 1), activities: [], spots: [], transport: [], meals: [], hotels: [] }); renderBuilder(); };
        window.removeDay = (idx) => { if (confirm('Delete day?')) { itinerary.splice(idx, 1); renderBuilder(); } };

        window.copyPdfLink = () => { navigator.clipboard.writeText("{{ route('admin.group-itineraries.pdf', data_get($itinerary, 'id')) }}?public=1").then(() => alert('Customer PDF Link copied!')); };
        window.shareCustomerQuote = () => {
            const title = document.getElementById('proposal-title').value;
            const clientName = document.getElementById('client-name').value;
            const adults = document.getElementById('pax-adults').value;
            const c1 = document.getElementById('pax-child-small').value;
            const c2 = document.getElementById('pax-child-large').value;
            
            const total = document.getElementById('preview-grand-total').innerText;
            const perPax = document.getElementById('preview-perpax-total').innerText;
            const perPaxVal = parseFloat(perPax.replace(/[^0-9.]/g, '') || 0);
            const currencyStr = "{{ data_get($itinerary, 'currency', 'INR') }}";

            // Child Rates (25% and 50% of adult rate)
            const childRateSmall = (perPaxVal * 0.25).toFixed(2);
            const childRateLarge = (perPaxVal * 0.50).toFixed(2);

            let text = `*📋 BOOKING PROPOSAL: ${title.toUpperCase()}*\n`;
            text += `🏢 *Company:* Tourliz\n`;
            text += `👤 *Guest:* ${clientName}\n`;
            text += `👥 *Pax:* ${adults} Adults`;
            if(c1 > 0) text += `, ${c1} Child(2-6y)`;
            if(c2 > 0) text += `, ${c2} Child(6-11y)`;
            text += `\n\n`;
            
            text += `*Full Itinerary Summary:*\n`;
            itinerary.forEach(day => {
                text += `*Day ${day.day}: ${day.title || ''}*\n`;
                const spots = [...ensureArray(day.spots), ...ensureArray(day.activities), ...ensureArray(day.places)];
                if(spots.length > 0) {
                    spots.forEach(s => {
                        const name = s.name || s.attraction_name;
                        if(name) text += `📍 ${name}\n`;
                    });
                }
                text += `\n`;
            });

            text += `*Pricing Details:*\n`;
            text += `Total Final Quote: ${total}\n`;
            text += `Rate Per Adult: ${perPax}\n`;
            if(c1 > 0) text += `Rate Child (2-6y): ${currencyStr} ${childRateSmall}\n`;
            if(c2 > 0) text += `Rate Child (6-11y): ${currencyStr} ${childRateLarge}\n`;

            text += `\n🔗 *Full Itinerary Link:* {{ route('admin.group-itineraries.pdf', data_get($itinerary, 'id')) }}?public=1\n`;
            text += `\n_Thank you for choosing Tourliz Team!_ 🙏✨\n`;
            
            window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
        };

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
            const pickup = (dayIndex > 0 && ensureArray(itinerary[dayIndex-1].hotels).length > 0)
                           ? itinerary[dayIndex-1].hotels[0].name
                           : (ensureArray(day.hotels).length > 0 ? day.hotels[0].name : 'Hotel/Location');

            let text = `*🚖 Tourliz DRIVER JOB SHEET*\n`;
            text += `📅 *Date:* ${dateStr}\n`;
            text += `👤 *Guest:* ${clientName}\n`;
            text += `📍 *Program:* ${day.title || 'Day ' + day.day}\n`;
            text += `🏨 *Pickup Point:* ${pickup}\n\n`;

            // Transport Info
            const transports = ensureArray(day.transport || day.transports);
            if (transports.length > 0) {
               text += `*Vehicle:* ${transports.map(t => t.type || t.name).join(', ')}\n`;
            }

            text += `\n*Day Program:*\n`;
            // Spots & Activities
            const spots = [...ensureArray(day.spots), ...ensureArray(day.activities), ...ensureArray(day.places)];
            if (spots.length > 0) {
                 spots.forEach(s => {
                     const name = s.name || s.attraction_name;
                     if (name) text += `📍 ${name}\n`;
                 });
            } else {
                text += "As per guest instructions.\n";
            }
            
            text += `\n_Generated by Tourliz Team_ 🚀\n`;

            window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
        };

        window.shareHotelRequest = (dayIndex, hotelIndex) => {
            const day = itinerary[dayIndex];
            const h = ensureArray(day.hotels)[hotelIndex];
            if(!h) return;

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

            let text = `*🏨 HOTEL BOOKING REQUEST* - *Tourliz*\n`;
            text += `🏢 *Hotel:* *${h.name}* \n`;
            text += `👤 *Guest:* ${clientName}\n`;
            text += `📅 *Check-In:* ${dateStr}\n`;
            text += `🛌 *Rooms:* ${h.quantity || 1} x ${h.type || 'Standard'}\n`;
            text += `👥 *Pax:* ${adults} Adults`;
            if(c1 > 0 || c2 > 0) text += ` + ${parseInt(c1)+parseInt(c2)} Kids`;
            text += `\n`;
            
            // Net Price for vendor confirmation
            const cost = (parseFloat(h.price_per_night) + parseFloat(h.add_on_price)) * parseFloat(h.quantity || 1);
            const currencyStr = "{{ data_get($itinerary, 'currency', 'INR') }}";
            
            text += `💰 *Net Rate:* ${currencyStr} ${cost.toFixed(2)}\n`;
            text += `\n_Please confirm availability._\n_Regards, Tourliz Team_ 🙏`;

            const number = prompt("Enter Hotel Reservations WhatsApp Number:", "");
            if (number) {
                 window.open(`https://api.whatsapp.com/send?phone=${number}&text=${encodeURIComponent(text)}`, '_blank');
            }
        };

        // Modal Search Logic
        document.getElementById('inventorySearch').addEventListener('input', debounce(fetchItems, 300));

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        document.getElementById('saveBtn').addEventListener('click', function () {
            // Collect involved vendors
            const selectedVendors = [];
            $('.vendor-cb:checked').each(function() {
                selectedVendors.push($(this).val());
            });

            // For backward compatibility and DB constraints, use the first selected as the primary supplier_id
            const primaryVendorId = selectedVendors.length > 0 ? selectedVendors[0] : '';
            document.getElementById('formSupplier').value = primaryVendorId;
            document.getElementById('formInvolvedVendors').value = JSON.stringify(selectedVendors);

            document.getElementById('itineraryData').value = JSON.stringify(itinerary);
            document.getElementById('formTitle').value = document.getElementById('proposal-title').value;
            document.getElementById('formClient').value = document.getElementById('client-name').value;
            document.getElementById('formAssignedUser').value = document.getElementById('assigned-user').value;
            document.getElementById('formEmail').value = document.getElementById('client-email').value;
            document.getElementById('formPhone').value = document.getElementById('client-phone').value;
            document.getElementById('formPhoneSecondary').value = document.getElementById('client-phone-secondary').value;
            document.getElementById('formLeadSource').value = document.getElementById('lead-source').value;
            document.getElementById('formMarkup').value = document.getElementById('markup-percentage').value;
            document.getElementById('formAdults').value = document.getElementById('pax-adults').value;
            document.getElementById('formChildSmall').value = document.getElementById('pax-child-small').value;
            document.getElementById('formChildLarge').value = document.getElementById('pax-child-large').value;

            const pStatus = document.getElementById('payment-status');
            document.getElementById('formPaymentStatus').value = pStatus ? pStatus.value : 'pending';

            document.getElementById('formPaymentReceived').value = document.getElementById('payment-received').value || 0;
            document.getElementById('formPaymentDetails').value = document.getElementById('payment-details').value;
            document.getElementById('formFollowupStatus').value = document.getElementById('followup-status').value;
            document.getElementById('formNextFollowup').value = document.getElementById('next-followup').value;
            document.getElementById('formArrivalDate').value = document.getElementById('arrival-date').value;
            document.getElementById('formDuration').value = document.getElementById('trip-duration').value;
            document.getElementById('formNotes').value = document.getElementById('proposal-notes').value;
            document.getElementById('saveForm').submit();
        });

        // --- Expense & Vendor Management ---
        let allSuppliers = []; // Added global array to support sharing logic
        document.addEventListener('DOMContentLoaded', function () {
            loadExpenses();
            // loadSuppliers(); // Moved to loadExpenses success callback
            $(document).on('change', '#expense-category', function() {
                filterSuppliersByCategory(this.value);
            });
        });

        function loadSuppliers() {
            $.get("{{ route('admin.suppliers.index') }}", { destination_id: "{{ data_get($itinerary, 'destination_id') }}" }, function (data) {
                allSuppliers = data; // Store globally for sharing logic
                
                // Initialize Filtered Dropdown
                const currentCat = $('#expense-category').val();
                filterSuppliersByCategory(currentCat);

                // Populate Expense modal select (Initial load, though filter overrides)
                const select = $('#expense-supplier-id');
                select.find('option:not(:first)').remove();
                
                // Populate Main proposal checkboxes
                const checkboxContainer = $('#vendor-checkboxes');
                checkboxContainer.empty();

                const currentVendorId = parseInt("{{ data_get($itinerary, 'supplier_id', 0) }}");

                data.forEach(s => {
                    const isChecked = ((s.id == currentVendorId) || (currentExpenses && currentExpenses.some(e => e.supplier_id == s.id))) ? 'checked' : '';

                    // select.append(`<option value="${s.id}">${s.name} (${s.type})</option>`); // This line is now handled by filterSuppliersByCategory

                    const cbHtml = `
                        <div class="form-check small mb-1">
                            <input class="form-check-input vendor-cb" type="checkbox" value="${s.id}" id="vcb-${s.id}" ${isChecked}>
                            <label class="form-check-label text-truncate d-block" for="vcb-${s.id}" title="${s.name}">
                                ${s.name} <span class="text-muted" style="font-size: 0.6rem;">(${s.type})</span>
                            </label>
                        </div>
                    `;
                    checkboxContainer.append(cbHtml);
                });

                if (data.length === 0) {
                    checkboxContainer.html('<div class="text-muted small py-1">No vendors found</div>');
                }
            });
        }

        function filterSuppliersByCategory(category) {
            const expenseSelect = $('#expense-supplier-id');
            const currentVal = expenseSelect.val(); 
            expenseSelect.find('option:not(:first)').remove(); // Keep default option

            if(!allSuppliers || allSuppliers.length === 0) return;

            const filtered = allSuppliers.filter(s => {
                const sType = (s.type || '').toLowerCase();
                const cat = (category || '').toLowerCase();
                
                if (cat === 'hotel') return sType === 'hotel';
                if (cat === 'transport') return sType === 'transport' || sType === 'driver' || sType === 'taxi';
                if (cat === 'activity') return sType === 'activity' || sType === 'attraction';
                if (cat === 'meal') return sType === 'restaurant' || sType === 'meal';
                if (cat === 'agent') return sType === 'agent';
                
                // For 'Other', exclude the above or just show all? 
                // Let's implement flexible matching for 'Other' or just fallback to true
                if (cat === 'other') return true; 

                return false; 
            });
            
            // If filtered is empty, maybe show all or show none? 
            // Stick to strict like B2B logic I wrote
            (filtered.length > 0 ? filtered : allSuppliers).forEach(s => {
                expenseSelect.append(`<option value="${s.id}">${s.name} (${s.type})</option>`);
            });
            
            if(currentVal) expenseSelect.val(currentVal);
        }

        function quickSaveSupplier() {
            const formData = $('#quickSupplierForm').serialize();
            $.post("{{ route('admin.suppliers.store') }}", formData, function (res) {
                if (res.success) {
                    $('#newSupplierModal').modal('hide');
                    $('#expenseModal').modal('show');
                    $('#quickSupplierForm')[0].reset();
                    loadSuppliers();
                }
            });
        }



        function downloadVendorPdf(id) {
            window.open(`/admin/expenses/${id}/pdf-vendor`, '_blank');
        }

        function loadExpenses() {
            $.get("{{ route('admin.expenses.index') }}", {
                itinerary_id: "{{ data_get($itinerary, 'id') }}",
                itinerary_type: 'group'
            }, function (data) {
                currentExpenses = data; // Store for Modal

                const tbody = $('#expense-table tbody');
                tbody.empty();
                let total = 0;

                data.forEach(exp => {
                    total += parseFloat(exp.amount);
                    const vendorName = exp.supplier ? `<br><small class="text-primary fw-bold">${exp.supplier.name}</small>` : '';

                    let actions = '';
                    if (exp.supplier) {
                        actions = `
                                                                                                        <button class="btn btn-link text-success p-0 me-2" onclick="shareVendorWhatsapp(${exp.id})" title="WhatsApp Vendor">
                                                                                                            <i class="bi bi-whatsapp"></i>
                                                                                                        </button>
                                                                                                        <button class="btn btn-link text-danger p-0 me-2" onclick="downloadVendorPdf(${exp.id})" title="PDF Voucher">
                                                                                                            <i class="bi bi-file-pdf"></i>
                                                                                                        </button>
                                                                                                    `;
                    }

                    tbody.append(`
                                                                                                                <tr>
                                                                                                                    <td>
                                                                                                                        <div class="fw-bold" style="font-size: 0.75rem;">${exp.category}${vendorName}</div>
                                                                                                                        <div class="text-muted" style="font-size: 0.65rem;">${exp.description || ''}</div>
                                                                                                                    </td>
                                                                                                                    <td class="text-end fw-bold text-danger">-${currency} ${parseFloat(exp.amount).toFixed(2)}</td>
                                                                                                                    <td class="text-end">
                                                                                                                        ${actions}
                                                                                                                        <button class="btn btn-link text-muted p-0" onclick="deleteExpense(${exp.id})" title="Remove">
                                                                                                                            <i class="bi bi-trash"></i>
                                                                                                                        </button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                            `);
                });

                if (data.length === 0) {
                    tbody.append('<tr><td colspan="3" class="text-center text-muted py-2 small">No costs recorded</td></tr>');
                }

                updateFinancialSummary(total);
                loadSuppliers();
            });
        }

        function saveExpense() {
            const formData = $('#expenseForm').serialize();
            $.post("{{ route('admin.expenses.store') }}", formData, function (res) {
                if (res.success) {
                    $('#expenseModal').modal('hide');
                    $('#expenseForm')[0].reset();
                    loadExpenses();
                }
            }).fail(function (xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Check inputs'));
            });
        }

        function deleteExpense(id) {
            if (!confirm('Remove this cost entry?')) return;
            $.ajax({
                url: `/admin/expenses/${id}`,
                type: 'DELETE',
                success: function (res) {
                    loadExpenses();
                }
            });
        }

        function updateFinancialSummary(actualCost) {
            const totalQuoted = parseFloat("{{ data_get($itinerary, 'total_price', 0) }}");
            const profit = totalQuoted - actualCost;
            const currencyStr = "{{ data_get($itinerary, 'currency') }} ";

            $('#total-actual-cost').text(currencyStr + actualCost.toFixed(2));
            $('#summary-actual-cost').text(currencyStr + actualCost.toFixed(2));
            $('#actual-profit').text(currencyStr + profit.toFixed(2));

            const profitEl = $('#actual-profit');
            const marginEl = $('#actual-margin');
            const margin = totalQuoted > 0 ? (profit / totalQuoted) * 100 : 0;

            marginEl.text(margin.toFixed(2) + '%');

            if (profit >= 0) {
                profitEl.removeClass('text-danger text-dark').addClass('text-success');
                marginEl.removeClass('text-danger').addClass('text-success');
            } else {
                profitEl.removeClass('text-success text-dark').addClass('text-danger');
                marginEl.removeClass('text-success').addClass('text-danger');
            }
        }
    </script>
@endpush

<!-- Vendor Share Modal -->
<div class="modal fade" id="vendorShareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share with Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="vendor-list-container" class="list-group list-group-flush">
                    <!-- Populated by JS -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentExpenses = [];

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
            const refId = "{{ data_get($itinerary, 'itinerary_id') ?? data_get($itinerary, 'id') }}";

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
                    if(d.hotels) d.hotels.forEach(h => {
                        rQty += parseInt(h.quantity || 0);
                        if(h.type && !rType.includes(h.type)) rType.push(h.type);
                        // Calculate total cost strictly for this vendor
                        if (vendor && h.name && h.name.toLowerCase().includes(vendor.name.toLowerCase())) {
                            totalCost += (parseFloat(h.price_per_night || 0) + parseFloat(h.add_on_price || 0)) * parseFloat(h.quantity || 1);
                        }
                    });
                });

                text += `*Rooms:* ${rQty} ${rType.join(', ') || 'Standard'}\n`;
                text += `*Total Pax:* ${adults} Adults`;
                if(parseInt(c1)+parseInt(c2) > 0) text += `, ${parseInt(c1)+parseInt(c2)} Kids`;
                text += `\n`;
                
                if (totalCost > 0) {
                    text += `*Total Amount:* {{ data_get($itinerary, 'currency', 'INR') }} ${totalCost.toFixed(2)}\n`;
                }
                
                text += `*Auth by:* {{ Auth::user()->name }}\n`;

                window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
            } else if (vendorType.toLowerCase().includes('transport') || vendorType.toLowerCase().includes('driver') || vendorType.toLowerCase().includes('taxi')) {
                // Template C: Transport/Driver
                let text = `*TRANSPORT REQUEST*\n`;
                text += `*Ref ID:* #${refId}\n`;
                text += `*Total Pax:* ${adults} Adults`;
                if(parseInt(c1)+parseInt(c2) > 0) text += `, ${parseInt(c1)+parseInt(c2)} Kids`;
                text += `\n\n*ITINERARY DETAILS:*\n`;

                itinerary.forEach((d) => {
                    text += `*Day ${d.day}:* ${d.program || 'Itinerary flow'}\n`;
                    let hName = 'Own Arrangement';
                    if(d.hotels && d.hotels.length > 0 && d.hotels[0].name) {
                        hName = d.hotels[0].name;
                    }
                    text += `📍 *Stay:* ${hName}\n\n`;
                });

                text += `*Auth by:* {{ Auth::user()->name }}\n`;
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
                if(parseInt(c1)+parseInt(c2) > 0) text += `, ${parseInt(c1)+parseInt(c2)} Kids`;
                text += `\n`;

                if (specificExp) {
                     // Individual Share Mode
                     text += `*Activity:* ${specificExp.description}\n`;
                     // Date
                     const expDate = specificExp.expense_date ? (new Date(specificExp.expense_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })) : '';
                     if(expDate) text += `*Date:* ${expDate}\n`;
                     
                     text += `*Total Amount:* {{ data_get($itinerary, 'currency', 'INR') }} ${parseFloat(specificExp.amount || 0).toFixed(2)}\n`;
                } else {
                    // Aggregate Mode
                    let actList = [];
                    let totalCost = 0;
                    itinerary.forEach((d) => {
                         // Check Activities
                         if (d.activities) d.activities.forEach(a => { 
                             if(a.name) {
                                 actList.push(a.name); 
                                 if (vendor && a.name.toLowerCase().includes(vendor.name.toLowerCase())) {
                                      totalCost += parseFloat(a.price || 0) * (parseInt(adults) + parseInt(c1)+parseInt(c2));
                                 }
                             }
                         });
                         // Check Spots
                         if (d.spots) d.spots.forEach(s => { 
                             if(s.name) actList.push(s.name); 
                             if (vendor && s.name && s.name.toLowerCase().includes(vendor.name.toLowerCase())) {
                                  totalCost += parseFloat(s.price || 0);
                             }
                         });
                    });

                    if (actList.length > 0) text += `*Activity:* ${actList.join(', ')}\n`;
                    if (totalCost > 0) text += `*Total Amount:* {{ data_get($itinerary, 'currency', 'INR') }} ${totalCost.toFixed(2)}\n`;
                }

                text += `*Auth by:* {{ Auth::user()->name }}\n`;
                window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
            } else {
                // General Vendor Share
                let text = `*AVAILABILITY CHECK*\n`;
                text += `Ref ID: #${refId}\n`;
                text += `Guest: ${clientName}\n`;
                text += `Arrival: ${arrivalDate}\n`;
                text += `Pax: ${adults} Adults + ${parseInt(c1)+parseInt(c2)} Kids\n`;
                text += `\nRep: {{ Auth::user()->name }}`;
                window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
            }
         }
    };

    function openVendorShareModal() {
        const container = document.getElementById('vendor-list-container');
        container.innerHTML = '';
        
        // Collect vendors from expenses
        const expenseVendors = currentExpenses.filter(e => e.supplier).map(e => ({
            id: e.supplier.id,
            name: e.supplier.name,
            type: e.supplier.type,
            category: e.category,
            description: e.description,
            expenseId: e.id
        }));

        // Collect vendors from checkboxes
        const selectedVendors = [];
        $('.vendor-cb:checked').each(function() {
            const id = $(this).val();
            const label = $(this).next('label').text().trim();
            // Basic parsing of the label text "Name (Type)"
            const name = label.split(' (')[0];
            const typeMatch = label.match(/\(([^)]+)\)/);
            const type = typeMatch ? typeMatch[1] : '';

            // Avoid duplicates if already in expenseVendors
            if (!expenseVendors.some(ev => ev.id == id)) {
                selectedVendors.push({ id, name, type, category: 'General', description: 'Selected for Proposal' });
            }
        });

        const allVendors = [...expenseVendors, ...selectedVendors];

        if (allVendors.length === 0) {
            container.innerHTML = '<div class="p-4 text-center text-muted">No vendors selected. Choose vendors in the left sidebar or add expenses.</div>';
        } else {
            allVendors.forEach(vendor => {
                const item = document.createElement('div');
                item.className = 'list-group-item d-flex justify-content-between align-items-center p-3';
                item.innerHTML = `
                    <div style="flex:1">
                        <div class="fw-bold">${vendor.name} <span class="badge bg-light text-dark border ms-1">${vendor.category || vendor.type}</span></div>
                        <div class="text-muted small">${vendor.description || ''}</div>
                    </div>
                    <div class="d-flex gap-2">
                         <button class="btn btn-danger btn-sm" onclick="downloadVendorPdf(${vendor.expenseId})" title="PDF Voucher" ${!vendor.expenseId ? 'disabled' : ''}>
                             <i class="bi bi-file-earmark-pdf"></i>
                         </button>
                         <button class="btn btn-success btn-sm" onclick="shareVendorWhatsapp(${vendor.expenseId || 0}, ${vendor.id})" title="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </button>
                    </div>
                `;
                container.appendChild(item);
            });
        }

        new bootstrap.Modal(document.getElementById('vendorShareModal')).show();
    }
</script>

<!-- Expense Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title fs-6">Add Actual Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="expenseForm">
                    <input type="hidden" name="itinerary_id" value="{{ data_get($itinerary, 'id') }}">
                    <input type="hidden" name="itinerary_type" value="group">
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">Category</label>
                        <select name="category" id="expense-category" class="form-select form-select-sm" required>
                            <option value="Hotel">Hotel Payout</option>
                            <option value="Transport">Transport / Driver</option>
                            <option value="Activity">Entrance / Activity</option>
                            <option value="Meal">Meal Payment</option>
                            <option value="Agent">Agent Commission</option>
                            <option value="Other">Other Operational</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">Amount ({{ data_get($itinerary, 'currency') }})</label>
                        <input type="number" step="0.01" name="amount" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">Supplier <a href="#"
                                class="float-end text-decoration-none" data-bs-toggle="modal"
                                data-bs-target="#newSupplierModal">+ New</a></label>
                        <select name="supplier_id" id="expense-supplier-id" class="form-select form-select-sm">
                            <option value="">Select Partner (Optional)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">Expense Date</label>
                        <input type="date" name="expense_date" class="form-control form-control-sm"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted mb-1">Description / Notes</label>
                        <input type="text" name="description" class="form-control form-control-sm"
                            placeholder="e.g. Driver payout ref #123">
                    </div>
                </form>
            </div>
            <div class="modal-footer py-1">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="saveExpense()">Save Cost</button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Supplier Modal -->
<div class="modal fade" id="newSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title fs-6">Quick Add Supplier</h5>
            </div>
            <div class="modal-body">
                <form id="quickSupplierForm">
                    <div class="mb-2">
                        <label class="small text-muted mb-1">Supplier Name</label>
                        <input type="text" name="name" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="small text-muted mb-1">Type</label>
                        <select name="type" class="form-select form-select-sm" required>
                            <option value="Hotel">Hotel</option>
                            <option value="Transport">Transport</option>
                            <option value="Activity">Activity</option>
                            <option value="Agent">Agent</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-1">
                <button type="button" class="btn btn-sm btn-secondary"
                    onclick="$('#newSupplierModal').modal('hide'); $('#expenseModal').modal('show');">Back</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="quickSaveSupplier()">Save</button>
            </div>
        </div>
    </div>
</div>
