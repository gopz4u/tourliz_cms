@extends('layouts.admin')

@section('title', 'B2C (Direct) Itineraries')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2><i class="bi bi-people-fill me-2"></i>B2C Direct Proposals</h2>
                <p class="text-muted mb-0">Manage walk-in leads and direct customer bookings</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                {{-- View Toggle --}}
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.b2c-itineraries.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-table me-1"></i>Table
                    </a>
                    <a href="{{ route('admin.b2c-itineraries.kanban') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-kanban me-1"></i>Kanban
                    </a>
                </div>
                <a href="{{ route('admin.b2c-itineraries.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>New Walk-in Lead
                </a>
            </div>
        </div>
    </div>


    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body bg-light border-bottom">
            <form action="{{ route('admin.b2c-itineraries.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Search (Name, Phone, ID)</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="e.g. B2C-2024..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Destination (Country)</label>
                    <select name="destination_id" class="form-select form-select-sm">
                        <option value="">All Countries</option>
                        @foreach($destinations as $p)
                            <option value="{{ $p->id }}" {{ request('destination_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Source</label>
                    <select name="source" class="form-select form-select-sm">
                        <option value="">All Sources</option>
                        <option value="walk_in">Walk-in</option>
                        <option value="call">Call</option>
                        <option value="social">Social Media</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Followup</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Leads</option>
                        <option value="leads">New Leads</option>
                        <option value="interested">Interested</option>
                        <option value="converted">Converted</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-3">Filter</button>
                    <a href="{{ route('admin.b2c-itineraries.index') }}"
                        class="btn btn-sm btn-outline-secondary px-3">Reset</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3" width="130">Lead ID</th>
                            <th>Customer Info</th>
                            <th>Contact</th>
                            <th>Pax / Details</th>
                            <th>Managed By</th>
                            <th>Followup</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itineraries as $it)
                            <tr>
                                <td class="ps-3"><code class="fw-bold">{{ $it->quote_id }}</code></td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $it->client_name }}</div>
                                    <span class="badge bg-light text-muted border"
                                        style="font-size: 0.65rem;">{{ strtoupper(str_replace('_', ' ', $it->lead_source)) }}</span>
                                </td>
                                <td>
                                    @if($it->phone)
                                        <div class="small"><i class="bi bi-telephone me-1 text-primary"></i>{{ $it->phone }}</div>
                                    @endif
                                    @if($it->email)
                                        <div class="small text-muted"><i
                                                class="bi bi-envelope me-1"></i>{{ Str::limit($it->email, 20) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $it->currency }} {{ number_format($it->total_price, 2) }}</div>
                                    <small class="text-muted">{{ $it->destination->name ?? '' }} |
                                        {{ $it->duration_days }}D</small>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark"><i
                                            class="bi bi-person-badge me-1"></i>{{ $it->user->name ?? 'System' }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $it->user->role ?? 'admin' }}</div>
                                </td>
                                <td>
                                    @php
                                        $fStatus = $it->followup_status ?? 'leads';
                                        $fBadge = 'bg-secondary';
                                        if ($fStatus == 'interested')
                                            $fBadge = 'bg-info';
                                        if ($fStatus == 'converted')
                                            $fBadge = 'bg-success';
                                        if ($fStatus == 'waiting')
                                            $fBadge = 'bg-warning text-dark';
                                    @endphp
                                    <span class="badge {{ $fBadge }} mb-1"
                                        style="font-size: 0.65rem;">{{ strtoupper($fStatus) }}</span>
                                    @if($it->next_followup_date)
                                        <div class="small text-danger fw-bold" style="font-size: 0.65rem;">
                                            Next: {{ $it->next_followup_date->format('d M') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm border rounded">
                                        <a href="{{ route('admin.b2c-itineraries.edit', $it->id) }}"
                                            class="btn btn-sm btn-white" title="Edit Builder">
                                            <i class="bi bi-pencil-square text-primary"></i>
                                        </a>
                                        <a href="{{ route('admin.b2c-itineraries.pdf', $it->id) }}" class="btn btn-sm btn-white"
                                            title="PDF">
                                            <i class="bi bi-file-pdf text-danger"></i>
                                        </a>
                                        <form action="{{ route('admin.b2c-itineraries.destroy', $it->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-white"
                                                onclick="return confirm('Delete Lead?')"><i
                                                    class="bi bi-trash text-muted"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No B2C leads captured yet.</td>
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
@endsection