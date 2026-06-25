@extends('layouts.admin')

@section('title', 'Edit Fixed Itinerary')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="bi bi-pencil-square me-2"></i>Edit Fixed Itinerary</h2>
            <p class="text-muted mb-0">{{ $itinerary->title }}</p>
        </div>
        <a href="{{ route('admin.fixed-itineraries.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to List
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('admin.fixed-itineraries.update', $itinerary->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Itinerary Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $itinerary->title) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Supplier / Vendor</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">— Select Supplier —</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id', $itinerary->supplier_id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Fixed Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $itinerary->currency }}</span>
                                <input type="number" name="fixed_price" class="form-control" step="0.01" min="0"
                                    value="{{ old('fixed_price', $itinerary->fixed_price) }}" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Countries Covered</label>
                            <div class="row g-2" style="max-height: 200px; overflow-y: auto;">
                                @foreach($countries as $country)
                                @php $selectedIds = old('country_ids', $itinerary->country_ids ?? []); @endphp
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="country_ids[]" value="{{ $country->id }}"
                                            id="country_{{ $country->id }}"
                                            {{ in_array($country->id, $selectedIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="country_{{ $country->id }}">{{ $country->name }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Itinerary Description</label>
                            <textarea name="itinerary_description" class="form-control" rows="8"
                                placeholder="Describe the fixed itinerary here...">{{ old('itinerary_description', $itinerary->itinerary_description) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $itinerary->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $itinerary->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-top pt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-2"></i>Update Fixed Itinerary
                        </button>
                        <a href="{{ route('admin.fixed-itineraries.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
