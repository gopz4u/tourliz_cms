@extends('layouts.admin')

@section('title', 'Create B2C Itinerary')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 font-weight-bold text-gray-800 mb-1">Create B2C Travel Itinerary</h2>
        <p class="text-muted small mb-0">Add a public travel package with structured day-by-day activities.</p>
    </div>
    <a href="{{ route('admin.itineraries.b2c.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.itineraries.b2c.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <!-- Main Form Left -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">General Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Itinerary Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g. 5 Days Magical Bali Paradise Tour">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Destination <span class="text-danger">*</span></label>
                            <select name="destination_id" class="form-select" required>
                                <option value="">Select Destination</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" {{ old('destination_id') == $dest->id ? 'selected' : '' }}>{{ $dest->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold">Duration (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', 3) }}" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold">Duration (Nights) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_nights" class="form-control" value="{{ old('duration_nights', 2) }}" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="2" placeholder="Brief tagline or overview for cards">{{ old('short_description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Detailed itinerary description">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Structured Day Activities -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold">Day-by-Day Itinerary Schedule</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addDayBtn"><i class="bi bi-plus"></i> Add Day</button>
                </div>
                <div class="card-body" id="daysContainer">
                    <div class="day-card border rounded p-3 mb-3" data-day="1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-primary">Day 1 Schedule</h6>
                            <button type="button" class="btn btn-sm btn-link text-danger remove-day-btn" style="display:none;"><i class="bi bi-trash"></i></button>
                        </div>
                        <input type="hidden" name="days[0][day_number]" value="1">
                        <div class="row g-2 mb-2">
                            <div class="col-md-8">
                                <input type="text" name="days[0][title]" class="form-control" placeholder="Day Title (e.g., Arrival & Sunset Cruise)" value="Arrival & Welcome">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="days[0][overnight_location]" class="form-control" placeholder="Overnight City/Hotel" value="">
                            </div>
                        </div>
                        <textarea name="days[0][description]" class="form-control form-control-sm" rows="2" placeholder="Day description and highlights"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Right -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">Pricing & Status</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Public Selling Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="selling_price" class="form-control" value="{{ old('selling_price', '0.00') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Child Price (₹)</label>
                        <input type="number" step="0.01" name="child_price" class="form-control" value="{{ old('child_price') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="isPublishedCheck" checked>
                        <label class="form-check-label fw-bold" for="isPublishedCheck">
                            Visible on Public Website
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Featured Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">SEO Optimization</div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label small">SEO Meta Title</label>
                        <input type="text" name="seo_title" class="form-control form-control-sm" value="{{ old('seo_title') }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">SEO Meta Description</label>
                        <textarea name="seo_description" class="form-control form-control-sm" rows="2">{{ old('seo_description') }}</textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 font-weight-bold">
                <i class="bi bi-check-circle me-1"></i> Save B2C Itinerary
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    let dayCount = 1;
    document.getElementById('addDayBtn').addEventListener('click', function() {
        dayCount++;
        const index = dayCount - 1;
        const container = document.getElementById('daysContainer');
        const dayHtml = `
            <div class="day-card border rounded p-3 mb-3" data-day="${dayCount}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 text-primary">Day ${dayCount} Schedule</h6>
                    <button type="button" class="btn btn-sm btn-link text-danger remove-day-btn" onclick="this.closest('.day-card').remove();"><i class="bi bi-trash"></i></button>
                </div>
                <input type="hidden" name="days[${index}][day_number]" value="${dayCount}">
                <div class="row g-2 mb-2">
                    <div class="col-md-8">
                        <input type="text" name="days[${index}][title]" class="form-control" placeholder="Day Title">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="days[${index}][overnight_location]" class="form-control" placeholder="Overnight Location">
                    </div>
                </div>
                <textarea name="days[${index}][description]" class="form-control form-control-sm" rows="2" placeholder="Day description"></textarea>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', dayHtml);
    });
</script>
@endpush
@endsection
