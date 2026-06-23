<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ data_get($itinerary, 'title', 'Proposal') }} — Tourliz</title>
    <style>
        @page {
            margin: 20mm 18mm 20mm 18mm;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #2d3436;
            font-size: 10.5pt;
            line-height: 1.6;
        }

        /* ── HEADER ── */
        .header-table {
            width: 100%;
            margin-bottom: 22px;
            border-bottom: 3px solid #1a73e8;
            padding-bottom: 14px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 140px;
        }

        .logo-img {
            height: 52px;
        }

        .logo-text {
            font-size: 22pt;
            font-weight: 900;
            color: #1a73e8;
            letter-spacing: 2px;
        }

        .logo-text span {
            color: #ff6b35;
        }

        .company-info {
            text-align: right;
            font-size: 9pt;
            color: #636e72;
            line-height: 1.5;
        }

        /* ── TITLE SECTION ── */
        .proposal-title {
            font-size: 20pt;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .proposal-subtitle {
            font-size: 10pt;
            color: #636e72;
            margin-bottom: 6px;
        }

        .ref-badge {
            display: inline-block;
            background: #e8f0fe;
            color: #1a73e8;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: 600;
        }

        /* ── TRIP SUMMARY BAR ── */
        .summary-bar {
            width: 100%;
            margin: 16px 0 20px 0;
            border-collapse: collapse;
        }

        .summary-bar td {
            background: #f8f9fb;
            padding: 10px 14px;
            border-right: 1px solid #e0e0e0;
            text-align: center;
            font-size: 9.5pt;
        }

        .summary-bar td:last-child {
            border-right: none;
        }

        .summary-label {
            color: #636e72;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }

        .summary-value {
            font-weight: 700;
            color: #1a1a2e;
        }

        /* ── DAY BLOCK ── */
        .day-block {
            margin-bottom: 16px;
            page-break-inside: avoid;
            border: 1px solid #e8ecf1;
            border-radius: 4px;
            overflow: hidden;
        }

        .day-head {
            background: #1a73e8;
            color: #fff;
            padding: 9px 14px;
            font-weight: 700;
            font-size: 11pt;
        }

        .day-head .day-num {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 9px;
            border-radius: 3px;
            margin-right: 8px;
            font-size: 9pt;
        }

        .day-body {
            padding: 12px 14px;
        }

        .day-section {
            margin-bottom: 10px;
            padding-left: 4px;
            border-left: 3px solid #dfe6e9;
            padding: 6px 0 6px 12px;
            margin-bottom: 8px;
        }

        .day-section-label {
            font-size: 8pt;
            font-weight: 700;
            color: #636e72;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .day-section-content {
            font-size: 10pt;
            color: #2d3436;
        }

        .day-icon {
            color: #1a73e8;
            font-size: 9pt;
        }

        /* ── PRICING TABLE ── */
        .pricing-section {
            margin-top: 24px;
            page-break-inside: avoid;
        }

        .section-heading {
            font-size: 13pt;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #1a73e8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pricing-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 16px;
        }

        .pricing-table thead th {
            background: #1a73e8;
            color: #fff;
            padding: 8px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pricing-table tbody td {
            padding: 7px 12px;
            border-bottom: 1px solid #e8ecf1;
            vertical-align: top;
        }

        .pricing-table tbody tr:nth-child(even) td {
            background: #fafbfc;
        }

        .price-amount {
            color: #e67e22;
            font-weight: 700;
            white-space: nowrap;
        }

        .price-sub {
            font-size: 8pt;
            color: #999;
            font-weight: normal;
        }

        /* ── GRAND TOTAL BOX ── */
        .grand-total-box {
            background: #1a1a2e;
            color: #fff;
            padding: 20px 24px;
            margin-top: 20px;
            page-break-inside: avoid;
            border-radius: 4px;
        }

        .grand-total-table {
            width: 100%;
        }

        .grand-total-table td {
            padding: 4px 0;
            vertical-align: middle;
        }

        .grand-label {
            font-size: 10pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.85;
        }

        .grand-amount {
            font-size: 20pt;
            font-weight: 900;
            text-align: right;
        }

        .grand-perpax {
            font-size: 9pt;
            opacity: 0.7;
            text-align: right;
            padding-top: 4px;
        }

        .grand-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 6px 0;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 30px;
            padding-top: 14px;
            border-top: 1px solid #dfe6e9;
            text-align: center;
            font-size: 8.5pt;
            color: #999;
            line-height: 1.6;
        }

        .footer .brand {
            font-weight: 700;
            color: #1a73e8;
            font-size: 9pt;
        }

        /* ── INTERNAL PAGE ── */
        .internal-stamp {
            background: #e74c3c;
            color: #fff;
            padding: 8px 16px;
            text-align: center;
            font-weight: 700;
            font-size: 10pt;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 18px;
        }

        .internal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .internal-table th {
            background: #f1f2f6;
            padding: 8px 12px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #dfe6e9;
        }

        .internal-table td {
            padding: 8px 12px;
            border: 1px solid #dfe6e9;
        }

        .internal-table .highlight-row td {
            background: #fff3e0;
            font-weight: 700;
            color: #d35400;
        }

        /* ── TERMS BOX ── */
        .terms-box {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .terms-box .section-heading {
            font-size: 11pt;
        }

        .terms-list {
            font-size: 9pt;
            color: #636e72;
            padding-left: 18px;
            line-height: 1.8;
        }

        .terms-list li {
            margin-bottom: 3px;
        }
    </style>
</head>

<body>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- HEADER --}}
    {{-- ═══════════════════════════════════════════ --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if(data_get($agency, 'logo'))
                    <img src="{{ data_get($agency, 'logo') }}" class="logo-img" alt="Logo">
                @else
                    <div class="logo-text">TOUR<span>LIZ</span></div>
                @endif
            </td>
            <td class="company-info">
                <strong>{{ data_get($agency, 'company_name', 'Tourliz') }}</strong><br>
                @if(data_get($agency, 'whatsapp_number'))
                    {{ data_get($agency, 'whatsapp_number') }}<br>
                @endif
                {{ !empty($generated_at) ? 'Generated: ' . $generated_at : '' }}
            </td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TITLE --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="proposal-title">{{ data_get($itinerary, 'title', 'Travel Proposal') }}</div>
    <div class="proposal-subtitle">
        Prepared for <strong>{{ ucwords(strtolower(data_get($itinerary, 'client_name', 'Valued Guest'))) }}</strong>
    </div>
    <span class="ref-badge">{{ data_get($itinerary, 'quote_id', '') }}</span>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TRIP SUMMARY BAR --}}
    {{-- ═══════════════════════════════════════════ --}}
    <table class="summary-bar">
        <tr>
            <td>
                <span class="summary-label">Destination</span>
                <span class="summary-value">{{ data_get($itinerary, 'destination.name', 'N/A') }}</span>
            </td>
            <td>
                <span class="summary-label">Duration</span>
                <span class="summary-value">{{ data_get($itinerary, 'duration_days', 0) }} Days /
                    {{ (int) data_get($itinerary, 'duration_days', 1) - 1 }} Nights</span>
            </td>
            <td>
                <span class="summary-label">Travelers</span>
                <span class="summary-value">{{ data_get($itinerary, 'adults', 1) }} Adults
                    @if(data_get($itinerary, 'children_2_6', 0) > 0)
                        + {{ data_get($itinerary, 'children_2_6') }} Child
                    @endif
                    @if(data_get($itinerary, 'children_6_11', 0) > 0)
                        + {{ data_get($itinerary, 'children_6_11') }} Child
                    @endif
                </span>
            </td>
            @if(data_get($itinerary, 'start_date'))
                <td>
                    <span class="summary-label">Tour Starts</span>
                    <span class="summary-value">{{ \Carbon\Carbon::parse($itinerary->start_date)->format('d M Y') }}</span>
                </td>
            @endif
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- DAY-BY-DAY ITINERARY --}}
    {{-- ═══════════════════════════════════════════ --}}
    @foreach($enrichedItinerary as $day)
        <div class="day-block">
            <div class="day-head">
                <span class="day-num">DAY {{ $day['day'] }}</span>
                {{ $day['title'] }}
            </div>
            <div class="day-body">

                {{-- Places / Attractions --}}
                @if(!empty($day['places']))
                    <div class="day-section">
                        <div class="day-section-label">&#9733; Places to Visit</div>
                        <div class="day-section-content">
                            @foreach($day['places'] as $p)
                                {{ $p['attraction_name'] ?? ($p['name'] ?? ($p['place_name'] ?? '')) }}
                                @if(!empty($p['description']))
                                    <br><small style="color:#636e72;">{{ $p['description'] }}</small>
                                @endif
                                @if(!$loop->last)<br>@endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Activities --}}
                @if(!empty($day['activities']))
                    <div class="day-section">
                        <div class="day-section-label">&#9874; Activities</div>
                        <div class="day-section-content">
                            @foreach($day['activities'] as $act)
                                <strong>{{ $act['name'] ?? 'Activity' }}</strong>
                                @if(!empty($act['description']))
                                    <br><small style="color:#636e72;">{{ $act['description'] }}</small>
                                @endif
                                @if(!$loop->last)<br><br>@endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Tourist Spots / Points of Interest --}}
                @if(!empty($day['spots']))
                    <div class="day-section">
                        <div class="day-section-label">&#9673; Points of Interest</div>
                        <div class="day-section-content">
                            @foreach($day['spots'] as $spot)
                                <strong>{{ $spot['name'] ?? 'Spot' }}</strong>
                                @if(!empty($spot['description']))
                                    <br><small style="color:#636e72;">{{ $spot['description'] }}</small>
                                @endif
                                @if(!$loop->last)<br>@endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Meals --}}
                @if(!empty($day['meals']))
                    <div class="day-section">
                        <div class="day-section-label">&#9749; Meals</div>
                        <div class="day-section-content">
                            @foreach($day['meals'] as $m)
                                {{ $m['name'] ?? 'Meal' }}@if(!$loop->last), @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Hotel --}}
                @if(!empty($day['hotel']['name']))
                    <div class="day-section">
                        <div class="day-section-label">&#9730; Overnight Stay</div>
                        <div class="day-section-content">
                            <strong>{{ $day['hotel']['name'] }}</strong>
                            @if(!empty($day['hotel']['type']))
                                <span style="color:#636e72;">({{ $day['hotel']['type'] }})</span>
                            @endif
                        </div>
                    </div>
                @endif
                @if(!empty($day['hotels']))
                    @foreach($day['hotels'] as $h)
                        @if(!empty($h['name']))
                            <div class="day-section">
                                <div class="day-section-label">&#9730; Overnight Stay</div>
                                <div class="day-section-content">
                                    <strong>{{ $h['name'] }}</strong>
                                    @if(!empty($h['type']))
                                        <span style="color:#636e72;">({{ $h['type'] }})</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif

                {{-- Transport --}}
                @if(!empty($day['transport']))
                    <div class="day-section">
                        <div class="day-section-label">&#9992; Transport</div>
                        <div class="day-section-content">
                            @foreach($day['transport'] as $t)
                                {{ $t['name'] ?? 'Transport' }}@if(!$loop->last), @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Day Notes --}}
                @if(!empty($day['notes']))
                    <div
                        style="margin-top:8px; padding:8px 10px; background:#fef9e7; border-left:3px solid #f39c12; font-size:9pt; color:#7d6608; font-style:italic;">
                        "{{ $day['notes'] }}"
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    {{-- ═══════════════════════════════════════════ --}}
    {{-- PRICING BREAKDOWN (Customer View) --}}
    {{-- ═══════════════════════════════════════════ --}}
    @php
        $allHotels = [];
        $allTransport = [];
        $allTickets = [];
        $allActivities = [];
        $allMeals = [];

        foreach ($enrichedItinerary as $day) {
            if (!empty($day['hotels'])) {
                foreach ($day['hotels'] as $h) {
                    if (!empty($h['name'])) {
                        $allHotels[] = $h;
                    }
                }
            } elseif (!empty($day['hotel']['name'])) {
                $allHotels[] = $day['hotel'];
            }
            if (!empty($day['transport'])) {
                foreach ($day['transport'] as $t) {
                    if (!empty($t['name'])) {
                        $allTransport[] = $t;
                    }
                }
            }
            if (!empty($day['activities'])) {
                foreach ($day['activities'] as $act) {
                    if (!empty($act['name'])) {
                        $allActivities[] = $act;
                    }
                }
            }
            if (!empty($day['places'])) {
                foreach ($day['places'] as $p) {
                    if (!empty($p['attraction_name']) || !empty($p['name'])) {
                        $allTickets[] = $p;
                    }
                }
            }
            if (!empty($day['meals'])) {
                foreach ($day['meals'] as $m) {
                    if (!empty($m['name'])) {
                        $allMeals[] = $m;
                    }
                }
            }
        }

        $totalPrice = (float) data_get($itinerary, 'total_price', 0);
        if ($totalPrice == 0) {
            $totalPrice = (float) data_get($itinerary, 'base_cost', 0) + (float) data_get($itinerary, 'markup_amount', 0);
        }

        $adults = (int) data_get($itinerary, 'adults', 1);
        $c1 = (int) data_get($itinerary, 'children_2_6', 0);
        $c2 = (int) data_get($itinerary, 'children_6_11', 0);
        $totalPax = $adults + $c1 + $c2;
        $perPaxCost = $totalPax > 0 ? ($totalPrice / $totalPax) : 0;
    @endphp

    @if(count($allHotels) > 0 || count($allTransport) > 0 || count($allActivities) > 0 || count($allTickets) > 0 || count($allMeals) > 0)
        <div class="pricing-section">
            <div class="section-heading">Package Inclusions</div>

            <table class="pricing-table">
                <thead>
                    <tr>
                        <th width="45%">Item</th>
                        <th width="30%">Details</th>
                        @if(!$is_public)
                            <th width="25%">Cost</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    {{-- Hotels --}}
                    @foreach($allHotels as $h)
                        @php
                            $hqty = (int) ($h['quantity'] ?? 1);
                            $hprice = (float) ($h['price_per_night'] ?? 0);
                            $haddon = (float) ($h['add_on_price'] ?? 0);
                        @endphp
                        <tr>
                            <td><strong>{{ $h['name'] }}</strong></td>
                            <td>
                                @if(!empty($h['type'])){{ $h['type'] }}@endif
                                @if($hqty > 1)<br><small>{{ $hqty }} rooms</small>@endif
                            </td>
                            @if(!$is_public)
                                <td class="price-amount">
                                    {{ data_get($itinerary, 'currency', 'MYR') }}
                                    {{ number_format(($hprice + $haddon) * $hqty, 0) }}
                                    @if($haddon > 0)<br><span class="price-sub">incl. {{ number_format($haddon, 0) }}
                                    addon</span>@endif
                                </td>
                            @endif
                        </tr>
                    @endforeach

                    {{-- Transport --}}
                    @foreach($allTransport as $t)
                        <tr>
                            <td><strong>{{ $t['name'] }}</strong></td>
                            <td>Transport</td>
                            @if(!$is_public)
                                <td class="price-amount">
                                    {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format($t['price'] ?? 0, 0) }}
                                </td>
                            @endif
                        </tr>
                    @endforeach

                    {{-- Activities & Tickets --}}
                    @foreach(array_merge($allActivities, $allTickets) as $item)
                        @php
                            $name = $item['name'] ?? ($item['attraction_name'] ?? 'Item');
                            $et = $item['entry_ticket'] ?? null;
                            $hours = (float) ($item['hours'] ?? 0);
                            $rate = (float) ($item['price_per_hour'] ?? 0);
                            $cost = 0;
                            if ($et) {
                                $cost = (($et['adult_price'] ?? $et['price'] ?? 0) * ($et['adult_qty'] ?? 0)) +
                                    (($et['child_2_6_price'] ?? 0) * ($et['child_2_6_qty'] ?? 0)) +
                                    (($et['child_6_11_price'] ?? 0) * ($et['child_6_11_qty'] ?? 0));
                                if ($cost == 0)
                                    $cost = (float) ($et['price'] ?? 0);
                            }
                            if ($hours > 0 && $rate > 0) {
                                $cost += $hours * $rate;
                            }
                            $paxInfo = '';
                            if ($et) {
                                $parts = [];
                                if (($et['adult_qty'] ?? 0) > 0)
                                    $parts[] = $et['adult_qty'] . ' Adult';
                                if (($et['child_2_6_qty'] ?? 0) > 0)
                                    $parts[] = $et['child_2_6_qty'] . ' Child (2-6)';
                                if (($et['child_6_11_qty'] ?? 0) > 0)
                                    $parts[] = $et['child_6_11_qty'] . ' Child (6-11)';
                                $paxInfo = implode(', ', $parts);
                            }
                            if ($hours > 0) {
                                $paxInfo .= ($paxInfo ? ' | ' : '') . $hours . ' hrs';
                            }
                        @endphp
                        <tr>
                            <td><strong>{{ $name }}</strong></td>
                            <td>{{ $paxInfo ?: 'Activity' }}</td>
                            @if(!$is_public)
                                <td class="price-amount">
                                    {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format($cost, 0) }}
                                </td>
                            @endif
                        </tr>
                    @endforeach

                    {{-- Meals --}}
                    @foreach($allMeals as $m)
                        @php $mqty = (int) ($m['quantity'] ?? 1); @endphp
                        <tr>
                            <td><strong>{{ $m['name'] }}</strong></td>
                            <td>@if($mqty > 1) x{{ $mqty }} persons @endif</td>
                            @if(!$is_public)
                                <td class="price-amount">
                                    {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format(($m['price'] ?? 0) * $mqty, 0) }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- GRAND TOTAL --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="grand-total-box">
        <table class="grand-total-table">
            <tr>
                <td class="grand-label">Total Package Cost</td>
                <td class="grand-amount">
                    {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format($totalPrice, 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="grand-divider"></div>
                </td>
            </tr>
            <tr>
                <td class="grand-label" style="font-size:8pt;">Per Person Estimate</td>
                <td class="grand-perpax">
                    {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format($perPaxCost, 2) }}
                    <small>(based on {{ $totalPax }} pax)</small>
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TERMS & NOTES --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="terms-box">
        <div class="section-heading" style="font-size:11pt;">Terms &amp; Notes</div>
        <ul class="terms-list">
            <li>All prices are quoted in {{ data_get($itinerary, 'currency', 'MYR') }} and are subject to availability
                at the time of booking.</li>
            <li>Rates may vary during peak season, public holidays, and special events.</li>
            <li>Full payment is required to confirm all reservations.</li>
            <li>Cancellation charges may apply based on proximity to travel dates.</li>
            <li>This proposal is valid for 7 days from the date of issue.</li>
            <li>Travel insurance is strongly recommended and is not included in this quote.</li>
        </ul>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- FOOTER --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="footer">
        <div class="brand">{{ data_get($agency, 'company_name', 'Tourliz') }}</div>
        <div>
            @if(data_get($agency, 'whatsapp_number')) {{ data_get($agency, 'whatsapp_number') }} &nbsp;|&nbsp; @endif
            We craft unforgettable travel experiences
        </div>
        <div style="margin-top:2px;font-size:7.5pt;">Thank you for the opportunity to serve you!</div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- INTERNAL PAGE (Admin Only) --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if(!$is_public)
        <div style="page-break-before: always; margin-top: 20px;">
            <div class="internal-stamp">&#9888; Internal — For Office Use Only</div>

            <div class="section-heading">Cost Sheet</div>

            <table class="internal-table" style="margin-bottom:16px;">
                <thead>
                    <tr>
                        <th>Component</th>
                        <th style="text-align:right;">Amount ({{ data_get($itinerary, 'currency', 'MYR') }})</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Base Cost (Net Supplier Cost)</td>
                        <td style="text-align:right;">{{ number_format(data_get($itinerary, 'base_cost', 0), 2) }}</td>
                    </tr>
                    <tr>
                        <td>Markup ({{ data_get($itinerary, 'markup_percentage', 0) }}%)</td>
                        <td style="text-align:right;">{{ number_format(data_get($itinerary, 'markup_amount', 0), 2) }}</td>
                    </tr>
                    <tr class="highlight-row">
                        <td><strong>Selling Price</strong></td>
                        <td style="text-align:right;">
                            <strong>{{ number_format(data_get($itinerary, 'total_price', 0), 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <table class="internal-table">
                <thead>
                    <tr>
                        <th>Additional Info</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Quote Reference</td>
                        <td><strong>{{ data_get($itinerary, 'quote_id', '') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>{{ ucfirst(data_get($itinerary, 'status', 'draft')) }}</td>
                    </tr>
                    <tr>
                        <td>Payment Status</td>
                        <td>{{ ucfirst(data_get($itinerary, 'payment_status', 'pending')) }}</td>
                    </tr>
                    @if(data_get($itinerary, 'total_amount_received', 0) > 0)
                        <tr>
                            <td>Amount Received</td>
                            <td>{{ number_format(data_get($itinerary, 'total_amount_received', 0), 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td>Generated On</td>
                        <td>{{ now()->format('d M Y, h:i A') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

</body>

</html>