@extends('layouts.admin')

@section('title', 'Notification Campaigns')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h2><i class="bi bi-bell-fill me-2 text-primary"></i>Notification Campaigns</h2>
        <p class="text-muted mb-0">Send a custom in-app and device-push announcement to your Tourliz staff.</p>
    </div>
    <a href="{{ route('admin.notification-campaigns.create') }}" class="btn btn-primary"><i class="bi bi-send-plus me-1"></i>New Notification</a>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Notification</th><th>Audience</th><th>In-app</th><th>Device push</th><th>Sent by</th><th></th></tr></thead>
            <tbody>
            @forelse($campaigns as $campaign)
                <tr>
                    <td><strong>{{ $campaign->title }}</strong><div class="small text-muted text-truncate" style="max-width:360px">{{ $campaign->message }}</div></td>
                    <td>{{ $campaign->recipient_count }} staff</td>
                    <td><span class="badge bg-primary">{{ $campaign->in_app_count }}</span></td>
                    <td><span class="badge bg-{{ $campaign->push_count ? 'success' : 'secondary' }}">{{ $campaign->push_count }}</span></td>
                    <td>{{ $campaign->creator->name ?? 'System' }}<div class="small text-muted">{{ optional($campaign->sent_at)->format('d M Y, h:i A') ?: 'Sending' }}</div></td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.notification-campaigns.show', $campaign) }}">View</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No custom notifications have been sent yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($campaigns->hasPages())<div class="card-footer bg-white">{{ $campaigns->links() }}</div>@endif
</div>
@endsection
