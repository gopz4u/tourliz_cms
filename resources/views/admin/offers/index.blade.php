@extends('layouts.admin')

@section('title', 'Package Offers & Add-ons')

@section('content')
<div class="page-header bg-white p-4 rounded shadow-sm mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1 text-dark fw-bold"><i class="bi bi-gift me-2 text-primary"></i>Package Offers</h1>
            <p class="text-muted mb-0">Manage special deals, discounts, and add-ons for your travel packages.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfferModal">
            <i class="bi bi-plus-lg me-1"></i> Create New Offer
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Offer Details</th>
                                <th>Type</th>
                                <th>Applies To</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($offers as $offer)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 me-3 text-primary overflow-hidden" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                            @if($offer->image)
                                                <img src="{{ $offer->image_url }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <i class="fa-solid {{ $offer->icon ?: 'fa-tag' }} fs-5"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $offer->title }}</div>
                                            @if($offer->discount_value)
                                                <div class="text-success small fw-bold">
                                                    {{ $offer->discount_type == 'percentage' ? $offer->discount_value . '%' : '₹' . number_format($offer->discount_value, 2) }} Off
                                                </div>
                                            @endif
                                            <small class="text-muted">{{ Str::limit($offer->description, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-soft text-info text-capitalize">{{ $offer->type }}</span>
                                </td>
                                <td>
                                    @if($offer->is_global)
                                        <span class="badge bg-primary">Global</span>
                                    @elseif($offer->countries)
                                        <span class="badge bg-warning-soft text-warning">{{ count($offer->countries) }} Countries</span>
                                    @else
                                        <span class="badge bg-light text-dark">{{ $offer->packages->count() }} Packages</span>
                                    @endif
                                </td>
                                <td>
                                    @if($offer->status)
                                        <span class="badge bg-success-soft text-success">Active</span>
                                    @else
                                        <span class="badge bg-danger-soft text-danger">Inactive</span>
                                    @endif
                                    @if($offer->expires_at)
                                        <div class="mt-1 small text-muted">
                                            <i class="bi bi-clock-history me-1"></i>Exp: {{ $offer->expires_at->format('d M') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editOfferModal{{ $offer->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.package-offers.destroy', $offer->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this offer?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Offer Modal -->
                            <div class="modal fade" id="editOfferModal{{ $offer->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('admin.package-offers.update', $offer->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Offer</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-8 mb-3">
                                                        <label class="form-label fw-bold">Offer Title</label>
                                                        <input type="text" name="title" class="form-control" value="{{ $offer->title }}" required>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-bold">Offer Type</label>
                                                        <select name="type" class="form-select">
                                                            <option value="discount" {{ $offer->type == 'discount' ? 'selected' : '' }}>Discount</option>
                                                            <option value="add-on" {{ $offer->type == 'add-on' ? 'selected' : '' }}>Add-on Service</option>
                                                            <option value="freebie" {{ $offer->type == 'freebie' ? 'selected' : '' }}>Freebie</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Discount Value (Optional)</label>
                                                        <input type="number" step="0.01" name="discount_value" class="form-control" value="{{ $offer->discount_value }}" placeholder="e.g. 10 or 500">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Discount Type</label>
                                                        <select name="discount_type" class="form-select">
                                                            <option value="">None</option>
                                                            <option value="percentage" {{ $offer->discount_type == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                                            <option value="fixed" {{ $offer->discount_type == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Published Date</label>
                                                        <input type="datetime-local" name="published_at" class="form-control" value="{{ $offer->published_at ? $offer->published_at->format('Y-m-d\TH:i') : '' }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Expiry Date</label>
                                                        <input type="datetime-local" name="expires_at" class="form-control" value="{{ $offer->expires_at ? $offer->expires_at->format('Y-m-d\TH:i') : '' }}">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Description</label>
                                                    <textarea name="description" class="form-control" rows="3">{{ $offer->description }}</textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Featured Photo (Optional)</label>
                                                        <input type="file" name="image" class="form-control">
                                                        @if($offer->image)
                                                            <div class="mt-2">
                                                                <img src="{{ $offer->image_url }}" class="rounded shadow-sm" style="height: 60px;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Icon Class (FontAwesome)</label>
                                                        <input type="text" name="icon" class="form-control" value="{{ $offer->icon }}" placeholder="fa-spa, fa-utensils, etc.">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 mb-3 d-flex align-items-end gap-4">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="is_global" id="is_global{{ $offer->id }}" {{ $offer->is_global ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold" for="is_global{{ $offer->id }}">Global Offer</label>
                                                        </div>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="status" id="status{{ $offer->id }}" {{ $offer->status ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold" for="status{{ $offer->id }}">Active</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" id="scopeSelection{{ $offer->id }}" style="{{ $offer->is_global ? 'display:none' : '' }}">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Select Countries (Scope)</label>
                                                        <select name="countries[]" class="form-select select2" multiple style="width:100%">
                                                            @foreach($countries as $country)
                                                                <option value="{{ $country }}" {{ is_array($offer->countries) && in_array($country, $offer->countries) ? 'selected' : '' }}>{{ $country }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Specific Packages (Optional)</label>
                                                        <select name="packages[]" class="form-select select2" multiple style="width:100%">
                                                            @foreach($packages as $pkg)
                                                                <option value="{{ $pkg->id }}" {{ $offer->packages->contains($pkg->id) ? 'selected' : '' }}>{{ $pkg->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Offer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No offers created yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Offer Modal -->
<div class="modal fade" id="addOfferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.package-offers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create New Offer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Offer Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. 50% Off Spa Treatment" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Offer Type</label>
                            <select name="type" class="form-select">
                                <option value="discount">Discount</option>
                                <option value="add-on">Add-on Service</option>
                                <option value="freebie">Freebie</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Discount Value (Optional)</label>
                            <input type="number" step="0.01" name="discount_value" class="form-control" placeholder="e.g. 10 or 500">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Discount Type</label>
                            <select name="discount_type" class="form-select">
                                <option value="">None</option>
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (₹)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Published Date</label>
                            <input type="datetime-local" name="published_at" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Expiry Date</label>
                            <input type="datetime-local" name="expires_at" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe the offer details..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Featured Photo (Optional)</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Icon Class (FontAwesome)</label>
                            <input type="text" name="icon" class="form-control" placeholder="fa-spa, fa-utensils, etc.">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3 d-flex align-items-end gap-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_global" id="is_global_new">
                                <label class="form-check-label fw-bold" for="is_global_new">Global Offer</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="status_new" checked>
                                <label class="form-check-label fw-bold" for="status_new">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="scopeSelectionNew">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Select Countries (Scope)</label>
                            <select name="countries[]" class="form-select select2" multiple style="width:100%">
                                @foreach($countries as $country)
                                    <option value="{{ $country }}">{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Specific Packages (Optional)</label>
                            <select name="packages[]" class="form-select select2" multiple style="width:100%">
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->id }}">{{ $pkg->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Offer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle scope selection based on global switch
        $('#is_global_new').on('change', function() {
            if($(this).is(':checked')) {
                $('#scopeSelectionNew').fadeOut();
            } else {
                $('#scopeSelectionNew').fadeIn();
            }
        });

        @foreach($offers as $offer)
        $('#is_global{{ $offer->id }}').on('change', function() {
            if($(this).is(':checked')) {
                $('#scopeSelection{{ $offer->id }}').fadeOut();
            } else {
                $('#scopeSelection{{ $offer->id }}').fadeIn();
            }
        });
        @endforeach
    });
</script>
@endpush

<style>
    .bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
</style>
@endsection
