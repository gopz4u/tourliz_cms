@extends('layouts.admin')

@section('title', 'Add New Agency')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-plus-circle me-2"></i>Add New Agency</h2>
                <p class="text-muted mb-0">Create a B2B partner account (No login required)</p>
            </div>
            <a href="{{ route('admin.agencies.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Agency Details</h5>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.agencies.store') }}" method="POST">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-muted fw-bold fs-7 mb-3">Company Information</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control"
                                    value="{{ old('company_name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Primary Contact Person</label>
                                <input type="text" name="primary_contact_name" class="form-control"
                                    value="{{ old('primary_contact_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">WhatsApp Number</label>
                                <input type="text" name="whatsapp_number" class="form-control"
                                    value="{{ old('whatsapp_number') }}" placeholder="+1234567890">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-muted fw-bold fs-7 mb-3">Destination Specializations</h6>
                            </div>
                            <div class="col-12">
                                <div class="row g-3">
                                    @foreach($destinations as $destination)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-check p-3 border rounded bg-light position-relative">
                                                <input class="form-check-input" type="checkbox" name="destination_ids[]"
                                                    value="{{ $destination->id }}" id="destination_{{ $destination->id }}"
                                                    {{ in_array($destination->id, old('destination_ids', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label w-100 stretched-link fw-medium"
                                                    for="destination_{{ $destination->id }}">
                                                    {{ $destination->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary px-4 py-2">Create Agency</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection