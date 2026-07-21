@extends('layouts.admin')

@section('title', 'Edit B2B Proposal')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-pencil-square me-2"></i>Edit B2B Proposal</h2>
                <p class="text-muted mb-0">Building itinerary for
                    <strong>{{ $itinerary['agency']['company_name'] ?? '' }}</strong>
                    ({{ $itinerary['place']['name'] ?? '' }})
                </p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-dark" onclick="openVendorShareModal()">
                    <i class="bi bi-people me-1"></i> Vendor
                </button>
                <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#shareModal">
                    <i class="bi bi-share me-1"></i> Share
                </button>
                <a href="{{ route('admin.b2b-itineraries.index') }}" class="btn btn-outline-secondary">
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
                    <h5 class="modal-title">Share Proposal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
                        <p class="mt-2 fw-bold mb-3">PDF Proposal</p>

                        <div class="row g-2 px-3">
                            <div class="col-6">
                                <a href="{{ route('admin.b2b-itineraries.pdf', $itinerary['id']) }}?public=1"
                                    class="btn btn-primary btn-sm w-100 py-2" onclick="return confirmPdfDownload(event)">
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
                                    <a href="{{ route('admin.b2b-itineraries.pdf', $itinerary['id']) }}"
                                        class="btn btn-outline-dark btn-sm w-100" onclick="return confirmPdfDownload(event)">
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
                            <i class="bi bi-whatsapp me-1"></i> Share as Text
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Sidebar: Settings -->
        <!-- Left Sidebar: Settings, Pricing, CRM Tabs -->
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 mb-4 bg-white rounded-3">
                <!-- Card Header with Navigation Tabs -->
                <div class="card-header border-bottom-0 bg-light p-2" style="border-radius: 8px 8px 0 0;">
                    <ul class="nav nav-pills nav-fill gap-1" id="sidebarTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link active py-2 d-flex flex-column align-items-center justify-content-center gap-1 border-0"
                                id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings-pane" type="button"
                                role="tab" aria-controls="settings-pane" aria-selected="true"
                                style="font-size: 0.7rem; border-radius: 6px; min-height: 52px; background: transparent;">
                                <i class="bi bi-gear-fill fs-6"></i>
                                <span class="fw-bold">Settings</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link py-2 d-flex flex-column align-items-center justify-content-center gap-1 border-0"
                                id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing-pane" type="button"
                                role="tab" aria-controls="pricing-pane" aria-selected="false"
                                style="font-size: 0.7rem; border-radius: 6px; min-height: 52px; background: transparent;">
                                <i class="bi bi-wallet2 fs-6"></i>
                                <span class="fw-bold">Pricing</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link py-2 d-flex flex-column align-items-center justify-content-center gap-1 border-0"
                                id="crm-tab" data-bs-toggle="tab" data-bs-target="#crm-pane" type="button" role="tab"
                                aria-controls="crm-pane" aria-selected="false"
                                style="font-size: 0.7rem; border-radius: 6px; min-height: 52px; background: transparent;">
                                <i class="bi bi-person-badge-fill fs-6"></i>
                                <span class="fw-bold">CRM</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Card Body with Tab Contents -->
                <div class="card-body p-3">
                    <div class="tab-content" id="sidebarTabContent">

                        <!-- TAB 1: SETTINGS -->
                        <div class="tab-pane fade show active" id="settings-pane" role="tabpanel"
                            aria-labelledby="settings-tab">
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Proposal Title</label>
                                <input type="text" id="proposal-title" class="form-control form-control-sm"
                                    value="{{ $itinerary['title'] }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Client Name</label>
                                <input type="text" id="client-name" class="form-control form-control-sm"
                                    value="{{ $itinerary['client_name'] }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Source Vendor</label>
                                <select id="supplier_id" class="form-select form-select-sm">
                                    <option value="">Select Vendor (Optional)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Additional Countries</label>
                                <div class="row g-1" style="max-height: 100px; overflow-y: auto;">
                                    @foreach($countries as $c)
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input country-ck" type="checkbox" value="{{ $c->id }}"
                                                    id="b2b_edit_country_{{ $c->id }}" {{ in_array($c->id, $itinerary->country_ids ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small"
                                                    for="b2b_edit_country_{{ $c->id }}">{{ $c->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label small text-muted mb-1">Arrival Date</label>
                                    <input type="date" id="arrival-date" class="form-control form-control-sm"
                                        value="{{ isset($itinerary['start_date']) ? \Carbon\Carbon::parse($itinerary['start_date'])->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small text-muted mb-1">Days</label>
                                    <input type="number" id="trip-duration" class="form-control form-control-sm"
                                        value="{{ $itinerary['duration_days'] }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Markup (%)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" id="markup-percentage" class="form-control"
                                        value="{{ $itinerary['markup_percentage'] }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="form-text small" style="font-size: 0.7rem;">Standard:
                                    {{ $itinerary['agency']['default_markup'] ?? 0 }}%
                                </div>
                            </div>

                            <div class="row g-2 mb-3 border p-2 rounded bg-light">
                                <div class="col-4">
                                    <label class="form-label small text-muted mb-1">Adults</label>
                                    <input type="number" id="pax-adults" class="form-control form-control-sm"
                                        value="{{ $itinerary['adults'] ?? 1 }}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small text-muted mb-1">Child 2-6</label>
                                    <input type="number" id="pax-child-small" class="form-control form-control-sm"
                                        value="{{ $itinerary['children_2_6'] ?? 0 }}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small text-muted mb-1">Child 6-11</label>
                                    <input type="number" id="pax-child-large" class="form-control form-control-sm"
                                        value="{{ $itinerary['children_6_11'] ?? 0 }}">
                                </div>
                                <div class="col-12 mt-1">
                                    <small class="text-info" style="font-size: 0.65rem;">Child 2-6: -75% | Child 6-11:
                                        -50%</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Involved Vendors</label>
                                <div id="vendor-checkboxes" class="border rounded p-2 bg-white mb-1"
                                    style="max-height: 120px; overflow-y: auto;">
                                    <div class="text-muted small py-1">Loading partners...</div>
                                </div>
                                <button type="button" class="btn btn-link btn-xs p-0 small text-decoration-none"
                                    onclick="loadSuppliers()">
                                    <i class="bi bi-arrow-clockwise"></i> Refresh List
                                </button>
                                <input type="hidden" id="supplier_id" value="{{ $itinerary['supplier_id'] ?? '' }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Internal Notes</label>
                                <textarea id="proposal-notes" class="form-control form-control-sm"
                                    rows="3">{{ $itinerary['notes'] }}</textarea>
                            </div>
                            <hr class="my-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-primary btn-sm" onclick="addDay()">Add Day</button>
                            </div>
                        </div>

                        <!-- TAB 2: PRICING -->
                        <div class="tab-pane fade" id="pricing-pane" role="tabpanel" aria-labelledby="pricing-tab">
                            <!-- Customer Quote -->
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;"><i
                                        class="bi bi-receipt me-1 text-info"></i> Customer Quote</h6>
                                <span class="badge bg-info text-white" style="font-size: 0.6rem;">Client-Facing</span>
                            </div>
                            <div class="mb-3">
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
                                    <span id="preview-perpax-label">Per Pax Estimate:</span>
                                    <span id="preview-perpax-total">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1 small text-primary">
                                    <span>Markup Cost (<span id="preview-markup-perc">0</span>%):</span>
                                    <span id="preview-markup">0.00</span>
                                </div>
                                <div class="bg-light p-3 rounded text-center my-3 border shadow-sm">
                                    <div class="small text-muted fw-bold text-uppercase"
                                        style="font-size: 0.7rem; letter-spacing: 0.5px;">TOTAL QUOTE</div>
                                    <div class="fs-4 fw-bold text-dark" id="preview-grand-total">0.00</div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.6rem;">* Updates in
                                        real-time. Save to finalize.</small>
                                </div>
                            </div>

                            <!-- Estimated Net Cost -->
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom mt-4">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;"><i
                                        class="bi bi-calculator me-1 text-primary"></i> Estimated Net Cost</h6>
                                <span class="badge bg-primary text-white" style="font-size: 0.6rem;">Internal</span>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">Hotel Cost</span>
                                    <span class="fw-bold" id="rt-cost-hotel">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">Transport Cost</span>
                                    <span class="fw-bold" id="rt-cost-transport">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">Activities/Tickets</span>
                                    <span class="fw-bold" id="rt-cost-tickets">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">Meals/Other</span>
                                    <span class="fw-bold" id="rt-cost-other">0.00</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between mb-3 small fw-bold">
                                    <span class="text-dark">Total Net Cost</span>
                                    <span class="text-dark" id="rt-total-net">0.00</span>
                                </div>

                                <div class="alert alert-warning py-1 px-2 small mb-2 d-none" id="rt-warning-box">
                                    <i class="bi bi-exclamation-triangle me-1"></i> <span id="rt-warning-msg">Missing
                                        prices!</span>
                                </div>

                                <div class="bg-light p-2 rounded border">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted">Gross Profit</span>
                                        <span class="fw-bold text-success" id="rt-gross-profit">0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Net Margin</span>
                                        <span class="fw-bold text-success" id="rt-net-margin">0%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Net Profitability -->
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom mt-4">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;"><i
                                        class="bi bi-graph-up-arrow me-1 text-success"></i> Net Profitability</h6>
                                <span class="badge bg-dark text-white" style="font-size: 0.6rem;">Margin Analysis</span>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span>Agency Total:</span>
                                    <span id="summary-quoted-total">{{ $itinerary['currency'] ?? 'USD' }}
                                        {{ number_format($itinerary['total_price'] ?? 0, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1 small text-danger">
                                    <span>Actual Costs:</span>
                                    <span id="summary-actual-cost">{{ $itinerary['currency'] ?? 'USD' }} 0.00</span>
                                </div>
                                <div class="bg-dark text-white p-3 rounded text-center my-3 shadow-sm">
                                    <div class="small text-white-50 fw-bold text-uppercase"
                                        style="font-size: 0.7rem; letter-spacing: 0.5px;">NET PROFIT</div>
                                    <div class="fs-4 fw-bold text-success" id="actual-profit">
                                        {{ $itinerary['currency'] ?? 'USD' }} 0.00
                                    </div>
                                    <div
                                        class="d-flex justify-content-between px-2 mt-2 border-top border-secondary pt-2 small text-white-50">
                                        <span>Profit Margin:</span>
                                        <span id="actual-margin" class="fw-bold text-white">0.00%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: CRM -->
                        <div class="tab-pane fade" id="crm-pane" role="tabpanel" aria-labelledby="crm-tab">
                            <!-- Lead Status -->
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;"><i
                                        class="bi bi-funnel me-1 text-primary"></i> Lead & Followup</h6>
                            </div>
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">Lead Stage</label>
                                    <select id="followup-status" class="form-select form-select-sm">
                                        <option value="leads" {{ ($itinerary['followup_status'] ?? 'leads') == 'leads' ? 'selected' : '' }}>New Lead (Default)</option>
                                        <option value="waiting" {{ ($itinerary['followup_status'] ?? '') == 'waiting' ? 'selected' : '' }}>Waiting for Reply</option>
                                        <option value="interested" {{ ($itinerary['followup_status'] ?? '') == 'interested' ? 'selected' : '' }}>Interested / Hot</option>
                                        <option value="converted" {{ ($itinerary['followup_status'] ?? '') == 'converted' ? 'selected' : '' }}>Converted / Booking</option>
                                        <option value="not_interested" {{ ($itinerary['followup_status'] ?? '') == 'not_interested' ? 'selected' : '' }}>Not Interested</option>
                                        <option value="dead" {{ ($itinerary['followup_status'] ?? '') == 'dead' ? 'selected' : '' }}>Dead / Lost</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">Proposal Status</label>
                                    <select id="proposal-lifecycle" class="form-select form-select-sm">
                                        <option value="draft" {{ ($itinerary['status'] ?? '') == 'draft' ? 'selected' : '' }}>
                                            Draft</option>
                                        <option value="proposed" {{ ($itinerary['status'] ?? '') == 'proposed' ? 'selected' : '' }}>Proposed to Agent</option>
                                        <option value="confirmed" {{ ($itinerary['status'] ?? '') == 'confirmed' ? 'selected' : '' }}>Confirmed / Booking</option>
                                        <option value="cancelled" {{ ($itinerary['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small text-muted mb-1">Next Followup Date</label>
                                    <input type="date" id="next-followup" class="form-control form-control-sm"
                                        value="{{ isset($itinerary['next_followup_date']) ? \Carbon\Carbon::parse($itinerary['next_followup_date'])->format('Y-m-d') : '' }}">
                                </div>
                                @if(isset($itinerary['followed_up_at']))
                                    <div class="mt-2 small text-muted" style="font-size: 0.7rem;">
                                        <i class="bi bi-clock-history me-1"></i>Last action:
                                        {{ \Carbon\Carbon::parse($itinerary['followed_up_at'])->diffForHumans() }}
                                    </div>
                                @endif
                            </div>

                            <!-- Payment Tracking -->
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom mt-4">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;"><i
                                        class="bi bi-cash-coin me-1 text-success"></i> Payment & Collection</h6>
                            </div>
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">Status</label>
                                    <select id="payment-status" class="form-select form-select-sm">
                                        <option value="pending" {{ ($itinerary['payment_status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="partially_paid" {{ ($itinerary['payment_status'] ?? '') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                                        <option value="paid" {{ ($itinerary['payment_status'] ?? '') == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                                        <option value="cancelled" {{ ($itinerary['payment_status'] ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">Amount Received</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">{{ $itinerary['currency'] ?? 'USD' }}</span>
                                        <input type="number" id="payment-received" class="form-control"
                                            value="{{ $itinerary['total_amount_received'] ?? 0 }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">Collection Progress</label>
                                    <div class="border rounded p-2 bg-light shadow-xs">
                                        @php $balance = ($itinerary['total_price'] ?? 0) - ($itinerary['total_amount_received'] ?? 0) @endphp
                                        <div class="progress mb-2" style="height: 8px;">
                                            @php $percent = ($itinerary['total_price'] ?? 0) > 0 ? (($itinerary['total_amount_received'] ?? 0) / $itinerary['total_price']) * 100 : 0 @endphp
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $percent }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between small" style="font-size: 0.75rem;">
                                            <span class="text-muted">Collected: {{ number_format($percent, 0) }}%</span>
                                            <span class="fw-bold {{ $balance <= 0 ? 'text-success' : 'text-danger' }}">
                                                Bal: {{ $itinerary['currency'] ?? 'USD' }} {{ number_format($balance, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small text-muted mb-1">Payment Notes</label>
                                    <textarea id="payment-details" class="form-control form-control-sm"
                                        rows="2">{{ $itinerary['payment_details'] ?? '' }}</textarea>
                                </div>
                            </div>

                            <!-- Actual Expenses -->
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom mt-4">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;"><i
                                        class="bi bi-wallet2 me-1 text-danger"></i> Actual Expenses</h6>
                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2 fw-bold"
                                    style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#expenseModal">
                                    <i class="bi bi-plus-lg me-1"></i>Add
                                </button>
                            </div>
                            <div>
                                <div class="table-responsive border rounded bg-white mb-2"
                                    style="max-height: 200px; overflow-y: auto;">
                                    <table class="table table-sm table-hover mb-0" id="expense-table"
                                        style="font-size: 0.75rem;">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th>Category</th>
                                                <th class="text-end">Amount</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>{{-- Loaded via JS --}}</tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between small fw-bold p-2 bg-light border rounded">
                                    <span>Total Actual Cost:</span>
                                    <span id="total-actual-cost">{{ $itinerary['currency'] ?? 'USD' }} 0.00</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content: Builder -->
        <div class="col-lg-9">
            <div id="itinerary-builder">
                <!-- Builder Rendered by JS -->
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-outline-primary" onclick="addDay()">
                    <i class="bi bi-plus-circle me-2"></i>Add Day {{ count($itinerary['itinerary'] ?? []) + 1 }}
                </button>
            </div>
        </div>
    </div>

    <!-- Save Form -->
    <form id="saveForm" action="{{ route('admin.b2b-itineraries.update', $itinerary['id']) }}" method="POST"
        style="display:none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="itinerary" id="itineraryData">
        <input type="hidden" name="supplier_id" id="formSupplier">
        <input type="hidden" name="markup_percentage" id="formMarkup">
        <input type="hidden" name="title" id="formTitle">
        <input type="hidden" name="client_name" id="formClient">
        <input type="hidden" name="notes" id="formNotes">

        <!-- Pax & Payment -->
        <input type="hidden" name="adults" id="formAdults">
        <input type="hidden" name="children_2_6" id="formChildSmall">
        <input type="hidden" name="children_6_11" id="formChildLarge">
        <input type="hidden" name="payment_status" id="formPaymentStatus">
        <input type="hidden" name="total_amount_received" id="formPaymentReceived">
        <input type="hidden" name="payment_details" id="formPaymentDetails">

        <!-- Followup & Lifecycle -->
        <input type="hidden" name="followup_status" id="formFollowupStatus">
        <input type="hidden" name="next_followup_date" id="formNextFollowup">
        <input type="hidden" name="status" id="formLifecycle">
        <input type="hidden" name="involved_vendors" id="formInvolvedVendors">
        <input type="hidden" name="country_ids" id="formCountryIds">

        <input type="hidden" name="start_date" id="formArrivalDate">
        <input type="hidden" name="duration_days" id="formDuration">
    </form>

    <!-- Shared Modal for Inventory Selection -->
    <div class="modal fade" id="inventoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inventoryModalTitle">Select Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="inventorySearchBox" class="mb-3 d-flex gap-2">
                        <select id="inventoryCountrySelect" class="form-select" style="max-width: 180px;"
                            onchange="filterInventoryCities()">
                            <option value="" data-country-id="">All Countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->name }}" data-country-id="{{ $country->id }}" {{ (isset($itinerary->destination) && $itinerary->destination->name == $country->name) ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        <select id="inventoryDestinationId" class="form-select" style="max-width: 180px;">
                            <option value="">All Cities</option>
                            @foreach($destinations as $destination)
                                <option value="{{ $destination->id }}" data-country="{{ $destination->country }}">
                                    {{ $destination->city }}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" id="inventorySearch" class="form-control" placeholder="Search items...">
                    </div>
                    <div id="inventoryResults" class="list-group">
                        <!-- Results injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    @push('scripts')
        <script>
            // In    itial Data
            let itinerary = @json($itinerary['itinerary'] ?? []);
            const currency = "{{ $itinerary['currency'] ?? 'INR' }}";
            const destinationId = "{{ $itinerary['destination_id'] ?? '' }}";

            // Helper to ensure we always have an array
            window.ensureArray = function (arr) {
                return Array.isArray(arr) ? arr : [];
            }

            // --- Core Rendering ---
            window.renderBuilder = function () {
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
            window.safeFloat = function (val) {
                if (val === null || val === undefined || String(val).trim() === '') return 0;
                const n = parseFloat(val);
                return isNaN(n) ? 0 : n;
            }

            window.calculateDynamicTotal = function () {
                let hotels = 0, transport = 0, activities = 0, meals = 0;
                let rtHotel = 0, rtTransport = 0, rtTickets = 0, rtOther = 0;
                let missingPrices = false;

                itinerary.forEach(day => {
                    // 1. Hotels
                    ensureArray(day.hotels).forEach(h => {
                        const price = safeFloat(h.price_per_night);
                        const addon = safeFloat(h.add_on_price);
                        let qty = 1;
                        if (h.quantity !== undefined && h.quantity !== null && String(h.quantity).trim() !== '') {
                            qty = safeFloat(h.quantity);
                        }
                        const cost = (price + addon) * qty;
                        hotels += cost;
                        rtHotel += cost;
                        if (cost <= 0 && h.name) missingPrices = true;
                    });

                    // 2. Transport
                    const transports = day.transport || day.transports;
                    ensureArray(transports).forEach(t => {
                        const cost = safeFloat(t.price);
                        transport += cost;
                        rtTransport += cost;
                        if (cost <= 0 && (t.vehicle_type || t.name)) missingPrices = true;
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
                            if ((aq + csq + clq) === 0) itemCost += adultRate;
                        }
                        if (a.hours || a.price_per_hour) {
                            itemCost += (safeFloat(a.hours) * safeFloat(a.price_per_hour));
                        }
                        activities += itemCost;
                        rtTickets += itemCost; // Activities -> Tickets/Activities category
                        if (itemCost <= 0 && a.name) missingPrices = true;
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
                            if ((aq + csq + clq) === 0) itemCost += adultRate;
                        }
                        activities += itemCost;
                        rtTickets += itemCost;
                        if (itemCost <= 0 && (p.attraction_name || p.name)) missingPrices = true;
                    });

                    // Spots
                    ensureArray(day.spots).forEach(s => {
                        const cost = (safeFloat(s.hours) * safeFloat(s.price_per_hour));
                        activities += cost;
                        rtOther += cost; // Spots -> Other/Meals
                    });

                    // 4. Meals
                    ensureArray(day.meals).forEach(m => {
                        const price = safeFloat(m.price);
                        let qty = 1;
                        if (m.quantity !== undefined && m.quantity !== null && String(m.quantity).trim() !== '') {
                            qty = safeFloat(m.quantity);
                        }
                        const cost = price * qty;
                        meals += cost;
                        rtOther += cost;
                        if (cost <= 0 && m.name) missingPrices = true;
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

                // Update Preview (Standard)
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

                const paxLabel = document.getElementById('preview-perpax-label');
                let perPax = 0;
                if (c1 > 0 || c2 > 0) {
                    const weightedPax = (adults * 1.0) + (c1 * 0.25) + (c2 * 0.50);
                    perPax = weightedPax > 0 ? (grandTotal / weightedPax) : 0;
                    if (paxLabel) paxLabel.innerText = "Per Adult Estimate:";
                } else {
                    perPax = totalPax > 0 ? (grandTotal / totalPax) : 0;
                    if (paxLabel) paxLabel.innerText = "Per Pax Estimate:";
                }
                safeSet('preview-perpax-total', perPax);
                const summaryQuotedEl = document.getElementById('summary-quoted-total');
                if (summaryQuotedEl) summaryQuotedEl.innerText = currency + ' ' + grandTotal.toFixed(2);

                // --- Real-Time Costing Card Updates ---
                safeSet('rt-cost-hotel', rtHotel);
                safeSet('rt-cost-transport', rtTransport);
                safeSet('rt-cost-tickets', rtTickets);
                safeSet('rt-cost-other', rtOther);
                safeSet('rt-total-net', baseCost);

                const profit = grandTotal - baseCost;
                const margin = grandTotal > 0 ? (profit / grandTotal) * 100 : 0;
                safeSet('rt-gross-profit', profit);

                const rtNetMargin = document.getElementById('rt-net-margin');
                if (rtNetMargin) rtNetMargin.innerText = margin.toFixed(2) + '%';

                const rtWarningBox = document.getElementById('rt-warning-box');
                if (rtWarningBox) {
                    if (missingPrices) {
                        rtWarningBox.classList.remove('d-none');
                        document.getElementById('rt-warning-msg').innerText = "Warning: Some items have 0 cost!";
                    } else {
                        rtWarningBox.classList.add('d-none');
                    }
                }

                // Update Profit (Legacy/Actual Expense Check)
                const actualCostStr = document.getElementById('summary-actual-cost')?.innerText || '0.00';
                const cleanActual = actualCostStr.replace(/[^0-9.-]+/g, '');
                const actualExpenseDb = parseFloat(cleanActual) || 0;

                // If DB actual expenses exist, they might override or supplement? 
                // For now, let's keep the legacy behavior for the "Financial Summary" card
                // but relying on the new "Real-Time" card for the builder view.

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
            window.createDayCard = function (day, index) {
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
                                                                                    oninput="window.updateField(${index}, 'title', this.value)"
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
                                                                                        <label class="text-uppercase small fw-bold mb-0" style="color:var(--bs-primary);"><i class="bi bi-building me-2"></i>Hotels</label>
                                                                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-sm" style="font-size:0.75rem;" onclick="window.openInventoryModal(${index}, 'hotels')"><i class="bi bi-search me-1"></i> Master</button>
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
                                                                                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3 py-1 shadow-sm" style="font-size:0.75rem;" onclick="window.openInventoryModal(${index}, 'transports')"><i class="bi bi-search me-1"></i> Master</button>
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
                                                                                        <button type="button" class="btn btn-sm btn-info text-white rounded-pill px-3 py-1 shadow-sm" style="font-size:0.75rem;" onclick="window.openInventoryModal(${index}, 'spots')"><i class="bi bi-search"></i></button>
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
                                                                                        <button type="button" class="btn btn-sm btn-warning text-dark rounded-pill px-3 py-1 shadow-sm" style="font-size:0.75rem;" onclick="window.openInventoryModal(${index}, 'activities')"><i class="bi bi-search"></i></button>
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
                                                                                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1 shadow-sm" style="font-size:0.75rem;" onclick="window.openInventoryModal(${index}, 'tickets')"><i class="bi bi-search"></i></button>
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
                                                                                        <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1 shadow-sm" style="font-size:0.75rem;" onclick="window.openInventoryModal(${index}, 'meals')"><i class="bi bi-search"></i></button>
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
                                                                            oninput="window.updateField(${index}, 'notes', this.value)">${day.notes || ''}</textarea>
                                                                        </div>
                                                                    </div>`;
                return div;
            }


            window.renderHotels = function (dayIndex, day) {
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
                                                                                oninput="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'name', this.value)">

                                                                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-circle border-0" onclick="window.pushToExpenses(${dayIndex}, 'hotels', ${hIndex})" title="Record as Actual Cost"><i class="bi bi-cash-coin"></i></button>
                                                                                <button type="button" class="btn btn-sm btn-outline-success rounded-circle border-0" onclick="window.shareHotelRequest(${dayIndex}, ${hIndex})" title="Share WP Booking"><i class="bi bi-whatsapp"></i></button>
                                                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle border-0" onclick="window.removeItem(${dayIndex}, 'hotels', ${hIndex})"><i class="bi bi-x-lg"></i></button>
                                                                            </div>
                                                                        </div>
                                                                        <input type="text" class="form-control form-control-sm border-0 mb-2 bg-white" placeholder="Room Type (e.g. Deluxe Room)"
                                                                            value="${hotel.type || ''}"
                                                                            oninput="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'type', this.value)">

                                                                        <div class="d-flex flex-wrap gap-2 text-nowrap">
                                                                            <div class="input-group input-group-sm flex-fill" style="min-width: 90px;">
                                                                                <span class="input-group-text bg-white border-0 text-muted px-2"><i class="bi bi-key"></i></span>
                                                                                <input type="number" class="form-control border-0 pt-1" placeholder="Qty"
                                                                                    value="${hotel.quantity || 1}"
                                                                                    oninput="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'quantity', this.value)">
                                                                            </div>
                                                                            <div class="input-group input-group-sm flex-fill" style="min-width: 120px;">
                                                                                <span class="input-group-text bg-white border-0 text-muted px-2">${currency}</span>
                                                                                <input type="number" class="form-control border-0 pt-1" placeholder="Rate/Night"
                                                                                    value="${hotel.price_per_night || 0}"
                                                                                    oninput="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'price_per_night', this.value)">
                                                                            </div>
                                                                            <div class="input-group input-group-sm flex-fill" style="min-width: 100px;">
                                                                                <span class="input-group-text bg-white border-0 text-muted px-2">+Addon</span>
                                                                                <input type="number" class="form-control border-0 pt-1" placeholder="Addon"
                                                                                    value="${hotel.add_on_price || 0}"
                                                                                    oninput="window.updateListItem(${dayIndex}, 'hotels', ${hIndex}, 'add_on_price', this.value)">
                                                                            </div>
                                                                        </div>`;
                    container.appendChild(row);
                });
            }
            window.renderListItems = function (dayIndex, type, items) {
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
                                                                                oninput="window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, '${nameField}', this.value)">

                                                                            <div class="d-flex align-items-center flex-shrink-0">
                                                                                <button type="button" class="btn btn-sm btn-outline-primary border-0 rounded-circle" onclick="window.pushToExpenses(${dayIndex}, '${type}', ${itemIndex})" title="Record as Actual Cost"><i class="bi bi-cash-coin"></i></button>
                                                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="window.removeItem(${dayIndex}, '${type}', ${itemIndex})"><i class="bi bi-x-lg"></i></button>
                                                                            </div>
                                                                        </div>
                                                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent mb-2" placeholder="Description... (optional)"
                                                                            value="${item.description || ''}"
                                                                            onchange="window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, 'description', this.value)">
                                                                    `;

                    if (type !== 'spots' && type !== 'activities' && type !== 'tickets') {
                        const priceValue = (type === 'transports' || type === 'meals') ? (item.price || 0) : (item.entry_ticket?.price || 0);
                        const qtyValue = (type === 'meals') ? (item.quantity || 1) : 1;

                        const onPriceChange = (type === 'transports' || type === 'meals')
                            ? `window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, 'price', this.value)`
                            : `window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'price', this.value)`;

                        html += `
                                                                            <div class="d-flex flex-wrap gap-2 mt-2 px-1">
                                                                                <div class="input-group input-group-sm flex-fill shadow-sm" style="min-width: 120px;">
                                                                                    <span class="input-group-text bg-white border-0 text-muted px-2">${currency}</span>
                                                                                    <input type="number" class="form-control border-0 pt-1" placeholder="Price" value="${priceValue}" oninput="${onPriceChange}">
                                                                                </div>
                                                                                ${type === 'meals' ? `
                                                                                <div class="input-group input-group-sm flex-fill shadow-sm" style="min-width: 80px;">
                                                                                    <span class="input-group-text bg-white border-0 text-muted px-2">Qty</span>
                                                                                    <input type="number" class="form-control border-0 pt-1" value="${qtyValue}" onchange="window.updateListItem(${dayIndex}, '${type}', ${itemIndex}, 'quantity', this.value)">
                                                                                </div>` : ''}
                                                                            </div>`;
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
                                                                            </div>`;
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
                                                                                    <div class="col-3"><input type="number" class="form-control form-control-sm border-0 shadow-sm px-2 text-center" value="${et.adult_qty || 0}" oninput="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'adult_qty', this.value)"></div>
                                                                                    <div class="col-5"><input type="number" class="form-control form-control-sm border-0 shadow-sm px-2" value="${et.adult_price || et.price || 0}" oninput="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'adult_price', this.value)"></div>
                                                                                </div>
                                                                                <div class="row g-2 align-items-center mb-2">
                                                                                    <div class="col-4 small text-secondary fw-semibold">Child 2-6</div>
                                                                                    <div class="col-3"><input type="number" class="form-control form-control-sm border-0 shadow-sm px-2 text-center" value="${et.child_2_6_qty || 0}" oninput="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'child_2_6_qty', this.value)"></div>
                                                                                    <div class="col-5"><input type="number" class="form-control form-control-sm border-0 shadow-sm px-2" value="${et.child_2_6_price || 0}" oninput="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'child_2_6_price', this.value)"></div>
                                                                                </div>
                                                                                <div class="row g-2 align-items-center mb-2">
                                                                                    <div class="col-4 small text-secondary fw-semibold">Child 6-11</div>
                                                                                    <div class="col-3"><input type="number" class="form-control form-control-sm border-0 shadow-sm px-2 text-center" value="${et.child_6_11_qty || 0}" oninput="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'child_6_11_qty', this.value)"></div>
                                                                                    <div class="col-5"><input type="number" class="form-control form-control-sm border-0 shadow-sm px-2" value="${et.child_6_11_price || 0}" oninput="window.updateListItemNested(${dayIndex}, '${type}', ${itemIndex}, 'entry_ticket', 'child_6_11_price', this.value)"></div>
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
                                                                            </div>`;
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

            // Add listener for city change in modal
            const cityNameSelect = document.getElementById('inventoryDestinationId');
            if (cityNameSelect) cityNameSelect.addEventListener('change', fetchItems);

            window.filterInventoryCities = function () {
                const country = document.getElementById('inventoryCountrySelect').value;
                const citySelect = document.getElementById('inventoryDestinationId');
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

            window.openInventoryModal = function (dayIndex, type) {
                currentDayIndex = dayIndex;
                currentType = type;
                const titleElem = document.getElementById('inventoryModalTitle');
                if (titleElem) titleElem.innerText = 'Select ' + type.charAt(0).toUpperCase() + type.slice(1) + ' from Master';

                // On open, ensure cities are filtered by the current country selection
                filterInventoryCities();

                if (inventoryModal) inventoryModal.show();
            };

            function fetchItems() {
                const dSelect = document.getElementById('inventoryDestinationId');
                const selectedDestinationId = dSelect ? dSelect.value : "";
                const countryFilter = document.getElementById('inventoryCountrySelect').value;
                const countrySelectEl = document.getElementById('inventoryCountrySelect');
                const countryId = countrySelectEl ? (countrySelectEl.options[countrySelectEl.selectedIndex]?.getAttribute('data-country-id') || '') : '';
                const search = document.getElementById('inventorySearch').value;

                let url = `/api/inventory/${currentType}?search=${encodeURIComponent(search)}`;
                if (selectedDestinationId) {
                    url += `&destination_id=${selectedDestinationId}`;
                } else if (countryId && currentType === 'spots') {
                    // For tourist spots: use country_id (direct FK — most accurate)
                    url += `&country_id=${countryId}`;
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
                                    document.getElementById('wiz-price').innerText = `{{ $itinerary['currency'] ?? 'INR' }} ${item.base_price}`;
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

            // Search listener
            document.getElementById('inventorySearch').addEventListener('input', debounce(fetchItems, 300));

            function debounce(func, wait) {
                let timeout;
                return function (...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
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
                        add_on_price: 0,
                        service_id: item.is_core_service ? item.id : null,
                        supplier_id: item.supplier_id || null
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
                            child_6_11_qty: c2,
                            currency: currency
                        },
                        service_id: item.is_core_service ? item.id : null,
                        supplier_id: item.supplier_id || null
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
                            child_6_11_qty: c2,
                            currency: currency
                        },
                        service_id: item.is_core_service ? item.id : null,
                        supplier_id: item.supplier_id || null
                    });
                } else if (currentType === 'spots') {
                    if (!Array.isArray(itinerary[currentDayIndex].spots)) itinerary[currentDayIndex].spots = [];
                    itinerary[currentDayIndex].spots.push({
                        name: item.name,
                        description: item.description || '',
                        hours: 2,
                        price_per_hour: 0,
                        service_id: item.is_core_service ? item.id : null,
                        supplier_id: item.supplier_id || null
                    });
                } else if (currentType === 'transports') {
                    if (!Array.isArray(itinerary[currentDayIndex].transport)) itinerary[currentDayIndex].transport = [];
                    itinerary[currentDayIndex].transport.push({
                        name: item.name,
                        type: item.vehicle_type,
                        price: item.base_price,
                        currency: currency,
                        service_id: item.is_core_service ? item.id : null,
                        supplier_id: item.supplier_id || null
                    });
                } else if (currentType === 'meals') {
                    if (!Array.isArray(itinerary[currentDayIndex].meals)) itinerary[currentDayIndex].meals = [];
                    const adults = parseInt(document.getElementById('pax-adults')?.value || 1);
                    itinerary[currentDayIndex].meals.push({
                        name: '[' + item.type + '] ' + item.name,
                        price: item.price,
                        quantity: adults,
                        currency: currency,
                        service_id: item.is_core_service ? item.id : null,
                        supplier_id: item.supplier_id || null
                    });
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
            let isDirty = false;
            window.confirmPdfDownload = (e) => {
                if (isDirty) {
                    if (!confirm("Warning: You have unsaved changes. The PDF will generate using the saved database values, which doesn't include your current changes. Please click 'Save Proposal' to apply your changes first. Download anyway?")) {
                        e.preventDefault();
                        return false;
                    }
                }
                return true;
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
                const refId = "{{ $itinerary['id'] }}";

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
                const currencyStr = "{{ $itinerary['currency'] ?? 'INR' }}";
                text += `Net Rate: ${currencyStr} ${cost.toFixed(2)}\n`;
                text += `Please confirm availability.`;

                const number = prompt("Enter Hotel Reservations WhatsApp Number:", "");
                if (number) {
                    window.open(`https://api.whatsapp.com/send?phone=${number}&text=${encodeURIComponent(text)}`, '_blank');
                }
                window.copyPdfLink = () => {
                    if (isDirty) {
                        if (!confirm("Warning: You have unsaved changes. The copied link will generate a PDF based on the saved database values, which doesn't include your current changes. Copy anyway?")) {
                            return;
                        }
                    }
                    navigator.clipboard.writeText("{{ route('admin.b2b-itineraries.pdf', $itinerary['id']) }}?public=1").then(() => alert('Customer PDF Link copied!'));
                };

                window.shareCustomerQuote = () => {
                    if (isDirty) {
                        if (!confirm("Warning: You have unsaved changes. The WhatsApp summary text will reflect your current screen inputs, but the itinerary PDF link in the message will download the old saved proposal. We highly recommend clicking 'Save Proposal' first. Share anyway?")) {
                            return;
                        }
                    }
                    const title = document.getElementById('proposal-title').value;
                    const clientName = document.getElementById('client-name').value;
                    const adults = document.getElementById('pax-adults').value;
                    const c1 = document.getElementById('pax-child-small').value;
                    const c2 = document.getElementById('pax-child-large').value;

                    const total = document.getElementById('preview-grand-total').innerText;
                    const totalVal = parseFloat(total.replace(/[^0-9.]/g, '') || 0);
                    const adultsCount = parseInt(adults || 0);
                    const c1Count = parseInt(c1 || 0);
                    const c2Count = parseInt(c2 || 0);
                    const currencyStr = "{{ $itinerary['currency'] ?? 'INR' }}";

                    let adultRate = 0;
                    let childRateSmall = 0;
                    let childRateLarge = 0;

                    if (c1Count > 0 || c2Count > 0) {
                        const weightedPax = (adultsCount * 1.0) + (c1Count * 0.25) + (c2Count * 0.50);
                        adultRate = weightedPax > 0 ? (totalVal / weightedPax) : 0;
                        childRateSmall = adultRate * 0.25;
                        childRateLarge = adultRate * 0.50;
                    } else {
                        adultRate = adultsCount > 0 ? (totalVal / adultsCount) : 0;
                    }

                    let text = `*📋 BOOKING PROPOSAL: ${title.toUpperCase()}*\n`;
                    text += `🏢 *Company:* Tourliz\n`;
                    text += `👤 *Guest:* ${clientName}\n`;
                    text += `👥 *Pax:* ${adults} Adults`;
                    if (c1 > 0) text += `, ${c1} Child(2-6y)`;
                    if (c2 > 0) text += `, ${c2} Child(6-11y)`;
                    text += `\n\n`;

                    text += `*Full Itinerary Summary:*\n`;
                    itinerary.forEach(day => {
                        text += `*Day ${day.day}: ${day.title || ''}*\n`;
                        const spots = [...ensureArray(day.spots), ...ensureArray(day.activities), ...ensureArray(day.places)];
                        if (spots.length > 0) {
                            spots.forEach(s => {
                                const name = s.name || s.attraction_name;
                                if (name) text += `📍 ${name}\n`;
                            });
                        }
                        text += `\n`;
                    });

                    text += `*Pricing Details:*\n`;
                    text += `Total Final Quote: ${total}\n`;
                    text += `Rate Per Adult: ${currencyStr} ${adultRate.toFixed(2)}\n`;
                    if (c1 > 0) text += `Rate Child (2-6y): ${currencyStr} ${childRateSmall.toFixed(2)}\n`;
                    if (c2 > 0) text += `Rate Child (6-11y): ${currencyStr} ${childRateLarge.toFixed(2)}\n`;

                    text += `\n🔗 *Full Itinerary Link:* {{ route('admin.b2b-itineraries.pdf', $itinerary['id']) }}?public=1\n`;
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
                    const pickup = (dayIndex > 0 && ensureArray(itinerary[dayIndex - 1].hotels).length > 0)
                        ? itinerary[dayIndex - 1].hotels[0].name
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

                // --- General Vendor Share (Expenses) ---
                // --- General Vendor Share (Expenses) ---
                window.shareVendorWhatsapp = (expenseId, supplierId) => {
                    // 1. If Expense ID exists, use backend logic
                    if (expenseId && expenseId > 0) {
                        fetch(`/admin/expenses/${expenseId}/whatsapp-vendor`)
                            .then(async res => {
                                const data = await res.json();
                                if (!res.ok) {
                                    throw new Error(data.error || "Failed to generate message");
                                }
                                return data;
                            })
                            .then(data => {
                                if (data.text) {
                                    window.open(`https://wa.me/?text=${encodeURIComponent(data.text)}`, '_blank');
                                } else {
                                    alert("Error: Empty message received.");
                                }
                            })
                            .catch(err => alert(err.message));
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
                        const refId = "{{ $itinerary['itinerary_id'] ?? $itinerary['id'] }}";

                        if (vendorType.toLowerCase().includes('hotel') || vendorType.toLowerCase().includes('accommodation')) {
                            // Template A: Hotel Vendor (STRICT PRIVACY - ISOLATION MODE)
                            let text = `*AVAILABILITY CHECK* - *Tourliz*\n`;
                            text += `*Ref ID:* #${refId}\n`;
                            text += `👤 *Guest:* ${clientName}\n`;
                            text += `📅 *Arrival:* ${arrivalDate}\n`;
                            text += `🌙 *Stay:* ${nights} Nights\n`;

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
                                text += `*Total Amount:* {{ $itinerary['currency'] ?? 'INR' }} ${totalCost.toFixed(2)}\n`;
                            }

                            text += `*Auth by:* {{ Auth::user()->name }} (Tourliz)\n`;
                            text += `\n_Thank you, Tourliz Team_ 🙏\n`;

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
                            if (parseInt(c1) + parseInt(c2) > 0) text += `, ${parseInt(c1) + parseInt(c2)} Kids`;
                            text += `\n`;

                            if (specificExp) {
                                // Individual Share Mode
                                text += `*Activity:* ${specificExp.description}\n`;
                                // Professional Date Format
                                const expDate = specificExp.expense_date ? (new Date(specificExp.expense_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })) : '';
                                if (expDate && expDate !== 'Invalid Date') text += `*Date:* ${expDate}\n`;

                                text += `*Total Amount:* {{ $itinerary['currency'] ?? 'INR' }} ${parseFloat(specificExp.amount || 0).toFixed(2)}\n`;
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
                                if (totalCost > 0) text += `*Total Amount:* {{ $itinerary['currency'] ?? 'INR' }} ${totalCost.toFixed(2)}\n`;
                            }

                            text += `*Auth by:* {{ Auth::user()->name }}\n`;
                            window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
                        } else {
                            // General Vendor Share
                            let text = `*AVAILABILITY CHECK*\n`;
                            text += `Ref ID: #${refId}\n`;
                            text += `Guest: ${clientName}\n`;
                            text += `Arrival: ${arrivalDate}\n`;
                            text += `Pax: ${adults} Adults + ${parseInt(c1) + parseInt(c2)} Kids\n`;
                            text += `\nRep: {{ Auth::user()->name }}`;
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

                    // Collect country IDs
                    const selectedCountries = [];
                    $('.country-ck:checked').each(function () {
                        selectedCountries.push($(this).val());
                    });
                    document.getElementById('formCountryIds').value = JSON.stringify(selectedCountries);

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

                    isDirty = false; // Reset dirty flag before submission
                    document.getElementById('saveForm').submit();
                });

                // Initialize display
                renderBuilder();
                // loadExpenses(); // Moved to end of file
                // loadSuppliers(); // Moved to end of file
            }
        </script>
    @endpush

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
                        <input type="hidden" name="itinerary_id" value="{{ $itinerary['id'] }}">
                        <input type="hidden" name="itinerary_type" value="b2b">
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
                        <div class="mb-2 d-none" id="vehicle-type-field">
                            <label class="form-label small text-muted mb-1">Vehicle Type</label>
                            <select name="vehicle_type" class="form-select form-select-sm">
                                <option value="">Select Vehicle</option>
                                <option value="Sedan">Sedan</option>
                                <option value="SUV">SUV</option>
                                <option value="Van">Van</option>
                                <option value="Mini Bus">Mini Bus</option>
                                <option value="Coaster">Coaster</option>
                                <option value="Luxury Car">Luxury Car</option>
                            </select>
                        </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-8">
                        <label class="form-label small text-muted mb-1">Unit Rate</label>
                        <input type="number" step="0.01" id="expense-rate" class="form-control form-control-sm"
                            placeholder="Price per unit">
                    </div>
                    <div class="col-4">
                        <label class="form-label small text-muted mb-1">Qty</label>
                        <input type="number" step="1" id="expense-qty" class="form-control form-control-sm" value="1">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Amount
                        ({{ $itinerary['currency'] ?? 'INR' }})</label>
                    <input type="number" step="0.01" name="amount" id="expense-amount" class="form-control form-control-sm"
                        required>
                </div>

                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Supplier <a href="#"
                            class="float-end text-decoration-none" data-bs-toggle="modal"
                            data-bs-target="#newSupplierModal">+ New</a></label>
                    <select name="supplier_id" id="expense-supplier-id" class="form-select form-select-sm">
                        <option value="">Select Partner (Optional)</option>
                        {{-- Loaded via JS --}}
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Expense Date</label>
                    <input type="date" name="expense_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}"
                        required>
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

    <!-- Vendor Payment Modal -->
    <div class="modal fade" id="vendorPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title fs-6">Record Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="vendorPaymentForm">
                        <input type="hidden" id="vp_expense_id">
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-1">Total Paid Amount</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">{{ $itinerary['currency'] ?? 'INR' }}</span>
                                <input type="number" step="0.01" id="vp_amount" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-1">Paid By (Method)</label>
                            <input type="text" id="vp_paid_by" class="form-control form-control-sm" list="paymentMethods"
                                placeholder="e.g. Cash, Office Account">
                            <datalist id="paymentMethods">
                                <option value="Cash">
                                <option value="Bank Transfer">
                                <option value="UPI">
                                <option value="Office Account">
                                <option value="Driver">
                                <option value="Petty Cash">
                            </datalist>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success w-100">Update Payment</button>
                    </form>
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
                        <input type="hidden" name="destination_id" value="{{ $itinerary['destination_id'] ?? '' }}">
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

    <!-- Change Supplier Modal -->
    <div class="modal fade" id="changeSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title fs-6">Change Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Select New Vendor</label>
                        <select id="change-supplier-select" class="form-select form-select-sm">
                            <!-- Populated JS -->
                        </select>
                    </div>
                    <button class="btn btn-primary btn-sm w-100" onclick="saveChangedSupplier()">Update Vendor</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentExpenses = [];
        let allSuppliers = []; // Store suppliers for dropdown

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
                expenseId: e.id,
                status: e.status
            }));

            // Collect vendors from checkboxes
            const selectedVendors = [];
            $('.vendor-cb:checked').each(function () {
                const id = $(this).val();
                const label = $(this).next('label').text().trim();
                const name = label.split(' (')[0];
                const typeMatch = label.match(/\(([^)]+)\)/);
                const type = typeMatch ? typeMatch[1] : '';

                if (!expenseVendors.some(ev => ev.id == id)) {
                    selectedVendors.push({ id, name, type, category: 'General', description: 'Selected for Proposal' });
                }
            });

            const allVendors = [...expenseVendors, ...selectedVendors];

            if (allVendors.length === 0) {
                container.innerHTML = '<div class="p-4 text-center text-muted">No vendors selected. Choose vendors in the left sidebar or add expenses.</div>';
            } else {
                allVendors.forEach(vendor => {
                    let statusBadge = '';
                    if (vendor.status) {
                        let statusColor = 'secondary';
                        if (vendor.status === 'confirmed') statusColor = 'success';
                        if (vendor.status === 'requested') statusColor = 'warning';
                        if (vendor.status === 'rejected') statusColor = 'danger';
                        statusBadge = `<span class="badge bg-${statusColor} ms-1 text-uppercase" style="font-size: 0.65rem;">${vendor.status}</span>`;
                    }

                    const item = document.createElement('div');
                    item.className = 'list-group-item p-3 d-flex justify-content-between align-items-center';
                    item.innerHTML = `
                                                                                                                                         <div style="flex:1">
                                                                                                                                         <div class="fw-bold">${vendor.name} <span class="badge bg-light text-dark border ms-1">${vendor.category || vendor.type}</span>${statusBadge}</div>
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

        function updateExpenseStatus(id, status) {
            $.ajax({
                url: `/admin/expenses/${id}`,
                type: 'PUT',
                data: { status: status },
                success: function (res) {
                    // Update local data
                    const idx = currentExpenses.findIndex(e => e.id == id);
                    if (idx !== -1) currentExpenses[idx].status = status;
                    // Re-render modal to toggle Change Vendor button if needed
                    // For simply changing select color, we might want full refresh, but let's just re-open/render for now or simple alert
                    // Ideally we refresh the list status badge.
                    // Let's close and re-open to refresh the view or just refresh list
                    openVendorShareModal();
                }
            });
        }

        let editingExpenseId = null;
        function openChangeSupplierModal(expenseId) {
            editingExpenseId = expenseId;
            const exp = currentExpenses.find(e => e.id == expenseId);

            // populate modal
            const modal = new bootstrap.Modal(document.getElementById('changeSupplierModal'));

            // Populate select options
            const select = document.getElementById('change-supplier-select');
            select.innerHTML = '<option value="">Select New Vendor</option>';
            allSuppliers.forEach(s => {
                select.innerHTML += `<option value="${s.id}">${s.name} (${s.type})</option>`;
            });

            modal.show();
        }

        function saveChangedSupplier() {
            const newSupplierId = document.getElementById('change-supplier-select').value;
            if (!newSupplierId) return alert('Select a supplier');

            $.ajax({
                url: `/admin/expenses/${editingExpenseId}`,
                type: 'PUT',
                data: { supplier_id: newSupplierId, status: 'pending' }, // Reset to pending
                success: function (res) {
                    location.reload(); // Simplest way to reflect everything including main list
                }
            });
        }
    </script>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            loadExpenses();
            // loadSuppliers(); // Moved to loadExpenses success callback to avoid race condition
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
            document.getElementById('markup-percentage').addEventListener('change', calculateDynamicTotal);
            document.getElementById('trip-duration').addEventListener('change', calculateDynamicTotal);
        });

        function loadSuppliers() {
            $.get("{{ route('admin.suppliers.index') }}", { destination_id: "{{ $itinerary['destination_id'] ?? '' }}" }, function (data) {
                allSuppliers = data; // Store globally

                // Initialize filtered dropdown based on default/current category
                const currentCat = $('#expense-category').val();
                filterSuppliersByCategory(currentCat);

                // Populate Main proposal checkboxes (Sidebar) - Keep all
                const checkboxContainer = $('#vendor-checkboxes');
                checkboxContainer.empty();

                const currentVendorId = parseInt("{{ $itinerary['supplier_id'] ?? 0 }}");

                data.forEach(s => {
                    const isChecked = ((s.id == currentVendorId) || (currentExpenses && currentExpenses.some(e => e.supplier_id == s.id))) ? 'checked' : '';
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
            const currentVal = expenseSelect.val(); // Preserve if possible
            expenseSelect.find('option:not(:first)').remove(); // Keep default Select Partner option

            if (!allSuppliers || allSuppliers.length === 0) return;

            // Map Category names to Supplier Types if needed, or loosely match
            // Categories: Hotel, Transport, Activity, Meal, Agent, Other
            // Supplier Types (DB): Hotel, Transport, Activity, Agent, Restaurant, etc.

            const filtered = allSuppliers.filter(s => {
                const sType = (s.type || '').toLowerCase();
                const cat = (category || '').toLowerCase();

                if (cat === 'hotel') return sType === 'hotel';
                if (cat === 'transport') return sType === 'transport' || sType === 'driver' || sType === 'taxi';
                if (cat === 'activity') return sType === 'activity' || sType === 'attraction';
                if (cat === 'meal') return sType === 'restaurant' || sType === 'meal';
                if (cat === 'agent') return sType === 'agent';
                // For 'Other' or unmatched, maybe show all? Or just 'Other' types?
                // Let's show all if 'Other' is selected to be safe, or just specific ones?
                // User asked "based upon vendor service", implies strict filtering.
                if (cat === 'other') return true;

                return false;
            });

            // If strict filtering yields nothing, or if logic fails, maybe show all? 
            // Let's stick to strict but fallback to showing all if 'Other' or empty?
            // Actually, let's append matching ones.

            (filtered.length > 0 ? filtered : allSuppliers).forEach(s => {
                expenseSelect.append(`<option value="${s.id}">${s.name} (${s.type})</option>`);
            });

            if (currentVal) expenseSelect.val(currentVal);

            // Also call toggleVehicleTypeField for the category change
            toggleVehicleTypeField(category);
        }

        // Event listener for category change
        $(document).on('change', '#expense-category', function () {
            filterSuppliersByCategory(this.value);
        });

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



        window.pushToExpenses = function (dayIndex, type, itemIndex) {
            const day = itinerary[dayIndex];
            const k = type === 'tickets' ? 'places' : (type === 'transports' ? 'transport' : type);
            const item = (type === 'hotels') ? day.hotels[itemIndex] : day[k][itemIndex];

            if (!item) return;

            let amount = 0;
            let description = '';
            let category = 'Other';

            if (type === 'hotels') {
                amount = (safeFloat(item.price_per_night) + safeFloat(item.add_on_price)) * safeFloat(item.quantity || 1);
                description = `Day ${day.day}: ${item.name} (${item.type})`;
                category = 'Hotel';
            } else if (type === 'activities' || type === 'tickets') {
                const et = item.entry_ticket || {};
                amount = (safeFloat(et.adult_price || et.price) * safeFloat(et.adult_qty)) +
                    (safeFloat(et.child_2_6_price) * safeFloat(et.child_2_6_qty)) +
                    (safeFloat(et.child_6_11_price) * safeFloat(et.child_6_11_qty));
                description = `Day ${day.day}: ${item.name || item.attraction_name}`;
                category = 'Activity';
            } else if (type === 'transports') {
                amount = safeFloat(item.price);
                description = `Day ${day.day}: Transport - ${item.name}`;
                category = 'Transport';
            } else if (type === 'meals') {
                amount = safeFloat(item.price) * safeFloat(item.quantity || 1);
                description = `Day ${day.day}: Meals - ${item.name}`;
                category = 'Meal';
            } else if (type === 'spots') {
                amount = safeFloat(item.hours) * safeFloat(item.price_per_hour);
                description = `Day ${day.day}: Spot - ${item.name}`;
                category = 'Activity';
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
                itinerary_id: "{{ $itinerary['id'] }}",
                itinerary_type: 'b2b',
                category: category,
                amount: amount,
                expense_date: expenseDate,
                description: description,
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
        }

        function downloadVendorPdf(id) {
            window.open(`/admin/expenses/${id}/pdf-vendor`, '_blank');
        }

        function loadExpenses() {
            $.get("{{ route('admin.expenses.index') }}", {
                itinerary_id: "{{ $itinerary['id'] }}",
                itinerary_type: 'b2b'
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
                                                                                                                 <td class="text-end fw-bold text-danger" style="white-space: nowrap;">
                                                                                                                  <div>-${currency} ${parseFloat(exp.amount).toFixed(2)}
                                                                                                                      <button class="btn btn-link btn-sm p-0 text-muted opacity-50" onclick="window.editExpenseInline(${exp.id}, ${exp.amount})" title="Edit Amount">
                                                                                                                          <i class="bi bi-pencil-square" style="font-size: 0.7rem;"></i>
                                                                                                                      </button>
                                                                                                                   </div>
                                                                                                                   <div class="text-success small opacity-75 mt-1" style="font-size: 0.7rem;">
                                                                                                                      Paid: ${currency} ${parseFloat(exp.paid_amount || 0).toFixed(2)}
                                                                                                                  <span class="text-muted" style="font-size: 0.6rem;">${exp.paid_by ? '(' + exp.paid_by + ')' : ''}</span>
                                                                                                                      <button class="btn btn-link btn-sm p-0 text-success opacity-50" onclick="window.editPaidAmount(${exp.id}, ${exp.paid_amount || 0}, '${exp.paid_by || ''}')" title="Record Payment">
                                                                                                                          <i class="bi bi-pencil-square" style="font-size: 0.6rem;"></i>
                                                                                                                      </button>
                                                                                                                   </div>
                                                                                                                   <div class="text-danger small fw-bold mt-1" style="font-size: 0.7rem;">
                                                                                                                      To Pay: ${currency} ${(parseFloat(exp.amount) - parseFloat(exp.paid_amount || 0)).toFixed(2)}
                                                                                                                   </div>
                                                                                                               </td>
                                                                                                                 <td class="text-end">
                                                                                                                 ${actions}
                                                                                                                 <button class="btn btn-link text-muted p-0" onclick="deleteExpense(${exp.id})" title="Remove Cost">
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
                loadSuppliers(); // Refresh checkboxes based on loaded expenses
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

        window.editExpenseInline = function (id, currentAmount) {
            const newAmount = prompt("Enter actual cost for this vendor:", currentAmount);
            if (newAmount === null) return;

            const amount = parseFloat(newAmount);
            if (isNaN(amount)) {
                alert("Please enter a valid number");
                return;
            }

            $.ajax({
                url: `/admin/expenses/${id}`,
                type: 'PATCH',
                data: {
                    amount: amount,
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    if (res.success) {
                        loadExpenses();
                    }
                },
                error: function (xhr) {
                    alert("Error updating amount: " + (xhr.responseJSON?.message || "Unknown error"));
                }
            });
        }

        window.editPaidAmount = function (id, currentPaid, currentPaidBy) {
            $('#vp_expense_id').val(id);
            $('#vp_amount').val(currentPaid);
            $('#vp_paid_by').val(currentPaidBy || '');
            $('#vendorPaymentModal').modal('show');
        }

        // Handle Vendor Payment Form Submission
        $('#vendorPaymentForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#vp_expense_id').val();
            const amount = parseFloat($('#vp_amount').val());
            const paidBy = $('#vp_paid_by').val();

            if (isNaN(amount)) {
                alert("Please enter a valid amount");
                return;
            }

            $.ajax({
                url: `/admin/expenses/${id}`,
                type: 'PATCH',
                data: {
                    paid_amount: amount,
                    paid_by: paidBy,
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    if (res.success) {
                        $('#vendorPaymentModal').modal('hide');
                        loadExpenses();
                    }
                },
                error: function (xhr) {
                    alert("Error updating payment: " + (xhr.responseJSON?.message || "Unknown error"));
                }
            });
        });

        function updateFinancialSummary(actualCost) {
            const totalQuoted = parseFloat("{{ $itinerary['total_price'] ?? 0 }}");
            const profit = totalQuoted - actualCost;
            const currencyStr = "{{ $itinerary['currency'] ?? 'INR' }} ";

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

        function toggleVehicleTypeField(category) {
            const field = document.getElementById('vehicle-type-field');
            if (category === 'Transport') {
                field.classList.remove('d-none');
            } else {
                field.classList.add('d-none');
            }
        }

        // Auto-calculate expense amount
        $(document).on('input', '#expense-rate, #expense-qty', function () {
            const rate = parseFloat($('#expense-rate').val()) || 0;
            const qty = parseFloat($('#expense-qty').val()) || 1;
            $('#expense-amount').val((rate * qty).toFixed(2));
        });

        // Final Initialization
        $(document).ready(function () {
            if (typeof loadExpenses === 'function') loadExpenses();
            if (typeof loadSuppliers === 'function') loadSuppliers();

            // Set up automatic dirty checking
            setTimeout(() => {
                isDirty = false; // Reset after initial programmatic renders
                $(document).on('change input', 'input, select, textarea', function () {
                    isDirty = true;
                });

                // Wrap renderBuilder to flag structure edits
                const originalRender = window.renderBuilder;
                if (originalRender) {
                    window.renderBuilder = function (...args) {
                        isDirty = true;
                        return originalRender.apply(this, args);
                    };
                }
            }, 1000);
        });
    </script>
@endpush
```