@php $departure = $groupDeparture ?? null; @endphp
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Fixed itinerary template *</label>
        <select name="fixed_itinerary_id" class="form-select" required>
            <option value="">Choose an itinerary</option>
            @foreach($fixedItineraries as $template)
                <option value="{{ $template->id }}" {{ old('fixed_itinerary_id', $departure->fixed_itinerary_id ?? $selected->id ?? '') == $template->id ? 'selected' : '' }}>{{ $template->title }} — {{ $template->duration }}</option>
            @endforeach
        </select>
        <div class="form-text">The downloadable day-by-day itinerary comes from this template.</div>
    </div>
    <div class="col-md-4"><label class="form-label">Display title</label><input name="title" class="form-control" value="{{ old('title', $departure->title ?? $selected->title ?? '') }}" placeholder="Uses template title if blank"></div>
    <div class="col-md-4"><label class="form-label">Announcement date</label><input type="date" name="announcement_date" class="form-control" value="{{ old('announcement_date', optional($departure->announcement_date ?? null)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"></div>
    <div class="col-md-4"><label class="form-label">Departure date *</label><input type="date" name="departure_date" class="form-control" required value="{{ old('departure_date', optional($departure->departure_date ?? null)->format('Y-m-d')) }}"></div>
    <div class="col-md-4"><label class="form-label">Return date</label><input type="date" name="return_date" class="form-control" value="{{ old('return_date', optional($departure->return_date ?? null)->format('Y-m-d')) }}"></div>
    <div class="col-md-3"><label class="form-label">Currency *</label><select name="currency" class="form-select">@foreach(['MYR','INR','USD','SGD','AED'] as $currency)<option {{ old('currency', $departure->currency ?? $selected->currency ?? 'MYR') === $currency ? 'selected' : '' }}>{{ $currency }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">Adult rate *</label><input type="number" step="0.01" min="0" name="adult_rate" class="form-control" required value="{{ old('adult_rate', $departure->adult_rate ?? $selected->fixed_price ?? 0) }}"></div>
    <div class="col-md-3"><label class="form-label">Child rate</label><input type="number" step="0.01" min="0" name="child_rate" class="form-control" value="{{ old('child_rate', $departure->child_rate ?? '') }}"></div>
    <div class="col-md-3"><label class="form-label">Capacity *</label><input type="number" min="1" name="capacity" class="form-control" required value="{{ old('capacity', $departure->capacity ?? 20) }}"></div>
    <div class="col-md-4"><label class="form-label">Status *</label><select name="status" class="form-select">@foreach(['draft','announced','open','sold_out','completed','cancelled'] as $status)<option value="{{ $status }}" {{ old('status', $departure->status ?? 'announced') === $status ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $status)) }}</option>@endforeach</select></div>
    <div class="col-md-8"><label class="form-label">Internal notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $departure->notes ?? '') }}</textarea></div>
</div>
