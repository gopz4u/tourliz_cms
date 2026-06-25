@extends('layouts.admin')

@section('title', 'B2B Itineraries')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-file-earmark-text me-2"></i>B2B Itineraries</h2>
                <p class="text-muted mb-0">Manage custom proposals for agencies</p>
            </div>
            <a href="{{ route('admin.b2b-itineraries.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Create Proposal
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body bg-light border-bottom">
            <form action="{{ route('admin.b2b-itineraries.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Search (Title, Client, Quote ID)</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="e.g. QT-2024..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Destination (Country)</label>
                    <select name="destination_id" class="form-select form-select-sm">
                        <option value="">All Countries</option>
                        @foreach($destinations as $p)
                            <option value="{{ $p->id }}" {{ request('destination_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-bold">Year</label>
                    <select name="year" class="form-select form-select-sm">
                        <option value="">Year</option>
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-bold">Month</label>
                    <select name="month" class="form-select form-select-sm">
                        <option value="">Month</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Payment Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Not Paid</option>
                        <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Half Paid
                        </option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Followup</label>
                    <select name="followup" class="form-select form-select-sm">
                        <option value="">All Leads</option>
                        <option value="leads" {{ request('followup') == 'leads' ? 'selected' : '' }}>New Leads</option>
                        <option value="waiting" {{ request('followup') == 'waiting' ? 'selected' : '' }}>Waiting</option>
                        <option value="interested" {{ request('followup') == 'interested' ? 'selected' : '' }}>Interested
                        </option>
                        <option value="converted" {{ request('followup') == 'converted' ? 'selected' : '' }}>Converted
                        </option>
                        <option value="not_interested" {{ request('followup') == 'not_interested' ? 'selected' : '' }}>Not
                            Interested</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                    <a href="{{ route('admin.b2b-itineraries.index') }}" class="btn btn-sm btn-outline-secondary"><i
                            class="bi bi-arrow-clockwise"></i></a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3" width="120">Quote ID</th>
                            <th>Proposal / Client</th>
                            <th>Agency</th>
                            <th>Managed By</th>
                            <th>Total Price</th>
                            <th>Followup</th>
                            <th>Payment</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itineraries as $itinerary)
                                    <tr>
                                        <td class="ps-3"><code class="fw-bold">{{ $itinerary->quote_id }}</code></td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $itinerary->title }}</div>
                                            <small class="text-muted"><i
                                                    class="bi bi-person me-1"></i>{{ $itinerary->client_name ?? 'Guest' }}</small>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-light text-dark border">{{ $itinerary->agency->company_name ?? 'N/A' }}</span>
                                            <div class="small text-muted">{{ $itinerary->destination->name ?? '' }} |
                                                {{ $itinerary->duration_days }} Days
                                            </div>
                                            @if($itinerary->supplier)
                                                <div class="mt-1">
                                                    <span class="badge bg-info-subtle text-info border-info" style="font-size: 0.65rem;">
                                                        <i class="bi bi-truck me-1"></i>{{ $itinerary->supplier->name }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small fw-bold text-dark"><i
                                                    class="bi bi-person-badge me-1"></i>{{ $itinerary->user->name ?? 'System' }}</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">{{ $itinerary->user->role ?? 'admin' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $itinerary->currency }}
                                                {{ number_format($itinerary->total_price, 2) }}
                                            </div>
                                            <div class="small text-muted">{{ $itinerary->adults }}A +
                                                {{ $itinerary->children_2_6 + $itinerary->children_6_11 }}C
                                            </div>
                                        </td>
                                        <td>
                            @php
                                $fStatus = $itinerary->followup_status ?? 'leads';
                                $fBadge = 'bg-secondary';
                                if ($fStatus == 'interested')
                                    $fBadge = 'bg-info';
                                if ($fStatus == 'converted')
                                    $fBadge = 'bg-success';
                                if ($fStatus == 'waiting')
                                    $fBadge = 'bg-warning text-dark';
                                if ($fStatus == 'not_interested' || $fStatus == 'dead')
                                    $fBadge = 'bg-danger';
                            @endphp
                                            <span class="badge {{ $fBadge }} w-100 mb-1"
                                                style="font-size: 0.65rem;">{{ strtoupper($fStatus) }}</span>
                                            @if($itinerary->followed_up_at)
                                                <div style="font-size: 0.6rem;" title="Last Followup">
                                                    <i
                                                        class="bi bi-calendar-check me-1"></i>{{ $itinerary->followed_up_at->diffForHumans() }}
                                                </div>
                                            @endif
                                            @if($itinerary->next_followup_date)
                                                <div style="font-size: 0.6rem;" class="text-primary mt-1 fw-bold">
                                                    <i class="bi bi-clock me-1"></i>Next:
                                                    {{ $itinerary->next_followup_date->format('d M') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <!-- Proposal Status -->
                                            <div class="mb-1">
                                                @if($itinerary->status == 'proposed')
                                                    <span class="badge rounded-pill bg-outline-info border border-info text-info px-2"
                                                        style="font-size: 0.6rem;">PROPOSED</span>
                                                @elseif($itinerary->status == 'confirmed')
                                                    <span class="badge rounded-pill bg-success px-2"
                                                        style="font-size: 0.6rem;">CONFIRMED</span>
                                                @else
                                                    <span class="badge rounded-pill bg-light text-muted border px-2"
                                                        style="font-size: 0.6rem;">{{ strtoupper($itinerary->status ?? 'DRAFT') }}</span>
                                                @endif
                                            </div>
                                            <!-- Payment Status -->
                                            <div>
                                                @if($itinerary->payment_status == 'paid')
                                                    <span class="text-success fw-bold" style="font-size: 0.7rem;"><i
                                                            class="bi bi-check-circle-fill me-1"></i>PAID</span>
                                                @elseif($itinerary->payment_status == 'partially_paid')
                                                    <span class="text-warning fw-bold" style="font-size: 0.7rem;"><i
                                                            class="bi bi-hourglass-split me-1"></i>HALF</span>
                                                @else
                                                    <span class="text-danger fw-bold" style="font-size: 0.7rem;"><i
                                                            class="bi bi-x-circle me-1"></i>PENDING</span>
                                                @endif
                                            </div>
                                            <!-- Vendor Payment Info -->
                                            <div class="mt-2 border-top pt-1">
                                                @php
                                                    $vTotal = $itinerary->expenses->sum('amount');
                                                    $vPaid = $itinerary->expenses->sum('paid_amount');
                                                    $vDue = $vTotal - $vPaid;
                                                @endphp
                                                @if($vTotal > 0)
                                                    <div style="font-size: 0.65rem;" class="text-muted mb-0">Vendor:
                                                        {{ number_format($vTotal, 0) }}</div>
                                                    @if($vDue > 0)
                                                        <div style="font-size: 0.7rem;" class="text-danger fw-bold">Due:
                                                            {{ number_format($vDue, 0) }}</div>
                                                    @else
                                                        <div style="font-size: 0.7rem;" class="text-success fw-bold"><i
                                                                class="bi bi-check-all"></i> Paid</div>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group">
                                                <a href="{{ route('admin.b2b-itineraries.edit', $itinerary->id) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="{{ route('admin.b2b-itineraries.pdf', $itinerary->id) }}"
                                                    class="btn btn-sm btn-outline-success" title="PDF">
                                                    <i class="bi bi-file-pdf"></i>
                                                </a>
                                                <form action="{{ route('admin.b2b-itineraries.destroy', $itinerary->id) }}"
                                                    method="POST" class="d-inline delete-form" onsubmit="return confirm('Delete this proposal? This action cannot be undone.');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No proposals matching your filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $itineraries->links() }}
    </div>
    </div>
    </div>
@endsection