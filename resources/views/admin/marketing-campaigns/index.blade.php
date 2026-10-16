@extends('layouts.admin')

@section('title', 'Marketing Campaigns')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h2><i class="bi bi-megaphone-fill me-2 text-primary"></i>Marketing Campaigns</h2><p class="text-muted mb-0">Manual WhatsApp campaigns using fixed itineraries</p></div>
    <a href="{{ route('admin.marketing-campaigns.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Campaign</a>
</div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>Campaign</th><th>Itinerary</th><th>Mode</th><th>Progress</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse($campaigns as $campaign)
        <tr>
            <td><strong>{{ $campaign->name }}</strong><div class="small text-muted">{{ $campaign->created_at->format('d M Y, h:i A') }}</div></td>
            <td>{{ $campaign->fixedItinerary->title ?? 'Deleted itinerary' }}</td>
            <td><span class="badge bg-info text-dark">{{ ucfirst($campaign->message_mode) }}</span></td>
            <td>{{ $campaign->sent_count }} / {{ $campaign->recipients_count }} sent</td>
            <td><span class="badge bg-{{ $campaign->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($campaign->status) }}</span></td>
            <td class="text-end"><a href="{{ route('admin.marketing-campaigns.show', $campaign) }}" class="btn btn-sm btn-outline-primary">Open Queue</a></td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted py-5">No marketing campaigns yet.</td></tr>
    @endforelse
    </tbody>
</table></div><div class="card-footer bg-white">{{ $campaigns->links() }}</div></div>
@endsection
