@extends('layouts.admin')

@section('title', 'Edit Country')

@section('content')
    <div class="page-header">
        <h1 class="mb-0"><i class="bi bi-pencil"></i> Edit Country</h1>
        <p class="text-muted mb-0">Update country information</p>
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

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.countries.update', $country->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Country Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $country->name) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Country Code</label>
                        <input type="text" class="form-control" id="code" name="code"
                            value="{{ old('code', $country->code) }}" placeholder="e.g. IN, US">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ $country->status ? 'checked' : '' }}>
                        <label class="form-check-label" for="status">Status (Active)</label>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.countries.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Country
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection