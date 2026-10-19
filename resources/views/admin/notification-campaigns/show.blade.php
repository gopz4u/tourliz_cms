@extends('layouts.admin')

@section('title', $notificationCampaign->title)

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div><h2><i class="bi bi-bell-fill me-2 text-primary"></i>{{ $notificationCampaign->title }}</h2><p class="text-muted mb-0">Custom staff notification</p></div>
    <div class="d-flex gap-2"><a href="{{ route('admin.notification-campaigns.create') }}" class="btn btn-primary">New Notification</a><a href="{{ route('admin.notification-campaigns.index') }}" class="btn btn-outline-secondary">Back</a></div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="row g-4">
    <div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-header bg-white fw-bold">Message</div><div class="card-body"><p class="mb-0" style="white-space:pre-wrap">{{ $notificationCampaign->message }}</p>@if($notificationCampaign->url)<hr><div class="small text-muted">Opens: <a href="{{ $notificationCampaign->url }}">{{ $notificationCampaign->url }}</a></div>@endif</div></div></div>
    <div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-header bg-white fw-bold">Delivery summary</div><div class="list-group list-group-flush"><div class="list-group-item d-flex justify-content-between"><span>Staff recipients</span><strong>{{ $notificationCampaign->recipient_count }}</strong></div><div class="list-group-item d-flex justify-content-between"><span>In-app notifications</span><strong>{{ $notificationCampaign->in_app_count }}</strong></div><div class="list-group-item d-flex justify-content-between"><span>Device pushes delivered</span><strong>{{ $notificationCampaign->push_count }}</strong></div><div class="list-group-item"><div class="small text-muted">Sent by</div><strong>{{ $notificationCampaign->creator->name ?? 'System' }}</strong><div class="small text-muted mt-1">{{ optional($notificationCampaign->sent_at)->format('d M Y, h:i A') }}</div></div></div></div><p class="small text-muted mt-2 mb-0">A device push is delivered only when that staff member enabled device alerts in Tourliz.</p></div>
</div>
@endsection
