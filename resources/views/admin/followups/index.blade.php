@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="mb-1">Follow-up Queue</h4>
            <p class="text-muted mb-0">Active sales leads only. Reminders are sent at 12:30 PM India time.</p>
        </div>
        <a href="{{ route('admin.b2c-itineraries.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Lead</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><a class="card text-decoration-none {{ $filter === 'due' ? 'border-danger' : '' }}" href="{{ route('admin.follow-ups.index', ['filter' => 'due']) }}"><div class="card-body"><div class="text-muted small">Due or unscheduled</div><div class="fs-3 fw-bold text-danger">{{ $dueCount + $unscheduledCount }}</div></div></a></div>
        <div class="col-md-4"><a class="card text-decoration-none {{ $filter === 'upcoming' ? 'border-primary' : '' }}" href="{{ route('admin.follow-ups.index', ['filter' => 'upcoming']) }}"><div class="card-body"><div class="text-muted small">Upcoming</div><div class="fs-3 fw-bold text-primary">{{ $upcomingCount }}</div></div></a></div>
        <div class="col-md-4"><a class="card text-decoration-none {{ $filter === 'all' ? 'border-secondary' : '' }}" href="{{ route('admin.follow-ups.index', ['filter' => 'all']) }}"><div class="card-body"><div class="text-muted small">View</div><div class="fs-5 fw-bold">All active leads</div></div></a></div>
    </div>

    <form class="row g-2 mb-3" method="GET" action="{{ route('admin.follow-ups.index') }}">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <div class="col-md-5"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Customer, trip, or phone"></div>
        <div class="col-md-3"><select name="assigned_to" class="form-select"><option value="">All sales staff</option>@foreach($admins as $admin)<option value="{{ $admin->id }}" {{ (string) request('assigned_to') === (string) $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Lead</th><th>Assigned</th><th>Stage</th><th>Next reminder</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($leads as $lead)
                    @php($isDue = !$lead->next_followup_at || $lead->next_followup_at->lte(now()))
                    <tr>
                        <td><div class="fw-semibold">{{ $lead->client_name }}</div><div class="small text-muted">{{ $lead->title }} · {{ $lead->phone ?: 'No phone' }}</div></td>
                        <td>{{ $lead->user->name ?? 'Unassigned' }}</td>
                        <td><span class="badge bg-secondary">{{ strtoupper(str_replace('_', ' ', $lead->followup_status ?: 'leads')) }}</span></td>
                        <td>{!! $lead->next_followup_at ? '<span class="' . ($isDue ? 'text-danger fw-bold' : '') . '">' . e($lead->next_followup_at->copy()->timezone('Asia/Kolkata')->format('d M Y, h:i A')) . ' IST</span>' : '<span class="text-warning fw-bold">Needs scheduling</span>' !!}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.b2c-itineraries.edit', $lead->id) }}" class="btn btn-sm btn-outline-secondary">Open</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.follow-ups.complete', $lead->id) }}">@csrf<button class="btn btn-sm btn-primary">Done</button></form>
                            <form class="d-inline" method="POST" action="{{ route('admin.follow-ups.snooze', $lead->id) }}">@csrf<input type="hidden" name="date" value="{{ now('Asia/Kolkata')->addDay()->toDateString() }}"><button class="btn btn-sm btn-outline-primary">Tomorrow</button></form>
                            <form class="d-inline" method="POST" action="{{ route('admin.follow-ups.close', $lead->id) }}">@csrf<input type="hidden" name="status" value="confirmed"><button class="btn btn-sm btn-success">Confirm</button></form>
                            <form class="d-inline" method="POST" action="{{ route('admin.follow-ups.close', $lead->id) }}">@csrf<input type="hidden" name="status" value="cancelled"><button class="btn btn-sm btn-outline-danger">Cancel</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No active leads in this queue.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())<div class="card-footer">{{ $leads->links() }}</div>@endif
    </div>
</div>
@endsection
