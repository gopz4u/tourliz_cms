@extends('layouts.admin')

@section('title', 'Preview Itinerary')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.1/dist/full.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    corePlugins: {
      preflight: false,
    },
    theme: {
      extend: {
        fontFamily: {
          sans: ['Inter', 'Segoe UI', 'Roboto', 'sans-serif'],
        },
        colors: {
          primary: '#3b82f6',
          secondary: '#10b981',
          accent: '#8b5cf6',
          neutral: '#1f2937',
          'base-100': '#ffffff',
          'base-200': '#f3f4f6',
        }
      }
    },
    daisyui: {
      themes: ["light"],
    },
  }
</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* Scoped styles applied on top of daisyui within this view */
.daisy-view {
    font-family: 'Inter', sans-serif;
}
.daisy-view h1, .daisy-view h2, .daisy-view h3, .daisy-view h4, .daisy-view h5, .daisy-view h6 {
    margin-top: 0;
    margin-bottom: 0;
}
.daisy-view ul, .daisy-view ol {
    margin-bottom: 0;
    padding-left: 0;
}
.daisy-view .timeline-vertical {
    flex-direction: column;
}
.daisy-view .timeline li {
    display: grid;
    grid-template-columns: var(--timeline-col-start, minmax(0, 1fr)) auto var(--timeline-col-end, minmax(0, 1fr));
    align-items: center;
}
</style>
@endpush

