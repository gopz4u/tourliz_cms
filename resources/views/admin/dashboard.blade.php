@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="mb-0"><i class="bi bi-speedometer2"></i> Dashboard</h1>
    <p class="text-muted mb-0">Welcome to Tourliz CMS Admin Panel</p>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Places</h5>
                        <h2 class="mb-0" id="places-count">-</h2>
                    </div>
                    <i class="bi bi-geo-alt" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('admin.places.index') }}" class="text-white text-decoration-none">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Packages</h5>
                        <h2 class="mb-0" id="packages-count">-</h2>
                    </div>
                    <i class="bi bi-briefcase" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('admin.packages.index') }}" class="text-white text-decoration-none">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Services</h5>
                        <h2 class="mb-0" id="services-count">-</h2>
                    </div>
                    <i class="bi bi-tools" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('admin.services.index') }}" class="text-white text-decoration-none">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Load statistics
    $(document).ready(function() {
        // Load counts (you can create a stats endpoint or use existing ones)
        $.get('{{ route("admin.places.index") }}', function(data) {
            $('#places-count').text(data.total || data.data?.length || 0);
        });
        
        $.get('{{ route("admin.packages.index") }}', function(data) {
            $('#packages-count').text(data.total || data.data?.length || 0);
        });
        
        $.get('{{ route("admin.services.index") }}', function(data) {
            $('#services-count').text(data.total || data.data?.length || 0);
        });
    });
</script>
@endpush
@endsection

