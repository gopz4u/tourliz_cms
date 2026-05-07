@extends('layouts.admin')

@section('title', 'Manage Reviews')

@section('content')
<div class="container-fluid p-0">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800"><i class="bi bi-star-fill text-warning me-2"></i>Review Management</h1>
            <p class="text-muted small mb-0">Moderation, bulk actions, and featured content management.</p>
        </div>
        <div>
            <a href="{{ route('admin.reviews.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Manual Review
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, or content..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="rating" class="form-select">
                        <option value="">All Ratings</option>
                        @for($i=5; $i>=1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="featured" class="form-select">
                        <option value="">All Types</option>
                        <option value="yes" {{ request('featured') == 'yes' ? 'selected' : '' }}>Featured Only</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-dark">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions & Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div id="bulkActionsWrapper" class="d-none animate__animated animate__fadeIn">
                <span class="me-3 small fw-bold text-primary"><span id="selectedCount">0</span> reviews selected</span>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="handleBulk('approve')">Approve</button>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="handleBulk('reject')">Reject</button>
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="handleBulk('feature')">Feature</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="handleBulk('delete')">Delete</button>
                </div>
            </div>
            <div class="ms-auto text-muted small">
                Showing {{ $reviews->firstItem() }} to {{ $reviews->lastItem() }} of {{ $reviews->total() }} reviews
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" width="40">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Package / Destination</th>
                            <th>Reviewer</th>
                            <th>Rating & Content</th>
                            <th>Media</th>
                            <th>Featured</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        <tr>
                            <td class="ps-4">
                                <input type="checkbox" class="form-check-input review-checkbox" value="{{ $review->id }}">
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $review->package->name ?? 'N/A' }}</div>
                                <div class="small text-muted">{{ $review->destination->name ?? '' }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                        {{ strtoupper(substr($review->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold small">{{ $review->name }}</div>
                                        <div class="text-muted extra-small">{{ $review->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-warning small mb-1">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                                <div class="review-comment small text-dark" title="{{ $review->comment }}">
                                    {{ Str::limit($review->comment, 60) }}
                                </div>
                                <div class="extra-small text-muted mt-1">{{ $review->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if($review->media && count($review->media) > 0)
                                    <div class="d-flex gap-1">
                                        @foreach(array_slice($review->media, 0, 3) as $m)
                                            <div class="rounded border shadow-xs" style="width: 30px; height: 30px; background: url('{{ $m }}') center/cover no-repeat;"></div>
                                        @endforeach
                                        @if(count($review->media) > 3)
                                            <span class="extra-small text-muted">+{{ count($review->media) - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted smaller">None</span>
                                @endif
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" onchange="toggleFeatured({{ $review->id }})" {{ $review->is_featured ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <select onchange="updateStatus({{ $review->id }}, this.value)" class="form-select form-select-sm border-0 bg-light fw-bold" style="width: 110px;">
                                    <option value="pending" class="text-warning" {{ $review->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" class="text-success" {{ $review->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" class="text-danger" {{ $review->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon btn-light" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        <li><a class="dropdown-item" href="{{ route('admin.reviews.show', $review->id) }}"><i class="bi bi-eye me-2"></i> View Details</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reviews.edit', $review->id) }}"><i class="bi bi-pencil me-2"></i> Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Delete this review forever?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i> Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted">No reviews found matching your criteria.</p>
                                <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-outline-primary">Clear All Filters</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($reviews->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .extra-small { font-size: 0.7rem; }
    .smaller { font-size: 0.8rem; }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .avatar-sm { border: 2px solid #fff; }
    .btn-icon { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
    .table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6e707e; }
    .review-comment { max-width: 250px; overflow: hidden; text-overflow: ellipsis; }
    .form-switch .form-check-input { width: 2.5em; height: 1.25em; cursor: pointer; }
</style>

@push('scripts')
<script>
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.review-checkbox');
    const bulkActions = document.getElementById('bulkActionsWrapper');
    const selectedCount = document.getElementById('selectedCount');

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkVisibility();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkVisibility);
    });

    function updateBulkVisibility() {
        const checked = document.querySelectorAll('.review-checkbox:checked').length;
        selectedCount.innerText = checked;
        if (checked > 0) {
            bulkActions.classList.remove('d-none');
        } else {
            bulkActions.classList.add('d-none');
        }
    }

    function updateStatus(id, status) {
        fetch(`{{ url('admin/reviews') }}/${id}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) toastr.success(data.message);
        });
    }

    function toggleFeatured(id) {
        fetch(`{{ url('admin/reviews') }}/${id}/toggle-featured`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) toastr.info('Featured status updated');
        });
    }

    function handleBulk(action) {
        const ids = Array.from(document.querySelectorAll('.review-checkbox:checked')).map(cb => cb.value);
        if(!confirm(`Apply ${action} to ${ids.length} reviews?`)) return;

        fetch(`{{ route('admin.reviews.bulk-action') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids, action: action })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                toastr.success(data.message);
                setTimeout(() => location.reload(), 1000);
            }
        });
    }
</script>
@endpush
@endsection