@section('content')
<div class="daisy-view p-4 md:p-8 bg-base-200 min-h-screen rounded-2xl">
    <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6 bg-base-100 p-8 rounded-3xl shadow-sm border border-base-200 relative overflow-hidden">
        <!-- Decorative subtle background -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-secondary/5 rounded-full blur-3xl -ml-20 -mb-20"></div>
        
        <div class="relative z-10 w-full md:w-auto">
            <h2 class="text-3xl md:text-4xl font-extrabold flex items-center gap-4 text-neutral tracking-tight m-0">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shadow-inner">
                    <i class="bi bi-eye"></i>
                </div>
                Preview Itinerary
            </h2>
            <p class="text-base-content/60 mt-3 text-lg m-0 ml-16">Previewing itinerary for: <span class="font-bold text-neutral">{{ $package->name }}</span></p>
        </div>
        <div class="flex flex-col sm:flex-row flex-wrap gap-3 relative z-10 w-full md:w-auto items-center md:justify-end">
            <a href="{{ route('admin.itineraries.edit', $package->id) }}" class="btn btn-primary hover:-translate-y-1 transition-transform duration-300 shadow-xl shadow-primary/30 rounded-full px-6 text-white border-0 w-full sm:w-auto">
                <i class="bi bi-pencil"></i> Continue Editing
            </a>
            <button onclick="shareItineraryWhatsapp()" class="btn bg-[#25D366] hover:bg-[#1DBA55] transition-transform duration-300 shadow-xl shadow-[#25D366]/30 rounded-full px-6 text-white border-0 hover:-translate-y-1 w-full sm:w-auto">
                <i class="bi bi-whatsapp"></i> WhatsApp
            </button>
            <a href="{{ route('admin.itineraries.pdf', $package->id) }}" class="btn btn-secondary hover:-translate-y-1 transition-transform duration-300 shadow-xl shadow-secondary/30 rounded-full px-6 text-white border-0 w-full sm:w-auto">
                <i class="bi bi-file-pdf"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Itinerary Timeline (Left/Center col span 2) -->
        <div class="lg:col-span-2 space-y-8">
            @forelse($enrichedItinerary as $day)
                <div class="card bg-base-100 shadow-xl shadow-base-200/50 overflow-hidden border-0 hover:shadow-2xl transition-all duration-300 rounded-3xl group">
                    <div class="card-body p-0">
                        <!-- Day Header -->
                        <div class="bg-gradient-to-r from-base-100 via-primary/5 to-base-100 p-6 md:p-8 border-b border-base-200/60 flex flex-col md:flex-row md:items-center gap-4">
                            <div class="badge border-0 bg-gradient-to-br from-primary to-blue-600 text-white shadow-lg shadow-primary/30 py-5 px-6 font-black text-lg md:text-xl rounded-2xl tracking-wide shrink-0">
                                Day {{ $day['day'] }}
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold text-neutral tracking-tight m-0 group-hover:text-primary transition-colors">{{ $day['title'] }}</h3>
                        </div>

                        <div class="p-6 md:p-8 space-y-10">
                            <!-- Places -->
                            @if(!empty($day['places']))
                                <div class="bg-base-200/50 rounded-2xl p-6 border border-base-200/60 relative overflow-hidden group/places hover:bg-base-200/80 transition-colors">
                                    <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-primary to-blue-400"></div>
                                    <h4 class="text-sm font-black uppercase tracking-widest text-primary mb-5 flex items-center gap-3 m-0">
                                        <div class="p-1.5 rounded-lg bg-primary/10 text-primary">
                                            <i class="bi bi-geo-alt-fill text-lg"></i>
                                        </div>
                                        Destinations to Visit
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($day['places'] as $place)
                                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center bg-base-100 p-4 rounded-xl shadow-sm border border-base-200/50 hover:border-primary/40 transition-all hover:-translate-y-0.5 gap-3">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0 shadow-inner">
                                                        <i class="bi bi-pin-map-fill text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <span class="font-bold text-neutral text-lg">{{ $place['name'] }}</span>
                                                        @if(isset($place['visit_duration']))
                                                            <div class="text-xs font-medium text-base-content/50 mt-0.5 flex items-center gap-1">
                                                                <i class="bi bi-stopwatch"></i> Spending {{ $place['visit_duration'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(isset($place['entry_ticket']) && $place['entry_ticket']['required'] && $place['entry_ticket']['price'] > 0)
                                                    <div class="badge badge-primary badge-outline badge-lg font-mono font-bold shadow-sm self-start sm:self-auto py-3">
                                                        {{ $place['entry_ticket']['currency'] }} {{ number_format($place['entry_ticket']['price'], 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                                <!-- Activities -->
                                <div class="bg-base-100 rounded-2xl border border-base-200/80 p-6 shadow-sm hover:shadow-md transition-shadow">
                                    <h4 class="text-sm font-black uppercase tracking-widest text-accent mb-6 flex items-center gap-3 m-0">
                                        <div class="p-1.5 rounded-lg bg-accent/10 text-accent">
                                            <i class="bi bi-activity text-lg"></i>
                                        </div>
                                        Activities
                                    </h4>
                                    @if(!empty($day['activities']))
                                        <ul class="timeline timeline-vertical timeline-compact m-0 p-0">
                                            @foreach($day['activities'] as $index => $activity)
                                                <li class="min-h-[5rem]">
                                                    @if($index !== 0)<hr class="bg-base-200"/>@endif
                                                    <div class="timeline-middle h-full flex flex-col justify-center">
                                                        <div class="w-6 h-6 rounded-full bg-accent/20 flex items-center justify-center -ml-1">
                                                            <div class="w-2.5 h-2.5 rounded-full bg-accent shadow-sm shadow-accent"></div>
                                                        </div>
                                                    </div>
                                                    <div class="timeline-end timeline-box bg-transparent border-0 shadow-none w-full mb-6 p-0 pl-4 group-activity">
                                                        <div class="bg-base-100/50 hover:bg-base-100 border border-base-200/50 hover:border-accent/30 rounded-xl p-4 transition-all shadow-sm hover:shadow">
                                                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2">
                                                                <div>
                                                                    <div class="font-bold text-neutral text-base leading-tight group-activity-hover:text-accent transition-colors">{{ $activity['name'] }}</div>
                                                                    @if(isset($activity['time']))
                                                                        <div class="text-sm font-medium text-base-content/50 mt-1.5 flex items-center gap-1.5 bg-base-200 w-fit px-2.5 py-0.5 rounded text-accent/80">
                                                                            <i class="bi bi-clock-fill text-xs"></i> {{ $activity['time'] }} 
                                                                            @if(isset($activity['duration'])) <span class="opacity-60 mx-1">•</span> <span class="font-normal">{{ $activity['duration'] }}</span> @endif
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                @if(isset($activity['entry_ticket']) && $activity['entry_ticket']['price'] > 0)
                                                                    <span class="badge badge-accent badge-outline font-mono font-bold">{{ $activity['entry_ticket']['currency'] }} {{ number_format($activity['entry_ticket']['price'], 2) }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if($index !== count($day['activities']) - 1)<hr class="bg-base-200"/>@endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="bg-base-200/50 border border-dashed border-base-300 rounded-xl p-8 flex flex-col items-center justify-center text-base-content/40 text-sm">
                                            <i class="bi bi-box-seam text-3xl mb-2 opacity-50"></i>
                                            <span class="font-medium">No specific activities</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Transport -->
                                <div class="bg-base-100 rounded-2xl border border-base-200/80 p-6 shadow-sm hover:shadow-md transition-shadow">
                                    <h4 class="text-sm font-black uppercase tracking-widest text-secondary mb-6 flex items-center gap-3 m-0">
                                        <div class="p-1.5 rounded-lg bg-secondary/10 text-secondary">
                                            <i class="bi bi-car-front-fill text-lg"></i>
                                        </div>
                                        Transport
                                    </h4>
                                    @if(!empty($day['transport']))
                                        <div class="space-y-4">
                                            @foreach($day['transport'] as $transport)
                                                <div class="bg-base-100 p-4 rounded-xl shadow-sm border border-base-200/80 hover:border-secondary/40 transition-all hover:translate-x-1 flex gap-4 items-center relative overflow-hidden group/trans">
                                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-secondary opacity-0 group-hover/trans:opacity-100 transition-opacity"></div>
                                                    <div class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center text-secondary shrink-0 shadow-inner">
                                                        <i class="bi {{ str_contains(strtolower($transport['type']), 'flight') ? 'bi-airplane-fill' : (str_contains(strtolower($transport['type']), 'train') ? 'bi-train-front-fill' : 'bi-truck-front-fill') }} text-xl"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex justify-between items-center mb-1.5">
                                                            <span class="font-bold text-neutral text-base truncate pr-2">{{ $transport['type'] }}</span>
                                                            @if(isset($transport['price']) && $transport['price'] > 0)
                                                                <span class="badge badge-secondary badge-sm badge-outline font-mono font-bold shrink-0">{{ $transport['currency'] ?? 'USD' }} {{ number_format($transport['price'], 2) }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center text-sm font-medium text-base-content/60 gap-3 bg-base-200 rounded-lg p-1.5 px-3 w-fit border border-base-200">
                                                            <span class="truncate max-w-[120px] text-neutral" title="{{ $transport['from'] }}">{{ $transport['from'] }}</span>
                                                            <div class="w-5 h-5 rounded-full bg-base-100 flex items-center justify-center shadow-sm">
                                                                <i class="bi bi-arrow-right text-secondary/70 text-xs"></i>
                                                            </div>
                                                            <span class="truncate max-w-[120px] text-neutral" title="{{ $transport['to'] }}">{{ $transport['to'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="bg-base-200/50 border border-dashed border-base-300 rounded-xl p-8 flex flex-col items-center justify-center text-base-content/40 text-sm">
                                            <i class="bi bi-car-front text-3xl mb-2 opacity-50"></i>
                                            <span class="font-medium">No transport details</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Hotel & Meals Container -->
                            <div class="bg-gradient-to-r from-teal-50 to-emerald-50 border border-teal-100 rounded-3xl p-1 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                <div class="grid grid-cols-1 xl:grid-cols-2 divide-y xl:divide-y-0 xl:divide-x divide-teal-200/50 bg-white/40 backdrop-blur-sm rounded-[22px]">
                                    <!-- Accommodation -->
                                    <div class="p-6 md:p-8">
                                        <h4 class="text-sm font-black uppercase tracking-widest text-teal-700 mb-6 flex items-center gap-3 m-0">
                                            <div class="p-1.5 rounded-lg bg-teal-100 text-teal-700">
                                                <i class="bi bi-building-fill text-lg"></i>
                                            </div>
                                            Accommodation
                                        </h4>
                                        @php
                                            $hotelData = $day['hotel'] ?? null;
                                            if (is_string($hotelData)) {
                                                $hotelData = ['name' => $hotelData, 'type' => '', 'price_per_night' => 0];
                                            }
                                        @endphp
                                        @if(!empty($hotelData) && is_array($hotelData))
                                            <div class="flex items-start gap-5">
                                                <div class="w-14 h-14 rounded-2xl bg-teal-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-teal-500/30">
                                                    <i class="bi bi-moon-stars-fill text-2xl"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="font-black text-teal-950 text-xl leading-tight mb-2">{{ $hotelData['name'] ?? 'Hotel' }}</div>
                                                    <div class="flex flex-wrap gap-2 mb-3">
                                                        @if(!empty($hotelData['type']))
                                                            <span class="badge border-teal-200 bg-white text-teal-800 shadow-sm font-medium py-2">{{ $hotelData['type'] }}</span>
                                                        @endif
                                                    </div>
                                                    @if(isset($hotelData['price_per_night']) && $hotelData['price_per_night'] > 0)
                                                        <div class="text-teal-700 font-mono text-base font-bold bg-white inline-block px-3 py-1 rounded-lg border border-teal-100 shadow-sm">
                                                            {{ $hotelData['currency'] ?? 'MYR' }} {{ number_format($hotelData['price_per_night'], 2) }} <span class="text-teal-600/60 font-sans font-medium text-xs ml-1 uppercase">/ night</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-white/60 border border-dashed border-teal-200 rounded-xl p-6 flex items-center justify-center text-teal-600/60 italic text-sm">
                                                No accommodation details
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Meals -->
                                    <div class="p-6 md:p-8">
                                        <h4 class="text-sm font-black uppercase tracking-widest text-emerald-700 mb-6 flex items-center gap-3 m-0">
                                            <div class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700">
                                                <i class="bi bi-cup-hot-fill text-lg"></i>
                                            </div>
                                            Meals Included
                                        </h4>
                                        @php
                                            $mealsData = $day['meals'] ?? [];
                                            // Normalize indexed array → keyed array
                                            if (is_array($mealsData) && isset($mealsData[0])) {
                                                $mealsData = [
                                                    'breakfast' => in_array('Breakfast', $mealsData) ? 'Included' : 'Not included',
                                                    'lunch'     => in_array('Lunch',     $mealsData) ? 'Included' : 'Not included',
                                                    'dinner'    => in_array('Dinner',    $mealsData) ? 'Included' : 'Not included',
                                                ];
                                            }
                                        @endphp
                                        @if(!empty($mealsData) && is_array($mealsData))
                                            <div class="flex flex-wrap sm:flex-nowrap gap-3">
                                                <div class="flex-1 bg-white rounded-2xl p-4 shadow-sm border border-emerald-100/50 text-center relative overflow-hidden group hover:border-emerald-300 transition-all hover:-translate-y-1">
                                                    <i class="bi bi-egg-fried text-emerald-200 text-3xl absolute -right-2 -bottom-2 group-hover:scale-110 transition-transform"></i>
                                                    <div class="text-xs uppercase font-black text-emerald-700/50 mb-2 relative z-10 tracking-wider">Breakfast</div>
                                                    <div class="font-bold text-emerald-950 text-lg relative z-10">{{ $mealsData['breakfast'] ?? 'No' }}</div>
                                                </div>
                                                <div class="flex-1 bg-white rounded-2xl p-4 shadow-sm border border-emerald-100/50 text-center relative overflow-hidden group hover:border-emerald-300 transition-all hover:-translate-y-1">
                                                    <i class="bi bi-basket text-emerald-200 text-3xl absolute -right-2 -bottom-2 group-hover:scale-110 transition-transform"></i>
                                                    <div class="text-xs uppercase font-black text-emerald-700/50 mb-2 relative z-10 tracking-wider">Lunch</div>
                                                    <div class="font-bold text-emerald-950 text-lg relative z-10">{{ $mealsData['lunch'] ?? 'No' }}</div>
                                                </div>
                                                <div class="flex-1 bg-white rounded-2xl p-4 shadow-sm border border-emerald-100/50 text-center relative overflow-hidden group hover:border-emerald-300 transition-all hover:-translate-y-1">
                                                    <i class="bi bi-moon text-emerald-200 text-3xl absolute -right-2 -bottom-2 group-hover:scale-110 transition-transform"></i>
                                                    <div class="text-xs uppercase font-black text-emerald-700/50 mb-2 relative z-10 tracking-wider">Dinner</div>
                                                    <div class="font-bold text-emerald-950 text-lg relative z-10">{{ $mealsData['dinner'] ?? 'No' }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-white/60 border border-dashed border-emerald-200 rounded-xl p-6 flex items-center justify-center text-emerald-600/60 italic text-sm">
                                                No meal details
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            @if(!empty($day['notes']))
                            <div class="alert bg-blue-50/80 border border-blue-200/60 text-blue-900 rounded-2xl shadow-sm p-5 items-start">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                                    <i class="bi bi-info-circle-fill text-xl"></i>
                                </div>
                                <div class="pt-1 text-base leading-relaxed font-medium">
                                    {{ $day['notes'] }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="hero bg-base-100 rounded-3xl shadow-xl border border-base-200/50 py-24 object-cover relative overflow-hidden">
                    <div class="absolute inset-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                    <div class="hero-content text-center relative z-10">
                        <div class="max-w-md">
                            <div class="w-32 h-32 mx-auto bg-base-200 rounded-full flex items-center justify-center mb-8 shadow-inner">
                                <i class="bi bi-calendar-x text-6xl text-base-content/30 block"></i>
                            </div>
                            <h2 class="text-3xl font-black text-neutral mb-4 tracking-tight">No itinerary data</h2>
                            <p class="py-2 text-base-content/60 text-lg mb-8 leading-relaxed">This package doesn't have any days defined in its itinerary yet. You can start building your perfect tour right now.</p>
                            <a href="{{ route('admin.itineraries.edit', $package->id) }}" class="btn btn-primary btn-lg rounded-full px-10 shadow-xl shadow-primary/30 text-white border-0 hover:-translate-y-1 transition-transform">Create Itinerary <i class="bi bi-arrow-right ml-2"></i></a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Right Sidebar (Cost Breakdown) -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Summary Stats Card -->
            <div class="card bg-gradient-to-br from-neutral to-neutral-focus text-neutral-content shadow-2xl sticky top-8 rounded-3xl overflow-hidden border border-white/10 group">
                <!-- Background decor -->
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-primary/20 rounded-full blur-3xl transition-opacity group-hover:opacity-100 opacity-50"></div>
                
                <div class="card-body p-8 relative z-10">
                    <h2 class="card-title text-xl font-black flex items-center gap-3 mb-6 border-b border-white/10 pb-6 text-white m-0 tracking-wide">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                            <i class="bi bi-calculator text-xl text-primary"></i>
                        </div>
                        Cost Breakdown
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center bg-black/20 hover:bg-black/30 p-3 rounded-xl transition-colors group/item">
                            <span class="flex items-center gap-3 text-sm font-semibold uppercase tracking-wider text-white/90"><div class="w-2.5 h-2.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.5)]"></div> Accommodation</span>
                            <span class="font-mono px-2 py-1 rounded bg-black/40 font-bold text-white shadow-inner whitespace-nowrap">{{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['hotels'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-black/20 hover:bg-black/30 p-3 rounded-xl transition-colors group/item">
                            <span class="flex items-center gap-3 text-sm font-semibold uppercase tracking-wider text-white/90"><div class="w-2.5 h-2.5 rounded-full bg-secondary shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div> Transport</span>
                            <span class="font-mono px-2 py-1 rounded bg-black/40 font-bold text-white shadow-inner whitespace-nowrap">{{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['transport'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-black/20 hover:bg-black/30 p-3 rounded-xl transition-colors group/item">
                            <span class="flex items-center gap-3 text-sm font-semibold uppercase tracking-wider text-white/90"><div class="w-2.5 h-2.5 rounded-full bg-accent shadow-[0_0_8px_rgba(139,92,246,0.5)]"></div> Activities</span>
                            <span class="font-mono px-2 py-1 rounded bg-black/40 font-bold text-white shadow-inner whitespace-nowrap">{{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['activities'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-black/20 hover:bg-black/30 p-3 rounded-xl transition-colors group/item">
                            <span class="flex items-center gap-3 text-sm font-semibold uppercase tracking-wider text-white/90"><div class="w-2.5 h-2.5 rounded-full bg-orange-400 shadow-[0_0_8px_rgba(251,146,60,0.5)]"></div> Tickets</span>
                            <span class="font-mono px-2 py-1 rounded bg-black/40 font-bold text-white shadow-inner whitespace-nowrap">{{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['entry_tickets'], 2) }}</span>
                        </div>
                        
                        <div class="my-6 border-t border-dashed border-white/20"></div>
                        
                        <div class="bg-black/30 rounded-2xl p-5 border border-white/5 shadow-inner">
                            <div class="text-xs font-bold text-white/50 uppercase tracking-widest mb-1 text-center">Total Estimated Cost</div>
                            <div class="text-center">
                                <span class="text-primary font-bold text-xl">{{ $costBreakdown['currency'] }}</span>
                                <span class="text-4xl font-black font-mono text-white tracking-tighter ml-1">{{ number_format($costBreakdown['total'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package Info Card -->
            <div class="card bg-base-100 shadow-xl shadow-base-200/50 border border-base-200/60 rounded-3xl">
                <div class="card-body p-8">
                    <h3 class="font-black text-neutral mb-6 flex items-center gap-3 text-xl m-0 tracking-tight">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        Package Summary
                    </h3>
                    
                    <ul class="space-y-6 m-0 p-0 list-none">
                        <li class="flex items-center gap-4 bg-base-200/50 p-4 rounded-2xl hover:bg-base-200 transition-colors">
                            <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-primary shrink-0">
                                <i class="bi bi-clock-history text-xl"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold uppercase tracking-widest text-base-content/50 mb-1">Duration</div>
                                <div class="font-bold text-neutral text-lg leading-none">{{ $package->duration }}</div>
                            </div>
                        </li>
                        <li class="flex items-center gap-4 bg-base-200/50 p-4 rounded-2xl hover:bg-base-200 transition-colors">
                            <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-accent shrink-0">
                                <i class="bi bi-pin-map-fill text-xl"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold uppercase tracking-widest text-base-content/50 mb-1">Destination</div>
                                <div class="font-bold text-neutral text-lg leading-none">{{ $package->destination->name ?? 'N/A' }}</div>
                            </div>
                        </li>
                        <li class="flex items-center gap-4 bg-base-200/50 p-4 rounded-2xl hover:bg-base-200 transition-colors">
                            <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-secondary shrink-0">
                                <i class="bi bi-calendar2-check-fill text-xl"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold uppercase tracking-widest text-base-content/50 mb-1">Last Updated</div>
                                <div class="font-bold text-neutral text-lg leading-none">{{ $package->updated_at->format('M d, Y') }}</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function shareItineraryWhatsapp() {
    let text = "";
    text += "\u2708\uFE0F *TOURLIZ ITINERARY* \u2708\uFE0F\n\n";
    text += "\u2B50 *{{ str_replace('\"', '\\\"', $package->name) }}*\n";
    text += "\u23F3 *Duration:* {{ (int)$package->duration_days }} Days, {{ (int)$package->duration_nights }} Nights\n\n";
    
    text += "\uD83D\uDCCB *Day by Day Route:*\n\n";
    
    @foreach($enrichedItinerary as $day)
        text += "\u2705 *Day {{ $day['day'] }} - {{ str_replace('\"', '\\\"', $day['title'] ?? '') }}*\n";
        
        @if(!empty($day['hotel']) && !empty($day['hotel']['name']))
            text += "   \uD83C\uDFE8 *Hotel:* {{ str_replace('\"', '\\\"', $day['hotel']['name']) }}\n";
        @endif
        
        @if(!empty($day['places']))
            @php $pl = array_map(fn($p) => $p['name'] ?? '', $day['places']); @endphp
            text += "   \uD83D\uDCCD *Places:* {{ str_replace('\"', '\\\"', implode(', ', $pl)) }}\n";
        @endif
        
        @if(!empty($day['activities']))
            @php $al = array_map(fn($a) => $a['name'] ?? '', $day['activities']); @endphp
            text += "   \u2728 *Activities:* {{ str_replace('\"', '\\\"', implode(', ', $al)) }}\n";
        @endif
        
        text += "\n";
    @endforeach
    
    text += "\uD83D\uDCA1 Let us know if you would like the full PDF or have any questions!\n\n";
    text += "\uD83D\uDE4F *Thank you from the Tourliz Team!*";

    window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
}
</script>
@endpush
