@props(['images', 'id' => 'gallery-slideshow', 'height' => '500px'])

@if(isset($images) && is_array($images) && count($images) > 0)
<div id="{{ $id }}" class="gallery-slideshow">
    <div id="{{ $id }}-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            @foreach($images as $index => $image)
                <button type="button" data-bs-target="#{{ $id }}-carousel" data-bs-slide-to="{{ $index }}" 
                        class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                        aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
        
        <div class="carousel-inner">
            @foreach($images as $index => $image)
                @php
                    $imageUrl = is_string($image) ? $image : ($image['url'] ?? $image['path'] ?? '');
                    if (!empty($imageUrl) && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                        $imageUrl = getImageUrl($imageUrl);
                    }
                @endphp
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ $imageUrl }}" class="d-block w-100" alt="Gallery Image {{ $index + 1 }}" 
                         style="height: {{ $height }}; object-fit: cover;">
                </div>
            @endforeach
        </div>
        
        @if(count($images) > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#{{ $id }}-carousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#{{ $id }}-carousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        @endif
    </div>
    
    <!-- Thumbnail Navigation -->
    @if(count($images) > 1)
    <div class="gallery-thumbnails mt-3 d-flex gap-2 overflow-auto" style="max-height: 100px;">
        @foreach($images as $index => $image)
            @php
                $thumbUrl = is_string($image) ? $image : ($image['url'] ?? $image['path'] ?? '');
                if (!empty($thumbUrl) && !filter_var($thumbUrl, FILTER_VALIDATE_URL)) {
                    $thumbUrl = getImageUrl($thumbUrl);
                }
            @endphp
            <img src="{{ $thumbUrl }}" class="gallery-thumb {{ $index === 0 ? 'active' : '' }}" 
                 alt="Thumbnail {{ $index + 1 }}" 
                 onclick="document.querySelector('#{{ $id }}-carousel').carousel.to({{ $index }})"
                 style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 2px solid transparent; border-radius: 4px;">
        @endforeach
    </div>
    @endif
</div>

<style>
    .gallery-slideshow {
        position: relative;
    }
    
    .gallery-thumb {
        transition: all 0.3s ease;
    }
    
    .gallery-thumb:hover {
        border-color: #007bff !important;
        transform: scale(1.05);
    }
    
    .gallery-thumb.active {
        border-color: #007bff !important;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        background-color: rgba(0, 0, 0, 0.5);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .carousel-control-prev {
        left: 15px;
    }
    
    .carousel-control-next {
        right: 15px;
    }
</style>

<script>
    // Update thumbnail active state when carousel changes
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.querySelector('#{{ $id }}-carousel');
        if (carousel) {
            carousel.addEventListener('slide.bs.carousel', function(e) {
                const thumbs = document.querySelectorAll('#{{ $id }} .gallery-thumb');
                thumbs.forEach((thumb, index) => {
                    thumb.classList.remove('active');
                    if (index === e.to) {
                        thumb.classList.add('active');
                    }
                });
            });
        }
    });
</script>
@else
<div class="alert alert-info">No gallery images available.</div>
@endif

