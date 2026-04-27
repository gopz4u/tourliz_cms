@extends('layouts.admin')

@section('title', 'Edit Coupon')

@section('content')
    <div class="page-header">
        <h1 class="mb-0"><i class="bi bi-tag"></i> Edit Coupon</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="code" class="form-label">Coupon Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code"
                            value="{{ old('code', $coupon->code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount
                            </option>
                            <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>
                                Percentage</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('value') is-invalid @enderror"
                            id="value" name="value" value="{{ old('value', $coupon->value) }}" required min="0">
                        <div class="form-text">Amount discount or percentage value.</div>
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="min_spend" class="form-label">Minimum Spend</label>
                        <input type="number" step="0.01" class="form-control @error('min_spend') is-invalid @enderror"
                            id="min_spend" name="min_spend" value="{{ old('min_spend', $coupon->min_spend) }}" min="0">
                        <div class="form-text">Minimum transaction amount required (optional).</div>
                        @error('min_spend')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="usage_limit" class="form-label">Usage Limit</label>
                        <input type="number" class="form-control @error('usage_limit') is-invalid @enderror"
                            id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}"
                            min="1">
                        <div class="form-text">Total number of times this coupon can be used (optional).</div>
                        @error('usage_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date"
                            name="start_date"
                            value="{{ old('start_date', $coupon->start_date ? $coupon->start_date->format('Y-m-d') : '') }}">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date"
                            name="end_date"
                            value="{{ old('end_date', $coupon->end_date ? $coupon->end_date->format('Y-m-d') : '') }}">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">Update Coupon</button>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection