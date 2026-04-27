@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="page-header bg-white p-4 rounded shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-1 text-dark fw-bold"><i class="bi bi-speedometer2 me-2 text-primary"></i>Operations Dashboard
                </h1>
                <p class="text-muted mb-0">Tourliz CMS | Real-time Operations & Pipeline Tracking</p>
            </div>
            <div class="text-end">
                <div class="small fw-bold text-uppercase text-muted">Current Date</div>
                <div class="h5 mb-0">{{ date('d M Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Executive Sales Analytics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm transition hover-shadow">
                <div class="card-body">
                    <div class="small text-uppercase text-muted fw-bold mb-1">Total Quoted Revenue</div>
                    <h3 class="fw-bold text-dark mb-0">{{ $salesStats['quoted'] }}</h3>
                    <div class="small text-muted mt-1">Gross value of all proposals</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm transition hover-shadow">
                <div class="card-body">
                    <div class="small text-uppercase text-muted fw-bold mb-1">Actual Cash Received</div>
                    <h3 class="fw-bold text-success mb-0">{{ $salesStats['received'] }}</h3>
                    <div class="small text-muted mt-1">Realized payments in bank</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm transition hover-shadow">
                <div class="card-body">
                    <div class="small text-uppercase text-muted fw-bold mb-1">Total Operational Cost</div>
                    <h3 class="fw-bold text-danger mb-0">{{ $salesStats['expenses'] }}</h3>
                    <div class="small text-muted mt-1">Sum of all actual expenses</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm transition hover-shadow bg-dark">
                <div class="card-body">
                    <div class="small text-uppercase text-white opacity-75 fw-bold mb-1">Estimated Net Profit</div>
                    <h3 class="fw-bold text-white mb-0">{{ $salesStats['profit'] }}</h3>
                    <div class="small text-white opacity-50 mt-1">Revenue - Actual Expenses</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Sales Performance Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Sales & Profit
                        Performance <small class="text-muted fw-normal">(Last 6 Months)</small></h6>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="280"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Lifecycle Stats -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2 text-info"></i>Productivity Overview</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded">
                        <div>
                            <div class="h3 fw-bold mb-0">{{ $salesStats['conversion_rate'] }}%</div>
                            <div class="small text-muted">Lead Conversion Rate</div>
                        </div>
                        <i class="bi bi-check2-circle fs-1 text-success"></i>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 border rounded text-center">
                                <div class="h4 mb-0 fw-bold text-primary">{{ $stats['bookings'] }}</div>
                                <div class="small text-muted">Direct Bookings</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded text-center">
                                <div class="h4 mb-0 fw-bold text-indigo">{{ $stats['b2b_leads'] }}</div>
                                <div class="small text-muted">B2B Proposals</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded text-center">
                                <div class="h4 mb-0 fw-bold text-success">{{ $stats['b2c_leads'] }}</div>
                                <div class="small text-muted">B2C Leads</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded text-center">
                                <div class="h4 mb-0 fw-bold text-info">{{ $stats['packages'] }}</div>
                                <div class="small text-muted">Master PKGs</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Operations & Reminders -->
        <div class="col-lg-8">
            <!-- Arrival Reminders -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center">
                    <i class="bi bi-airplane-engines-fill text-warning me-2 fs-5"></i>
                    <h6 class="mb-0 fw-bold">Upcoming Arrivals <small class="text-muted fw-normal">(In 3 Days)</small></h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($bookingArrivals as $booking)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <span class="badge bg-light text-dark border border-secondary mb-1">BOOKING |
                                        {{ $booking->name ?: $booking->customer_name }}</span>
                                    <div class="fw-bold">{{ $booking->package->name ?? 'Custom Package' }}</div>
                                    <small class="text-muted"><i
                                            class="bi bi-calendar-event me-1"></i>{{ $booking->travel_date->format('d M') }} |
                                        <i class="bi bi-geo-alt me-1"></i>{{ $booking->package->destination->name ?? 'N/A' }}</small>
                                </div>
                                <a href="{{ route('admin.bookings.index', ['search' => $booking->id]) }}"
                                    class="btn btn-sm btn-outline-dark shadow-sm"><i class="bi bi-eye"></i></a>
                            </li>
                        @empty @endforelse

                        @forelse($b2bArrivals as $lead)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <span class="badge bg-light text-primary border border-primary mb-1">B2B |
                                        {{ $lead->agency->company_name }}</span>
                                    <div class="fw-bold">{{ $lead->title }}</div>
                                    <small class="text-muted"><i
                                            class="bi bi-calendar-event me-1"></i>{{ $lead->start_date->format('d M') }} | <i
                                            class="bi bi-geo-alt me-1"></i>{{ $lead->destination->name }}</small>
                                </div>
                                <a href="{{ route('admin.b2b-itineraries.edit', $lead->id) }}"
                                    class="btn btn-sm btn-outline-primary shadow-sm"><i class="bi bi-eye"></i></a>
                            </li>
                        @empty @endforelse

                        @forelse($b2cArrivals as $lead)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <span class="badge bg-light text-success border border-success mb-1">B2C |
                                        {{ $lead->client_name }}</span>
                                    <div class="fw-bold">{{ $lead->title }}</div>
                                    <small class="text-muted"><i
                                            class="bi bi-calendar-event me-1"></i>{{ $lead->start_date->format('d M') }} | <i
                                            class="bi bi-geo-alt me-1"></i>{{ $lead->destination->name }}</small>
                                </div>
                                <a href="{{ route('admin.b2c-itineraries.edit', $lead->id) }}"
                                    class="btn btn-sm btn-outline-success shadow-sm"><i class="bi bi-eye"></i></a>
                            </li>
                        @empty @endforelse

                        @if($bookingArrivals->isEmpty() && $b2bArrivals->isEmpty() && $b2cArrivals->isEmpty())
                            <li class="list-group-item text-center py-4 text-muted">No arrivals expected in 3 days.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-light-blue border-start border-primary border-4">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-telephone-inbound-fill me-2"></i>Critical
                        Followups</h6>
                </div>
                <div class="card-body">
                    @forelse($bookingFollowups as $f)
                        @php
                            $fName = $f->name ?: $f->customer_name ?: 'Valued Client';
                        @endphp
                        <div class="mb-3 p-3 bg-white rounded shadow-xs border border-primary">
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-primary text-white" style="font-size: 0.6rem;">DIRECT BOOKING</span>
                                <small
                                    class="text-danger fw-bold">{{ $f->next_followup_date->isToday() ? 'TODAY' : $f->next_followup_date->diffForHumans() }}</small>
                            </div>
                            <div class="fw-bold mt-1 small">{{ $fName }}</div>
                            <div style="font-size: 0.75rem" class="text-muted">
                                {{ Str::limit($f->package->name ?? 'Custom Booking', 40) }}</div>
                            <a href="{{ route('admin.bookings.index', ['search' => $f->id]) }}"
                                class="btn btn-xs btn-primary mt-2">View Booking</a>
                        </div>
                    @empty @endforelse

                    @forelse($b2bFollowups as $f)
                        <div class="mb-3 p-3 bg-white rounded shadow-xs border">
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-info text-dark" style="font-size: 0.6rem;">B2B LEAD</span>
                                <small
                                    class="text-danger fw-bold">{{ $f->next_followup_date->isToday() ? 'TODAY' : $f->next_followup_date->diffForHumans() }}</small>
                            </div>
                            <div class="fw-bold mt-1 small">{{ $f->agency->company_name }}</div>
                            <div style="font-size: 0.75rem" class="text-muted">{{ Str::limit($f->title, 40) }}</div>
                            <a href="{{ route('admin.b2b-itineraries.edit', $f->id) }}"
                                class="btn btn-xs btn-outline-info mt-2">Update Stage</a>
                        </div>
                    @empty @endforelse

                    @forelse($b2Followups as $f)
                        <div class="mb-3 p-3 bg-white rounded shadow-xs border border-success">
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-success" style="font-size: 0.6rem;">B2C LEAD</span>
                                <small
                                    class="text-danger fw-bold">{{ $f->next_followup_date->isToday() ? 'TODAY' : $f->next_followup_date->diffForHumans() }}</small>
                            </div>
                            <div class="fw-bold mt-1 small">{{ $f->client_name }}</div>
                            <div style="font-size: 0.75rem" class="text-muted">{{ Str::limit($f->title, 40) }}</div>
                            <a href="{{ route('admin.b2c-itineraries.edit', $f->id) }}"
                                class="btn btn-xs btn-outline-success mt-2">Update Stage</a>
                        </div>
                    @empty @endforelse

                    @if($bookingFollowups->isEmpty() && $b2bFollowups->isEmpty() && $b2Followups->isEmpty())
                        <div class="text-center py-3">
                            <p class="text-muted small mb-0">No urgent followups pending.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4 bg-dark text-white">
                <div class="card-body py-4">
                    <h6 class="fw-bold mb-3">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.b2b-itineraries.create') }}"
                            class="btn btn-sm btn-outline-light text-start border-secondary"><i
                                class="bi bi-plus-circle me-2"></i>New B2B Proposal</a>
                        <a href="{{ route('admin.b2c-itineraries.create') }}"
                            class="btn btn-sm btn-outline-light text-start border-secondary"><i
                                class="bi bi-person-plus me-2"></i>New Walk-in Lead</a>
                        <a href="{{ route('admin.bookings.index') }}"
                            class="btn btn-sm btn-outline-light text-start border-secondary"><i
                                class="bi bi-ticket-detailed me-2"></i>All Bookings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['labels']) !!},
                    datasets: [
                        {
                            label: 'Gross Revenue (Quoted)',
                            data: {!! json_encode($chartData['revenue']) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Net Profit (Estimated)',
                            data: {!! json_encode($chartData['profit']) !!},
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) { return '₹ ' + value.toLocaleString(); }
                            }
                        }
                    }
                }
            });
        </script>
    @endpush

    <style>
        .shadow-xs {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .bg-light-blue {
            background-color: #f0f7ff;
        }

        .transition {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        .btn-xs {
            padding: 4px 10px;
            font-size: 0.75rem;
            border-radius: 4px;
        }
    </style>
@endsection