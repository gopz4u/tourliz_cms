@extends('layouts.admin')

@section('title', $marketingCampaign->name)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center"><div><h2><i class="bi bi-whatsapp text-success me-2"></i>{{ $marketingCampaign->name }}</h2><p class="text-muted mb-0">{{ ucfirst($marketingCampaign->message_mode) }} · {{ $marketingCampaign->fixedItinerary->title }}</p></div><a href="{{ route('admin.marketing-campaigns.index') }}" class="btn btn-outline-secondary">Back</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="row g-4">
<div class="col-lg-4"><div class="card border-0 shadow-sm sticky-top" style="top:85px"><div class="card-header bg-white fw-bold">Message Preview</div><div class="card-body">
    @if($marketingCampaign->fixedItinerary->image)<img src="{{ asset('storage/' . $marketingCampaign->fixedItinerary->image) }}" class="img-fluid rounded border mb-2" alt="Poster"><div class="alert alert-warning py-2 small">Download or save this poster, then attach it manually in WhatsApp.</div>@endif
    <textarea id="messageTemplate" class="form-control small" rows="18" readonly>{{ $messageTemplate }}</textarea>
    <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-2" onclick="navigator.clipboard.writeText(document.getElementById('messageTemplate').value)">Copy Message</button>
</div></div></div>
<div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex justify-content-between"><strong>Manual Sending Queue</strong><span>{{ $marketingCampaign->recipients->where('status','sent')->count() }} / {{ $marketingCampaign->recipients->count() }} sent</span></div><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Guest</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@foreach($marketingCampaign->recipients as $recipient)<tr>
    <td><strong>{{ $recipient->name ?: 'Guest' }}</strong><div class="small text-muted">{{ $recipient->phone }}</div></td>
    <td><span class="badge bg-{{ ['sent'=>'success','failed'=>'danger','skipped'=>'secondary','pending'=>'warning'][$recipient->status] ?? 'secondary' }}">{{ ucfirst($recipient->status) }}</span></td>
    <td><div class="d-flex flex-wrap gap-1">
        <button type="button" class="btn btn-sm btn-success" onclick='sendCampaignMessage(@json($recipient->name ?: "Guest"), @json($recipient->phone))'><i class="bi bi-whatsapp"></i> Open</button>
        @foreach(['sent'=>'Sent','skipped'=>'Skip','failed'=>'Failed'] as $status=>$label)<form method="POST" action="{{ route('admin.marketing-campaigns.recipients.update', [$marketingCampaign, $recipient]) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $status }}"><button class="btn btn-sm btn-outline-{{ $status === 'sent' ? 'success' : ($status === 'failed' ? 'danger' : 'secondary') }}">{{ $label }}</button></form>@endforeach
    </div></td>
</tr>@endforeach
</tbody></table></div></div></div>
</div>
@endsection

@push('scripts')<script>
function sendCampaignMessage(name, phone) {
    const template = document.getElementById('messageTemplate').value;
    window.openWhatsApp(template.replaceAll('{name}', name), phone);
}
</script>@endpush
