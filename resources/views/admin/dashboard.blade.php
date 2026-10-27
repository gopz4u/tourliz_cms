@extends('layouts.admin')

@section('title', 'Travel Advisor Dashboard')

@section('content')
    @php
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
        $firstName = explode(' ', trim(auth()->user()->name ?? 'Advisor'))[0];
        $hasFollowups = $followupCount > 0;
    @endphp

    <div class="advisor-dashboard">
        <header class="dashboard-hero">
            <div>
                <div class="dashboard-eyebrow"><span></span> Advisor workspace</div>
                <h1>{{ $greeting }}, {{ $firstName }}.</h1>
                <p>Here is what needs your attention on {{ now()->format('l, d F') }}.</p>
            </div>
            <div class="hero-actions">
                <a href="{{ route('admin.calendar.index') }}" class="btn-soft">
                    <i class="bi bi-calendar3"></i><span>Calendar</span>
                </a>
                <div class="dropdown">
                    <button class="btn-primary-action dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-plus-lg"></i><span>Create new</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dashboard-create-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.b2c-itineraries.create') }}">
                                <span class="menu-icon coral"><i class="bi bi-person-plus"></i></span>
                                <span><strong>Direct client trip</strong><small>Build a B2C quote</small></span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.b2b-itineraries.create') }}">
                                <span class="menu-icon violet"><i class="bi bi-buildings"></i></span>
                                <span><strong>Agency proposal</strong><small>Create a B2B itinerary</small></span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.bookings.create') }}">
                                <span class="menu-icon blue"><i class="bi bi-ticket-perforated"></i></span>
                                <span><strong>Manual booking</strong><small>Add a confirmed booking</small></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <section class="metric-grid" aria-label="Business summary">
            <article class="metric-card metric-total">
                <div class="metric-topline">
                    <span class="metric-label">Total trip value</span>
                    <span class="metric-icon"><i class="bi bi-stars"></i></span>
                </div>
                <div class="metric-value"><small>MYR</small> {{ $salesStats['total_revenue'] }}</div>
                <div class="metric-foot">
                    <span><i class="bi bi-briefcase"></i> {{ $stats['bookings'] + $stats['b2b_leads'] + $stats['b2c_leads'] }} client trips</span>
                    <span class="positive">{{ $salesStats['conversion_rate'] }}% won</span>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-topline">
                    <span class="metric-label">Payments collected</span>
                    <span class="metric-icon mint"><i class="bi bi-check2-circle"></i></span>
                </div>
                <div class="metric-value"><small>MYR</small> {{ $salesStats['received'] }}</div>
                <div class="collection-progress" aria-label="{{ $salesStats['collection_rate'] }} percent collected">
                    <span style="width: {{ $salesStats['collection_rate'] }}%"></span>
                </div>
                <div class="metric-foot"><span>{{ $salesStats['collection_rate'] }}% of total value</span></div>
            </article>

            <article class="metric-card">
                <div class="metric-topline">
                    <span class="metric-label">Outstanding</span>
                    <span class="metric-icon amber"><i class="bi bi-hourglass-split"></i></span>
                </div>
                <div class="metric-value"><small>MYR</small> {{ $salesStats['outstanding'] }}</div>
                <div class="metric-foot">
                    <span><i class="bi bi-receipt"></i> Client balance to collect</span>
                    <a href="{{ route('admin.expenses.index') }}">View finance</a>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-topline">
                    <span class="metric-label">Estimated profit</span>
                    <span class="metric-icon violet"><i class="bi bi-graph-up-arrow"></i></span>
                </div>
                <div class="metric-value"><small>MYR</small> {{ $salesStats['profit'] }}</div>
                <div class="metric-foot">
                    <span><i class="bi bi-dash-circle"></i> MYR {{ $salesStats['expenses'] }} costs</span>
                    <span>Live estimate</span>
                </div>
            </article>
        </section>

        <div class="dashboard-grid">
            <section class="dashboard-card action-card">
                <div class="card-heading">
                    <div>
                        <span class="section-kicker">Today</span>
                        <h2>Your action list</h2>
                        <p>Follow-ups that are due or overdue.</p>
                    </div>
                    <a href="{{ route('admin.follow-ups.index') }}" class="text-link">View queue <i class="bi bi-arrow-right"></i></a>
                </div>

                @if($hasFollowups)
                    <div class="action-list">
                        @foreach($bookingFollowups->take(2) as $followup)
                            @php
                                $followupName = $followup->name ?: ($followup->customer_name ?: 'Client');
                                $followupDate = $followup->next_followup_date;
                            @endphp
                            <a href="{{ route('admin.bookings.show', $followup) }}" class="action-item">
                                <span class="client-avatar blue">{{ strtoupper(substr($followupName, 0, 1)) }}</span>
                                <span class="action-copy">
                                    <strong>{{ $followupName }}</strong>
                                    <small>{{ optional($followup->package)->name ?: 'Direct booking' }}</small>
                                </span>
                                <span class="action-type">Booking</span>
                                <span class="due-label {{ $followupDate && $followupDate->lt(now()->startOfDay()) ? 'overdue' : '' }}">
                                    {{ $followupDate && $followupDate->isToday() ? 'Due today' : ($followupDate ? $followupDate->diffForHumans() : 'Due') }}
                                </span>
                                <i class="bi bi-chevron-right row-arrow"></i>
                            </a>
                        @endforeach

                        @foreach($b2bFollowups->take(2) as $followup)
                            @php
                                $followupName = optional($followup->agency)->company_name ?: ($followup->client_name ?: 'Agency client');
                                $followupDate = $followup->next_followup_date;
                            @endphp
                            <a href="{{ route('admin.b2b-itineraries.edit', $followup->id) }}" class="action-item">
                                <span class="client-avatar violet">{{ strtoupper(substr($followupName, 0, 1)) }}</span>
                                <span class="action-copy">
                                    <strong>{{ $followupName }}</strong>
                                    <small>{{ $followup->title ?: 'Agency proposal' }}</small>
                                </span>
                                <span class="action-type">B2B quote</span>
                                <span class="due-label {{ $followupDate && $followupDate->lt(now()->startOfDay()) ? 'overdue' : '' }}">
                                    {{ $followupDate && $followupDate->isToday() ? 'Due today' : ($followupDate ? $followupDate->diffForHumans() : 'Due') }}
                                </span>
                                <i class="bi bi-chevron-right row-arrow"></i>
                            </a>
                        @endforeach

                        @foreach($b2Followups->take(2) as $followup)
                            @php
                                $followupName = $followup->client_name ?: 'Direct client';
                                $followupDate = $followup->next_followup_date;
                            @endphp
                            <a href="{{ route('admin.b2c-itineraries.edit', $followup->id) }}" class="action-item">
                                <span class="client-avatar coral">{{ strtoupper(substr($followupName, 0, 1)) }}</span>
                                <span class="action-copy">
                                    <strong>{{ $followupName }}</strong>
                                    <small>{{ $followup->title ?: 'Direct proposal' }}</small>
                                </span>
                                <span class="action-type">B2C quote</span>
                                <span class="due-label {{ $followupDate && $followupDate->lt(now()->startOfDay()) ? 'overdue' : '' }}">
                                    {{ $followupDate && $followupDate->isToday() ? 'Due today' : ($followupDate ? $followupDate->diffForHumans() : 'Due') }}
                                </span>
                                <i class="bi bi-chevron-right row-arrow"></i>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state compact">
                        <span class="empty-icon"><i class="bi bi-check2"></i></span>
                        <div><strong>You are all caught up</strong><p>No client follow-ups are due right now.</p></div>
                    </div>
                @endif
            </section>

            <aside class="dashboard-card pulse-card">
                <div class="card-heading compact-heading">
                    <div>
                        <span class="section-kicker">Pipeline</span>
                        <h2>Business pulse</h2>
                    </div>
                    <a href="{{ route('admin.b2c-itineraries.kanban') }}" class="icon-link" aria-label="Open pipeline"><i class="bi bi-arrow-up-right"></i></a>
                </div>

                <div class="conversion-block">
                    <div class="conversion-ring" style="--conversion: {{ $salesStats['conversion_rate'] }}">
                        <div><strong>{{ $salesStats['conversion_rate'] }}%</strong><span>conversion</span></div>
                    </div>
                    <div class="conversion-copy"><strong>Lead conversion</strong><span>Across bookings, B2B and direct clients</span></div>
                </div>

                <div class="pulse-stats">
                    <div><span class="pulse-dot coral"></span><p>B2C proposals</p><strong>{{ $stats['b2c_leads'] }}</strong></div>
                    <div><span class="pulse-dot violet"></span><p>B2B proposals</p><strong>{{ $stats['b2b_leads'] }}</strong></div>
                    <div><span class="pulse-dot blue"></span><p>Bookings</p><strong>{{ $stats['bookings'] }}</strong></div>
                </div>

                <div class="quote-breakdown">
                    <div><span>B2C Quoted Revenue</span><strong>MYR {{ $salesStats['b2c_quoted'] }}</strong></div>
                    <div><span>B2B Quoted Revenue</span><strong>MYR {{ $salesStats['b2b_quoted'] }}</strong></div>
                    <a href="{{ route('admin.b2c-itineraries.index', ['date_status' => 'unscheduled']) }}"><span><i class="bi bi-calendar-x me-1"></i>B2C needs travel date</span><strong>{{ $stats['b2c_undated'] }}</strong></a>
                    <a href="{{ route('admin.b2b-itineraries.index', ['date_status' => 'unscheduled']) }}"><span><i class="bi bi-calendar-x me-1"></i>B2B needs travel date</span><strong>{{ $stats['b2b_undated'] }}</strong></a>
                </div>
            </aside>
        </div>

        <section class="dashboard-card trips-card">
            <div class="card-heading">
                <div>
                    <span class="section-kicker">Next 30 days</span>
                    <h2>Upcoming trips</h2>
                    <p>Your nearest departures across every sales channel.</p>
                </div>
                <a href="{{ route('admin.calendar.index') }}" class="text-link">Open calendar <i class="bi bi-arrow-right"></i></a>
            </div>

            @forelse($upcomingTrips as $trip)
                <a href="{{ $trip['url'] }}" class="trip-row">
                    <span class="date-tile">
                        <small>{{ $trip['date']->format('M') }}</small>
                        <strong>{{ $trip['date']->format('d') }}</strong>
                    </span>
                    <span class="trip-main">
                        <strong>{{ $trip['title'] }}</strong>
                        <small><i class="bi bi-geo-alt"></i> {{ $trip['destination'] }}</small>
                    </span>
                    <span class="trip-client">
                        <small>Client</small>
                        <strong>{{ $trip['client'] }}</strong>
                    </span>
                    <span class="trip-countdown">
                        <small>Departs</small>
                        <strong>{{ $trip['date']->isToday() ? 'Today' : $trip['date']->diffForHumans(now(), true) }}</strong>
                    </span>
                    <span class="status-pill {{ $trip['tone'] }}">{{ ucfirst(str_replace('_', ' ', $trip['status'])) }}</span>
                    <i class="bi bi-chevron-right row-arrow"></i>
                </a>
            @empty
                <div class="empty-state">
                    <span class="empty-icon plane"><i class="bi bi-airplane"></i></span>
                    <div><strong>No departures in the next 30 days</strong><p>Create a trip or check the full calendar for later departures.</p></div>
                    <a href="{{ route('admin.b2c-itineraries.create') }}" class="btn-soft">Create a trip</a>
                </div>
            @endforelse
        </section>

        <div class="dashboard-grid bottom-grid">
            <section class="dashboard-card chart-card">
                <div class="card-heading">
                    <div>
                        <span class="section-kicker">Last 6 months</span>
                        <h2>Revenue overview</h2>
                        <p>Quoted value and estimated profit in MYR.</p>
                    </div>
                    <div class="chart-legend" aria-hidden="true">
                        <span><i class="legend-dot revenue"></i> Revenue</span>
                        <span><i class="legend-dot profit"></i> Profit</span>
                    </div>
                </div>
                <div class="chart-wrap"><canvas id="salesChart"></canvas></div>
            </section>

            <aside class="dashboard-card activity-card">
                <div class="card-heading compact-heading">
                    <div>
                        <span class="section-kicker">Latest</span>
                        <h2>Recent activity</h2>
                    </div>
                </div>

                <div class="activity-list">
                    @forelse($recentActivity as $activity)
                        <a href="{{ $activity['url'] }}" class="activity-item">
                            <span class="activity-mark {{ $activity['tone'] }}"><i class="bi bi-file-earmark-text"></i></span>
                            <span class="activity-copy">
                                <strong>{{ $activity['client'] }}</strong>
                                <small>{{ $activity['type'] }} · {{ ucfirst(str_replace('_', ' ', $activity['status'])) }}</small>
                            </span>
                            <span class="activity-meta">
                                <strong>MYR {{ number_format($activity['amount'], 0) }}</strong>
                                <small>{{ $activity['date']->diffForHumans() }}</small>
                            </span>
                        </a>
                    @empty
                        <div class="empty-state compact">
                            <span class="empty-icon"><i class="bi bi-inbox"></i></span>
                            <div><strong>No recent activity</strong><p>New quotes and bookings will appear here.</p></div>
                        </div>
                    @endforelse
                </div>
            </aside>
        </div>

        <section class="quick-create" aria-label="Quick actions">
            <div><span class="section-kicker">Quick create</span><h2>Keep your day moving</h2></div>
            <a href="{{ route('admin.b2c-itineraries.create') }}"><span class="quick-icon coral"><i class="bi bi-map"></i></span><span><strong>New direct trip</strong><small>Build a client itinerary</small></span><i class="bi bi-arrow-right"></i></a>
            <a href="{{ route('admin.b2b-itineraries.create') }}"><span class="quick-icon violet"><i class="bi bi-building-add"></i></span><span><strong>New B2B quote</strong><small>Prepare an agency proposal</small></span><i class="bi bi-arrow-right"></i></a>
            <a href="{{ route('admin.bookings.create') }}"><span class="quick-icon blue"><i class="bi bi-ticket-perforated"></i></span><span><strong>Add booking</strong><small>Record a confirmed trip</small></span><i class="bi bi-arrow-right"></i></a>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .advisor-dashboard {
            --dash-ink: #17233c;
            --dash-muted: #718096;
            --dash-line: #e8ebf2;
            --dash-card: #ffffff;
            --dash-coral: #f26f5b;
            --dash-coral-soft: #fff0ec;
            --dash-blue: #4f7cff;
            --dash-blue-soft: #edf2ff;
            --dash-violet: #7568d9;
            --dash-violet-soft: #f0eefe;
            --dash-mint: #21a179;
            --dash-mint-soft: #eaf8f3;
            color: var(--dash-ink);
            padding-bottom: 1rem;
        }

        .dashboard-hero { display:flex; align-items:flex-end; justify-content:space-between; gap:2rem; margin:0 0 1.6rem; }
        .dashboard-eyebrow, .section-kicker { color:#8a94a7; font-size:.68rem; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
        .dashboard-eyebrow { display:flex; align-items:center; gap:.55rem; margin-bottom:.55rem; }
        .dashboard-eyebrow span { width:18px; height:2px; border-radius:3px; background:var(--dash-coral); }
        .dashboard-hero h1 { margin:0; font-size:clamp(1.8rem, 3vw, 2.55rem); line-height:1.15; letter-spacing:-.045em; font-weight:800; }
        .dashboard-hero p { color:var(--dash-muted); margin:.5rem 0 0; font-size:.91rem; }
        .hero-actions { display:flex; align-items:center; gap:.65rem; flex-shrink:0; }
        .btn-soft, .btn-primary-action { min-height:42px; display:inline-flex; align-items:center; justify-content:center; gap:.55rem; padding:.65rem 1rem; border-radius:12px; font-size:.8rem; font-weight:750; text-decoration:none; border:1px solid var(--dash-line); transition:.2s ease; }
        .btn-soft { color:var(--dash-ink); background:#fff; }
        .btn-soft:hover { color:var(--dash-violet); border-color:#cbc7f4; transform:translateY(-1px); }
        .btn-primary-action { color:#fff; border-color:var(--dash-coral); background:var(--dash-coral); box-shadow:0 8px 18px rgba(242,111,91,.2); }
        .btn-primary-action:hover { color:#fff; background:#e8604c; transform:translateY(-1px); }
        .dashboard-create-menu { width:280px; border:1px solid var(--dash-line); border-radius:15px; padding:.5rem; box-shadow:0 18px 45px rgba(23,35,60,.14); }
        .dashboard-create-menu .dropdown-item { display:flex; align-items:center; gap:.75rem; border-radius:10px; padding:.7rem; }
        .dashboard-create-menu .dropdown-item span:last-child { display:flex; flex-direction:column; }
        .dashboard-create-menu strong { color:var(--dash-ink); font-size:.8rem; }
        .dashboard-create-menu small { color:var(--dash-muted); font-size:.68rem; margin-top:.12rem; }
        .menu-icon { width:36px; height:36px; display:grid; place-items:center; border-radius:10px; flex:0 0 auto; }

        .metric-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.85rem; margin-bottom:.9rem; }
        .metric-card, .dashboard-card { border:1px solid var(--dash-line); background:var(--dash-card); border-radius:17px; box-shadow:0 4px 18px rgba(23,35,60,.035); }
        .metric-card { position:relative; min-width:0; padding:1.15rem 1.2rem; overflow:hidden; }
        .metric-total:after { content:""; position:absolute; width:95px; height:95px; border-radius:50%; right:-45px; bottom:-52px; background:var(--dash-coral-soft); }
        .metric-topline, .metric-foot { display:flex; align-items:center; justify-content:space-between; gap:.7rem; }
        .metric-label { color:#68748a; font-size:.7rem; font-weight:750; letter-spacing:.025em; }
        .metric-icon { width:31px; height:31px; display:grid; place-items:center; border-radius:9px; color:var(--dash-coral); background:var(--dash-coral-soft); font-size:.87rem; }
        .metric-icon.mint, .menu-icon.mint, .quick-icon.mint { color:var(--dash-mint); background:var(--dash-mint-soft); }
        .metric-icon.amber { color:#bf7a18; background:#fff6df; }
        .metric-icon.violet, .menu-icon.violet, .quick-icon.violet { color:var(--dash-violet); background:var(--dash-violet-soft); }
        .menu-icon.blue, .quick-icon.blue { color:var(--dash-blue); background:var(--dash-blue-soft); }
        .menu-icon.coral, .quick-icon.coral { color:var(--dash-coral); background:var(--dash-coral-soft); }
        .metric-value { font-size:clamp(1.05rem, 1.8vw, 1.48rem); font-weight:800; letter-spacing:-.035em; margin:.75rem 0 .45rem; white-space:nowrap; }
        .metric-value small { color:#95a0b2; font-size:.62rem; letter-spacing:.04em; font-weight:800; }
        .metric-foot { color:#8a94a7; font-size:.63rem; min-height:16px; }
        .metric-foot a { position:relative; z-index:2; color:var(--dash-violet); font-weight:750; text-decoration:none; }
        .metric-foot .positive { color:var(--dash-mint); font-weight:750; }
        .collection-progress { height:4px; overflow:hidden; border-radius:20px; background:#edf0f5; margin:.2rem 0 .45rem; }
        .collection-progress span { display:block; height:100%; border-radius:inherit; background:var(--dash-mint); }

        .dashboard-grid { display:grid; grid-template-columns:minmax(0,1.65fr) minmax(280px,.75fr); gap:.9rem; margin-bottom:.9rem; }
        .dashboard-card { min-width:0; overflow:hidden; }
        .card-heading { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; padding:1.2rem 1.25rem 1rem; }
        .card-heading h2, .quick-create h2 { font-size:1rem; font-weight:800; letter-spacing:-.02em; margin:.18rem 0 .2rem; }
        .card-heading p { color:var(--dash-muted); font-size:.7rem; margin:0; }
        .text-link, .icon-link { color:var(--dash-violet); font-size:.7rem; font-weight:750; text-decoration:none; white-space:nowrap; }
        .text-link { margin-top:.55rem; }
        .text-link:hover, .icon-link:hover { color:#594db9; }
        .icon-link { width:30px; height:30px; display:grid; place-items:center; border:1px solid var(--dash-line); border-radius:9px; }

        .action-list { border-top:1px solid var(--dash-line); }
        .action-item { display:grid; grid-template-columns:36px minmax(130px,1fr) 78px 78px 14px; align-items:center; gap:.7rem; color:inherit; text-decoration:none; padding:.72rem 1.25rem; border-bottom:1px solid #f0f2f6; transition:.18s ease; }
        .action-item:last-child { border-bottom:0; }
        .action-item:hover { color:inherit; background:#fafbfe; }
        .client-avatar { width:34px; height:34px; display:grid; place-items:center; border-radius:11px; font-size:.7rem; font-weight:800; }
        .client-avatar.blue, .activity-mark.blue { color:var(--dash-blue); background:var(--dash-blue-soft); }
        .client-avatar.violet, .activity-mark.violet { color:var(--dash-violet); background:var(--dash-violet-soft); }
        .client-avatar.coral, .activity-mark.coral { color:var(--dash-coral); background:var(--dash-coral-soft); }
        .action-copy, .activity-copy, .activity-meta { display:flex; flex-direction:column; min-width:0; }
        .action-copy strong, .activity-copy strong { overflow:hidden; color:var(--dash-ink); font-size:.75rem; text-overflow:ellipsis; white-space:nowrap; }
        .action-copy small, .activity-copy small { overflow:hidden; color:var(--dash-muted); font-size:.64rem; text-overflow:ellipsis; white-space:nowrap; margin-top:.15rem; }
        .action-type { color:#8590a2; font-size:.64rem; }
        .due-label { color:#a36b17; background:#fff7e5; border-radius:20px; padding:.3rem .5rem; font-size:.58rem; font-weight:800; text-align:center; white-space:nowrap; }
        .due-label.overdue { color:#c95143; background:#fff0ed; }
        .row-arrow { color:#b1b8c5; font-size:.7rem; }

        .pulse-card { padding-bottom:1.05rem; }
        .compact-heading { align-items:center; }
        .conversion-block { display:flex; align-items:center; gap:1rem; padding:.15rem 1.25rem 1.05rem; }
        .conversion-ring { width:86px; height:86px; flex:0 0 auto; display:grid; place-items:center; border-radius:50%; background:conic-gradient(var(--dash-coral) calc(var(--conversion) * 1%), #eef0f5 0); position:relative; }
        .conversion-ring:before { content:""; position:absolute; inset:8px; border-radius:50%; background:#fff; }
        .conversion-ring div { position:relative; display:flex; flex-direction:column; align-items:center; }
        .conversion-ring strong { font-size:1.15rem; line-height:1; }
        .conversion-ring span { color:var(--dash-muted); font-size:.54rem; margin-top:.22rem; }
        .conversion-copy { display:flex; flex-direction:column; }
        .conversion-copy strong { font-size:.77rem; }
        .conversion-copy span { color:var(--dash-muted); font-size:.63rem; line-height:1.45; margin-top:.25rem; }
        .pulse-stats { display:grid; grid-template-columns:repeat(3,1fr); margin:0 1.25rem; border:1px solid var(--dash-line); border-radius:12px; }
        .pulse-stats div { position:relative; padding:.75rem .65rem; border-right:1px solid var(--dash-line); }
        .pulse-stats div:last-child { border-right:0; }
        .pulse-stats p { color:var(--dash-muted); font-size:.56rem; margin:0 0 .15rem; white-space:nowrap; }
        .pulse-stats strong { font-size:.95rem; }
        .pulse-dot { position:absolute; width:5px; height:5px; right:.55rem; top:.55rem; border-radius:50%; }
        .pulse-dot.coral { background:var(--dash-coral); } .pulse-dot.violet { background:var(--dash-violet); } .pulse-dot.blue { background:var(--dash-blue); }
        .quote-breakdown { display:grid; gap:.5rem; padding:.9rem 1.25rem 0; }
        .quote-breakdown div, .quote-breakdown a { display:flex; justify-content:space-between; gap:1rem; color:var(--dash-muted); font-size:.61rem; text-decoration:none; }
        .quote-breakdown a { padding-top:.45rem; border-top:1px solid var(--dash-line); }
        .quote-breakdown a:hover { color:var(--dash-violet); }
        .quote-breakdown strong { color:var(--dash-ink); font-size:.62rem; }

        .trips-card { margin-bottom:.9rem; }
        .trip-row { display:grid; grid-template-columns:48px minmax(170px,1.5fr) minmax(120px,1fr) 85px 88px 14px; align-items:center; gap:.9rem; color:inherit; text-decoration:none; border-top:1px solid var(--dash-line); padding:.7rem 1.25rem; transition:.18s ease; }
        .trip-row:hover { color:inherit; background:#fafbfe; }
        .date-tile { width:43px; height:45px; display:flex; flex-direction:column; align-items:center; justify-content:center; border:1px solid #e4e7ee; border-radius:10px; background:#fafbfc; }
        .date-tile small { color:var(--dash-coral); font-size:.54rem; font-weight:800; text-transform:uppercase; }
        .date-tile strong { font-size:.9rem; line-height:1.05; }
        .trip-main, .trip-client, .trip-countdown { display:flex; flex-direction:column; min-width:0; }
        .trip-main strong, .trip-client strong, .trip-countdown strong { overflow:hidden; color:var(--dash-ink); font-size:.72rem; text-overflow:ellipsis; white-space:nowrap; }
        .trip-main small, .trip-client small, .trip-countdown small { overflow:hidden; color:var(--dash-muted); font-size:.6rem; text-overflow:ellipsis; white-space:nowrap; margin-top:.15rem; }
        .trip-client small, .trip-countdown small { margin:0 0 .15rem; }
        .status-pill { display:inline-flex; justify-content:center; border-radius:20px; padding:.32rem .48rem; font-size:.57rem; font-weight:800; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .status-pill.blue { color:#3d64d6; background:var(--dash-blue-soft); }
        .status-pill.violet { color:#6558c2; background:var(--dash-violet-soft); }
        .status-pill.coral { color:#cf5846; background:var(--dash-coral-soft); }

        .bottom-grid { grid-template-columns:minmax(0,1.5fr) minmax(290px,.8fr); }
        .chart-card { min-height:360px; }
        .chart-wrap { position:relative; height:275px; padding:0 1rem 1rem .8rem; }
        .chart-legend { display:flex; gap:.8rem; color:var(--dash-muted); font-size:.62rem; margin-top:.55rem; }
        .legend-dot { display:inline-block; width:7px; height:7px; border-radius:50%; margin-right:.2rem; }
        .legend-dot.revenue { background:var(--dash-violet); } .legend-dot.profit { background:var(--dash-mint); }
        .activity-list { border-top:1px solid var(--dash-line); }
        .activity-item { display:grid; grid-template-columns:34px minmax(100px,1fr) auto; align-items:center; gap:.65rem; padding:.72rem 1.15rem; text-decoration:none; border-bottom:1px solid #f0f2f6; }
        .activity-item:last-child { border-bottom:0; }
        .activity-item:hover { background:#fafbfe; }
        .activity-mark { width:32px; height:32px; display:grid; place-items:center; border-radius:9px; font-size:.72rem; }
        .activity-meta { text-align:right; }
        .activity-meta strong { color:var(--dash-ink); font-size:.65rem; }
        .activity-meta small { color:var(--dash-muted); font-size:.55rem; margin-top:.16rem; }

        .empty-state { min-height:110px; display:flex; align-items:center; justify-content:center; gap:.9rem; padding:1.2rem; border-top:1px solid var(--dash-line); }
        .empty-state.compact { min-height:120px; }
        .empty-icon { width:42px; height:42px; display:grid; place-items:center; flex:0 0 auto; color:var(--dash-mint); background:var(--dash-mint-soft); border-radius:13px; font-size:1.1rem; }
        .empty-icon.plane { color:var(--dash-violet); background:var(--dash-violet-soft); }
        .empty-state strong { font-size:.78rem; }
        .empty-state p { color:var(--dash-muted); font-size:.66rem; margin:.2rem 0 0; }
        .empty-state .btn-soft { margin-left:auto; }

        .quick-create { display:grid; grid-template-columns:minmax(150px,.75fr) repeat(3,minmax(170px,1fr)); align-items:center; gap:.7rem; border-radius:17px; padding:.85rem 1rem; color:#fff; background:#1c2944; box-shadow:0 10px 25px rgba(23,35,60,.1); }
        .quick-create .section-kicker { color:#95a1b6; }
        .quick-create h2 { margin-bottom:0; }
        .quick-create > a { display:grid; grid-template-columns:36px minmax(0,1fr) 14px; align-items:center; gap:.65rem; color:#fff; text-decoration:none; border:1px solid rgba(255,255,255,.11); border-radius:12px; padding:.65rem; transition:.18s ease; }
        .quick-create > a:hover { background:rgba(255,255,255,.07); transform:translateY(-1px); }
        .quick-icon { width:34px; height:34px; display:grid; place-items:center; border-radius:9px; }
        .quick-create > a > span:nth-child(2) { display:flex; flex-direction:column; min-width:0; }
        .quick-create a strong { font-size:.68rem; }
        .quick-create a small { color:#aeb8ca; font-size:.56rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-top:.13rem; }
        .quick-create a > i { color:#8d99ae; font-size:.68rem; }

        @media (max-width: 1199.98px) {
            .metric-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
            .dashboard-grid, .bottom-grid { grid-template-columns:1fr; }
            .quick-create { grid-template-columns:repeat(3,1fr); }
            .quick-create > div:first-child { grid-column:1 / -1; }
        }
        @media (max-width: 767.98px) {
            .dashboard-hero { align-items:flex-start; flex-direction:column; gap:1rem; }
            .hero-actions { width:100%; }
            .hero-actions > *, .hero-actions .btn-primary-action { flex:1; }
            .metric-grid { grid-template-columns:1fr; }
            .action-item { grid-template-columns:36px minmax(0,1fr) auto 12px; }
            .action-type { display:none; }
            .trip-row { grid-template-columns:46px minmax(0,1fr) auto 12px; }
            .trip-client, .trip-countdown { display:none; }
            .quick-create { grid-template-columns:1fr; }
            .quick-create > div:first-child { grid-column:auto; }
            .card-heading { padding:1rem; }
            .chart-legend { display:none; }
        }
        @media (max-width: 430px) {
            .hero-actions { align-items:stretch; flex-direction:column; }
            .action-item { gap:.5rem; padding:.7rem .85rem; }
            .due-label { max-width:68px; overflow:hidden; text-overflow:ellipsis; }
            .trip-row { gap:.55rem; padding:.7rem .85rem; }
            .status-pill { max-width:70px; }
            .pulse-stats p { white-space:normal; }
            .empty-state { align-items:flex-start; flex-wrap:wrap; }
            .empty-state .btn-soft { margin-left:0; }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('salesChart');
            if (!canvas || typeof Chart === 'undefined') return;

            const context = canvas.getContext('2d');
            const revenue = @json($chartData['total_revenue']);
            const gradient = context.createLinearGradient(0, 0, 0, 270);
            gradient.addColorStop(0, 'rgba(117, 104, 217, .25)');
            gradient.addColorStop(1, 'rgba(117, 104, 217, 0)');

            new Chart(context, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Revenue',
                            data: revenue,
                            borderColor: '#7568d9',
                            backgroundColor: gradient,
                            borderWidth: 2.5,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            pointBackgroundColor: '#7568d9',
                            fill: true,
                            tension: .38
                        },
                        {
                            label: 'Estimated profit',
                            data: @json($chartData['profit']),
                            borderColor: '#21a179',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            fill: false,
                            tension: .38
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#17233c',
                            padding: 10,
                            cornerRadius: 9,
                            callbacks: {
                                label: function (item) {
                                    return ' ' + item.dataset.label + ': MYR ' + Number(item.raw || 0).toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { color: '#8a94a7', font: { size: 10, family: 'Plus Jakarta Sans' } }
                        },
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: '#eef0f5', drawTicks: false },
                            ticks: {
                                color: '#8a94a7',
                                padding: 8,
                                font: { size: 9, family: 'Plus Jakarta Sans' },
                                callback: function (value) {
                                    if (value >= 1000000) return 'MYR ' + (value / 1000000).toFixed(1) + 'm';
                                    if (value >= 1000) return 'MYR ' + (value / 1000).toFixed(0) + 'k';
                                    return 'MYR ' + value;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
