@extends('layouts.admin')

@section('title', 'New Notification')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div><h2><i class="bi bi-send-fill me-2 text-primary"></i>New Notification</h2><p class="text-muted mb-0">This is sent immediately to the notification bell and registered staff devices.</p></div>
    <a href="{{ route('admin.notification-campaigns.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<form method="POST" action="{{ route('admin.notification-campaigns.store') }}">@csrf
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm"><div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Title *</label>
                    <input name="title" maxlength="120" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Example: Tomorrow's sales meeting" required autofocus>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description *</label>
                    <textarea name="message" rows="5" maxlength="1000" class="form-control @error('message') is-invalid @enderror" placeholder="Write the message staff should see on their device." required>{{ old('message') }}</textarea>
                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label fw-bold">Open link <span class="text-muted fw-normal">(optional)</span></label>
                    <input name="url" maxlength="2048" class="form-control @error('url') is-invalid @enderror" value="{{ old('url') }}" placeholder="/admin/follow-ups">
                    <div class="form-text">Use a Tourliz path such as <code>/admin/follow-ups</code>. Clicking the notification opens this page.</div>
                    @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div></div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm"><div class="card-header bg-white fw-bold">Recipients *</div><div class="card-body">
                <div class="form-check mb-2"><input class="form-check-input recipient-scope" type="radio" name="recipient_scope" id="allRecipients" value="all" {{ old('recipient_scope', 'all') === 'all' ? 'checked' : '' }}><label class="form-check-label" for="allRecipients">All Tourliz staff ({{ $admins->count() }})</label></div>
                <div class="form-check mb-3"><input class="form-check-input recipient-scope" type="radio" name="recipient_scope" id="selectedRecipients" value="selected" {{ old('recipient_scope') === 'selected' ? 'checked' : '' }}><label class="form-check-label" for="selectedRecipients">Choose staff members</label></div>
                <div id="recipientList" class="border rounded p-2" style="max-height:300px; overflow:auto">
                    @forelse($admins as $admin)
                        <label class="d-flex align-items-start gap-2 border-bottom py-2 last-child-border-0">
                            <input class="form-check-input mt-1 recipient-checkbox" type="checkbox" name="recipient_ids[]" value="{{ $admin->id }}" {{ in_array($admin->id, old('recipient_ids', [])) ? 'checked' : '' }}>
                            <span><strong>{{ $admin->name }}</strong><small class="d-block text-muted">{{ $admin->email }} · {{ ucfirst(str_replace('_', ' ', $admin->role ?: 'admin')) }}</small></span>
                        </label>
                    @empty
                        <p class="text-muted mb-0">No staff accounts are available.</p>
                    @endforelse
                </div>
                @error('recipient_ids')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
            </div></div>
            <button class="btn btn-primary btn-lg w-100 mt-3"><i class="bi bi-send-fill me-1"></i>Send Notification Now</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('recipientList');
    const scopes = document.querySelectorAll('.recipient-scope');
    const refresh = () => {
        const selected = document.getElementById('selectedRecipients').checked;
        list.classList.toggle('opacity-50', !selected);
        document.querySelectorAll('.recipient-checkbox').forEach(item => item.disabled = !selected);
    };
    scopes.forEach(item => item.addEventListener('change', refresh));
    refresh();
});
</script>
@endpush
