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
<div class="col-lg-8"><div class="alert alert-info small"><i class="bi bi-info-circle me-1"></i><strong>Manual mode:</strong> Open WhatsApp, attach the poster, then press Send inside WhatsApp. If the prepared text does not appear, use Copy Message.</div><div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex justify-content-between"><strong>Manual Sending Queue</strong><span>{{ $marketingCampaign->recipients->where('status','sent')->count() }} / {{ $marketingCampaign->recipients->count() }} sent</span></div><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Guest</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@foreach($marketingCampaign->recipients as $recipient)<tr>
    @php
        $personalMessage = str_replace('{name}', $recipient->name ?: 'Guest', $messageTemplate);
        $whatsappNumber = preg_replace('/\D+/', '', $recipient->phone);
        $whatsappUrl = 'https://api.whatsapp.com/send?phone=' . $whatsappNumber . '&text=' . rawurlencode($personalMessage);
    @endphp
    <td><strong>{{ $recipient->name ?: 'Guest' }}</strong><div class="small text-muted">{{ $recipient->phone }}</div></td>
    <td><span class="badge bg-{{ ['sent'=>'success','failed'=>'danger','skipped'=>'secondary','pending'=>'warning'][$recipient->status] ?? 'secondary' }}">{{ ucfirst($recipient->status) }}</span></td>
    <td><div class="d-flex flex-wrap gap-1">
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-success"><i class="bi bi-whatsapp"></i> Open WhatsApp</a>
        <button type="button" class="btn btn-sm btn-outline-primary copy-recipient-message" data-message="{{ base64_encode($personalMessage) }}"><i class="bi bi-copy"></i> Copy</button>
        @foreach(['sent'=>'Sent','skipped'=>'Skip','failed'=>'Failed'] as $status=>$label)<form method="POST" action="{{ route('admin.marketing-campaigns.recipients.update', [$marketingCampaign, $recipient]) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $status }}"><button class="btn btn-sm btn-outline-{{ $status === 'sent' ? 'success' : ($status === 'failed' ? 'danger' : 'secondary') }}">{{ $label }}</button></form>@endforeach
    </div></td>
</tr>@endforeach
</tbody></table></div></div></div>
</div>
@endsection

@push('scripts')<script>
document.querySelectorAll('.copy-recipient-message').forEach(button => button.addEventListener('click', async function () {
    const message = decodeURIComponent(escape(atob(this.dataset.message)));
    await navigator.clipboard.writeText(message);
    const original = this.innerHTML;
    this.innerHTML = '<i class="bi bi-check-lg"></i> Copied';
    setTimeout(() => this.innerHTML = original, 1500);
}));
</script>@endpush
