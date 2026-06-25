@extends('layouts.admin')

@section('title', 'New Group Proposal')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-plus-circle me-2"></i>New Group Proposal</h2>
                <p class="text-muted mb-0">Capture group requirements and generate a dynamic proposal</p>
            </div>
            <a href="{{ route('admin.group-itineraries.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.group-itineraries.store') }}" method="POST">
                        @csrf

                        <h5 class="mb-4 border-bottom pb-2">Group Basic Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Group / Lead Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="client_name" class="form-control"
                                    placeholder="e.g. Acme Corp Group" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Primary Phone / WhatsApp <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="+91 ..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="client@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Lead Source</label>
                                <select name="lead_source" class="form-select">
                                    <option value="walk_in">Walk-in Customer</option>
                                    <option value="phone">Phone Enquiry</option>
                                    <option value="social">Social Media (FB/Insta)</option>
                                    <option value="reference">Reference</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="mt-5 mb-4 border-bottom pb-2">Trip Requirements</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">Trip Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="e.g. 5 Days Dubai Family Tour" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Base Destination (Country) <span
                                        class="text-danger">*</span></label>
                                <select name="destination_id" class="form-select" required>
                                    <option value="">Select Destination</option>
                                    @foreach($destinations as $destination)
                                        <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Arrival Date (Start)</label>
                                <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Duration (Days) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="duration_days" class="form-control" value="5" min="1" required>
                            </div>

                            <div class="col-12 mt-2">
                                <label class="form-label fw-bold small text-muted">Additional Countries Covered</label>
                                <div class="row g-2" style="max-height: 120px; overflow-y: auto;">
                                    @foreach($destinations as $dest)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="country_ids[]"
                                                    value="{{ $dest->id }}" id="grp_country_{{ $dest->id }}">
                                                <label class="form-check-label small"
                                                    for="grp_country_{{ $dest->id }}">{{ $dest->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label class="form-label fw-bold small text-muted">Assigned To (Managed By)</label>
                                <select name="user_id" class="form-select">
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ old('user_id', auth()->id()) == $admin->id ? 'selected' : '' }}>
                                            {{ $admin->name }} ({{ $admin->role }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 shadow">
                                <i class="bi bi-magic me-2"></i>Generate Draft Itinerary
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection