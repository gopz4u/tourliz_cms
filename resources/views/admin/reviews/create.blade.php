@extends('layouts.admin')

@section('title', 'Add New Review')

@section('content')
    <div class="page-header mb-4">
        <h1 class="mb-0"><i class="bi bi-star"></i> Add New Review</h1>
        <p class="text-muted">Create a new customer review or testimonial</p>
    </div>

    <form action="{{ route('admin.reviews.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header font-bold">Review Details</div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold small text-uppercase tracking-wider">Customer Name</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold small text-uppercase tracking-wider">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-bold small text-uppercase tracking-wider">Review Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. Amazing Experience!">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-bold small text-uppercase tracking-wider">Review Description</label>
                            <textarea name="comment" class="form-control" rows="5" required>{{ old('comment') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header font-bold">Rating & Relations</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-bold small text-uppercase tracking-wider">Star Rating (1-5)</label>
                            <select name="rating" class="form-select" required>
                                @for($i=5; $i>=1; $i--)
                                    <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                                @endfor
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-bold small text-uppercase tracking-wider">Related Package</label>
                            <select name="package_id" class="form-select select2">
                                <option value="">-- None --</option>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-bold small text-uppercase tracking-wider">Related Destination</label>
                            <select name="destination_id" class="form-select select2">
                                <option value="">-- None --</option>
                                @foreach($destinations as $destination)
                                    <option value="{{ $destination->id }}" {{ old('destination_id') == $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-bold small text-uppercase tracking-wider">Customer Photo</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_testimonial" value="1" id="is_testimonial">
                            <label class="form-check-label font-bold" for="is_testimonial">Mark as Testimonial</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-bold small text-uppercase tracking-wider">Status</label>
                            <select name="status" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="approved" selected>Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary w-100">Save Review</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
