@extends('layouts.admin')

@section('title', 'Create Marketing Campaign')

@section('content')
<div class="page-header"><h2><i class="bi bi-megaphone me-2 text-primary"></i>Create Marketing Campaign</h2><p class="text-muted mb-0">The poster is attached manually; no itinerary links are included.</p></div>
<form method="POST" action="{{ route('admin.marketing-campaigns.store') }}">@csrf
<div class="row g-4">
    <div class="col-lg-5"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="mb-3"><label class="form-label fw-bold">Campaign Name *</label><input name="name" class="form-control" required value="{{ old('name') }}" placeholder="Malaysia Holiday Promotion"></div>
        <div class="mb-3"><label class="form-label fw-bold">Fixed Itinerary *</label><select name="fixed_itinerary_id" id="campaignItinerary" class="form-select" required><option value="">Select itinerary</option>@foreach($fixedItineraries as $item)<option value="{{ $item->id }}" data-image="{{ $item->image ? asset('storage/' . $item->image) : '' }}" {{ old('fixed_itinerary_id') == $item->id ? 'selected' : '' }}>{{ $item->title }} — {{ $item->currency }} {{ number_format($item->fixed_price, 2) }}</option>@endforeach</select></div>
        <div id="posterPreview" class="mb-3 d-none"><div class="small fw-bold mb-1">Poster to attach manually</div><img class="img-fluid rounded border" style="max-height:220px" alt="Package poster"></div>
        <div class="mb-3"><label class="form-label fw-bold">Message Format *</label><div class="d-flex gap-3"><label><input type="radio" name="message_mode" value="summary" checked> Short Summary</label><label><input type="radio" name="message_mode" value="full" {{ old('message_mode') === 'full' ? 'checked' : '' }}> Full Itinerary</label></div></div>
        <div class="mb-3"><label class="form-label fw-bold">Intro Message</label><textarea name="intro_message" rows="3" class="form-control" placeholder="We have a special holiday package for you.">{{ old('intro_message') }}</textarea></div>
    </div></div></div>
    <div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex justify-content-between"><strong>Select Guests *</strong><label class="small"><input type="checkbox" id="selectAllContacts"> Select all</label></div><div class="card-body">
        <input id="contactSearch" class="form-control mb-3" placeholder="Search guest or phone">
        <div id="contactList" style="max-height:520px;overflow:auto">
        @forelse($contacts as $contact)<label class="contact-row d-flex gap-2 border-bottom py-2"><input type="checkbox" name="recipients[]" value="{{ $contact['key'] }}" {{ in_array($contact['key'], old('recipients', [])) ? 'checked' : '' }}><span><strong>{{ $contact['name'] }}</strong><small class="d-block text-muted">{{ $contact['phone'] }} · {{ strtoupper($contact['source_type']) }}</small></span></label>@empty<p class="text-muted">No guest phone numbers found.</p>@endforelse
        </div>
    </div></div><button class="btn btn-primary btn-lg w-100 mt-3"><i class="bi bi-list-check me-1"></i>Create Sending Queue</button></div>
</div></form>
@endsection

@push('scripts')<script>
const itinerarySelect = document.getElementById('campaignItinerary');
function showPoster(){ const image=itinerarySelect.options[itinerarySelect.selectedIndex]?.dataset.image; const box=document.getElementById('posterPreview'); const img=box.querySelector('img'); box.classList.toggle('d-none',!image); if(image) img.src=image; }
itinerarySelect.addEventListener('change',showPoster); showPoster();
document.getElementById('selectAllContacts').addEventListener('change',function(){ document.querySelectorAll('input[name="recipients[]"]').forEach(input=>input.checked=this.checked); });
document.getElementById('contactSearch').addEventListener('input',function(){ const term=this.value.toLowerCase(); document.querySelectorAll('.contact-row').forEach(row=>row.classList.toggle('d-none',!row.textContent.toLowerCase().includes(term))); });
</script>@endpush
