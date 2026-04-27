@extends('layouts.admin')

@section('title', 'Website Content Management')

@section('content')
<div class="page-header bg-white p-4 rounded shadow-sm mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1 text-dark fw-bold"><i class="bi bi-layout-text-window-reverse me-2 text-primary"></i>Website Management</h1>
            <p class="text-muted mb-0">Manage homepage banners, site content, and general settings.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Banner Management -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-image me-2 text-primary"></i>Homepage Banners</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                    <i class="bi bi-plus-lg me-1"></i> Add New Banner
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Preview</th>
                                <th>Details</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banners as $banner)
                            <tr>
                                <td class="ps-4">
                                    <img src="{{ asset('storage/' . $banner->image) }}" class="rounded shadow-sm" style="width: 120px; height: 60px; object-fit: cover;">
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $banner->title }}</div>
                                    <small class="text-muted">{{ Str::limit($banner->subtitle, 40) }}</small>
                                </td>
                                <td>{{ $banner->order }}</td>
                                <td>
                                    @if($banner->status)
                                        <span class="badge bg-success-soft text-success">Active</span>
                                    @else
                                        <span class="badge bg-danger-soft text-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editBannerModal{{ $banner->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.website.banners.destroy', $banner->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this banner?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Banner Modal -->
                            <div class="modal fade" id="editBannerModal{{ $banner->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('admin.website.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header border-bottom-0">
                                                <h5 class="modal-title fw-bold">Edit Banner</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Title</label>
                                                    <input type="text" name="title" class="form-control" value="{{ $banner->title }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Subtitle</label>
                                                    <textarea name="subtitle" class="form-control" rows="2">{{ $banner->subtitle }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Link (Optional)</label>
                                                    <input type="text" name="link" class="form-control" value="{{ $banner->link }}">
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Order</label>
                                                        <input type="number" name="order" class="form-control" value="{{ $banner->order }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3 d-flex align-items-end">
                                                        <div class="form-check form-switch mb-2">
                                                            <input class="form-check-input" type="checkbox" name="status" id="status{{ $banner->id }}" {{ $banner->status ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold" for="status{{ $banner->id }}">Active Status</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label fw-bold">Change Image</label>
                                                    <input type="file" name="image" class="form-control">
                                                    <small class="text-muted">Leave empty to keep current image.</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Banner</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No banners found. Click "Add New Banner" to start.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- General Settings -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-gear me-2 text-primary"></i>General Content</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.website.settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">About Us Content</label>
                        <textarea name="about_us" class="form-control" rows="6" placeholder="Describe your company...">{{ $settings['about_us'] ?? '' }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? '' }}" placeholder="info@tourliz.com">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ $settings['contact_phone'] ?? '' }}" placeholder="+1 234 567 890">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Office Address</label>
                        <textarea name="contact_address" class="form-control" rows="3" placeholder="Global HQ, Travel Street...">{{ $settings['contact_address'] ?? '' }}</textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary shadow-sm">Save All Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Banner Modal -->
<div class="modal fade" id="addBannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.website.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Add New Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Escape to Paradise" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subtitle</label>
                        <textarea name="subtitle" class="form-control" rows="2" placeholder="e.g. Discover crystal clear waters..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Link (Optional)</label>
                        <input type="text" name="link" class="form-control" placeholder="e.g. /packages/maldives">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Order</label>
                        <input type="number" name="order" class="form-control" value="0">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Banner Image</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
</style>
@endsection
