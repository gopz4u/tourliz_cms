@extends('layouts.admin')

@section('title', 'Review Details')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-link text-decoration-none p-0">
            <i class="bi bi-arrow-left me-1"></i> Back to Reviews
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <div class="text-warning h4 mb-1">
                                @for($i=1; $i<=5; $i++)
                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                            <h2 class="h4 fw-bold text-dark mb-1">{{ $review->name }}</h2>
                            <p class="text-muted small">{{ $review->email }} | {{ $review->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $review->status == 'approved' ? 'bg-success' : ($review->status == 'pending' ? 'bg-warning' : 'bg-danger') }} px-3 py-2">
                                {{ ucfirst($review->status) }}
                            </span>
                            @if($review->is_featured)
                                <div class="mt-2 text-warning fw-bold small"><i class="bi bi-patch-check-fill me-1"></i> FEATURED</div>
                            @endif
                        </div>
                    </div>

                    <div class="review-content bg-light p-4 rounded-3 border-start border-primary border-4 mb-4">
                        <p class="mb-0 fs-5 lh-base italic text-dark">"{{ $review->comment }}"</p>
                    </div>

                    @if($review->media && count($review->media) > 0)
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-images me-2 text-primary"></i> Attached Media</h6>
                        <div class="row g-2">
                            @foreach($review->media as $m)
                            <div class="col-md-4 col-6">
                                <a href="{{ $m }}" target="_blank" class="d-block rounded overflow-hidden shadow-sm border">
                                    <img src="{{ $m }}" class="img-fluid hover-zoom" alt="Review Media">
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-white p-4 border-top-0">
                    <div class="d-flex gap-2">
                        @if($review->status != 'approved')
                        <form action="{{ route('admin.reviews.updateStatus', $review->id) }}" method="POST" class="flex-grow-1">
                            @csrf <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-success w-100 fw-bold">Approve Review</button>
                        </form>
                        @endif
                        @if($review->status != 'rejected')
                        <form action="{{ route('admin.reviews.updateStatus', $review->id) }}" method="POST" class="flex-grow-1">
                            @csrf <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-outline-danger w-100 fw-bold">Reject Review</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Context Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small text-muted d-block fw-bold mb-1">Package</label>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-2 me-2">
                                <i class="bi bi-box-seam fs-4 text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-dark">{{ $review->package->name ?? 'N/A' }}</div>
                                <a href="{{ route('admin.packages.edit', $review->package_id ?? 0) }}" class="extra-small text-decoration-none">View Package</a>
                            </div>
                        </div>
                    </div>
                    <hr class="my-3 opacity-50">
                    <div class="mb-3">
                        <label class="small text-muted d-block fw-bold mb-1">Destination</label>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-2 me-2">
                                <i class="bi bi-geo-alt fs-4 text-info"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-dark">{{ $review->destination->name ?? 'Global' }}</div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-3 opacity-50">
                    <div class="mb-0">
                        <label class="small text-muted d-block fw-bold mb-1">User Account</label>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-2 me-2">
                                <i class="bi bi-person-badge fs-4 text-secondary"></i>
                            </div>
                            <div>
                                @if($review->user_id)
                                    <div class="fw-bold small text-dark">{{ $review->user->name ?? 'System User' }}</div>
                                    <span class="badge bg-light text-success border border-success extra-small">Registered User</span>
                                @else
                                    <div class="fw-bold small text-dark">Guest User</div>
                                    <span class="badge bg-light text-muted border extra-small">Guest</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 bg-dark text-white">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Moderation History</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2 d-flex justify-content-between">
                            <span class="text-white-50">Submitted:</span>
                            <span>{{ $review->created_at->format('M d, Y') }}</span>
                        </li>
                        <li class="mb-0 d-flex justify-content-between">
                            <span class="text-white-50">Last Updated:</span>
                            <span>{{ $review->updated_at->diffForHumans() }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .hover-zoom { transition: transform .3s; }
    .hover-zoom:hover { transform: scale(1.05); }
    .italic { font-style: italic; }
</style>
@endsection
