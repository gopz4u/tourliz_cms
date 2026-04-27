<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ data_get($agency, 'company_name', 'Proposal') }} - {{ data_get($itinerary, 'title', '') }}</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 11pt;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }

        .agency-name {
            font-size: 18pt;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
        }

        .trip-title {
            font-size: 22pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .client-info {
            text-align: center;
            color: #7f8c8d;
            font-size: 12pt;
            margin-bottom: 40px;
        }

        .day-block {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .day-head {
            background: #f8f9fa;
            padding: 10px;
            font-weight: bold;
            border-left: 4px solid #3498db;
        }

        .day-body {
            padding: 10px 15px;
        }

        .section-label {
            font-size: 9pt;
            font-weight: bold;
            color: #95a5a6;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .price-box {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 9pt;
            color: #999;
        }

        .breakdown-section {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .breakdown-title {
            font-size: 14pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .breakdown-item {
            margin-bottom: 15px;
        }

        .breakdown-item-head {
            font-weight: bold;
            color: #34495e;
            font-size: 12pt;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }

        .breakdown-detail {
            font-size: 11pt;
            color: #555;
            margin-left: 10px;
        }

        .note-text {
            font-size: 10pt;
            color: #7f8c8d;
            font-style: italic;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        @if(data_get($agency, 'logo'))
            <img src="{{ data_get($agency, 'logo') }}" height="50">
        @else
            <div class="agency-name">TOURLIZ</div>
        @endif
        <div class="agency-name" style="font-size: 14pt;">{{ data_get($agency, 'company_name', '') }}</div>
        <div style="font-size: 10pt; color: #666;">
            {{ !empty($generated_at) ? "Date: " . $generated_at : "" }}
        </div>
        <div>{{ data_get($agency, 'whatsapp_number') ? "WhatsApp: " . data_get($agency, 'whatsapp_number') : "" }}</div>
    </div>

    <div style="text-align: center;">
        <div class="trip-title">{{ data_get($itinerary, 'title', '') }}</div>
        <div style="font-size: 10pt; color: #7f8c8d; margin-top: -5px; margin-bottom: 20px;">Ref ID:
            {{ data_get($itinerary, 'quote_id', '') }}
        </div>
        <div class="client-info">
            <div style="font-size: 14pt; margin-bottom: 5px;"><strong>Customer:</strong> {{ ucwords(strtolower(data_get($itinerary, 'client_name', 'Valued Guest'))) }}</div>
            <div>{{ data_get($itinerary, 'duration_days', 0) }} Days / {{ (int)data_get($itinerary, 'duration_days', 1) - 1 }} Nights | <strong>{{ data_get($itinerary, 'destination.name', '') }}</strong></div>
            @if(data_get($itinerary, 'start_date'))
                <div><strong>Tour Starts:</strong> {{ \Carbon\Carbon::parse($itinerary->start_date)->format('d M Y') }}</div>
            @endif
            <div style="margin-top: 5px;">
                <strong>Travelers:</strong> {{ data_get($itinerary, 'adults', 1) }} Adults
                @if(data_get($itinerary, 'children_2_6', 0) > 0) , {{ data_get($itinerary, 'children_2_6') }} Child (2-6y) @endif
                @if(data_get($itinerary, 'children_6_11', 0) > 0) , {{ data_get($itinerary, 'children_6_11') }} Child (6-11y) @endif
            </div>
        </div>
    </div>

    @foreach($enrichedItinerary as $day)
        <div class="day-block">
            <div class="day-head">Day {{ $day['day'] }}: {{ $day['title'] }}</div>
            <div class="day-body">
                @if(!empty($day['places']))
                    <div>
                        <span class="section-label">Places:</span>
                        @foreach($day['places'] as $p)
                            {{ $p['name'] ?? ($p['place_name'] ?? ($p['attraction_name'] ?? '')) }}@if(!$loop->last), @endif
                        @endforeach
                    </div>
                @endif

                @if(!empty($day['activities']) && count($day['activities']) > 0)
                    <div style="margin-top:10px;">
                        @foreach($day['activities'] as $act)
                            <div style="margin-bottom: 5px;">
                                <span class="section-label">Activity:</span> {{ $act['name'] ?? 'Untitled' }}
                                @if(!empty($act['description']))
                                    <div style="font-size: 11px; color: #666; margin-left: 15px;">{{ $act['description'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(!empty($day['spots']) && count($day['spots']) > 0)
                    <div style="margin-top:10px;">
                        @foreach($day['spots'] as $spot)
                            <div style="margin-bottom: 5px;">
                                <span class="section-label">Point of Interest:</span> {{ $spot['name'] ?? 'Untitled' }}
                                @if(!empty($spot['description']))
                                    <div style="font-size: 11px; color: #666; margin-left: 15px;">{{ $spot['description'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(!empty($day['places']) && count($day['places']) > 0)
                    <div style="margin-top:5px;">
                        <span class="section-label">Tickets:</span>
                        @foreach($day['places'] as $p) {{ $p['attraction_name'] ?? ($p['name'] ?? '') }}@if(!$loop->last),
                        @endif @endforeach
                    </div>
                @endif

                @if(!empty($day['meals']) && count($day['meals']) > 0)
                    <div style="margin-top:5px;">
                        <span class="section-label">Meals:</span>
                        @foreach($day['meals'] as $m) {{ $m['name'] ?? '' }}@if(!$loop->last), @endif @endforeach
                    </div>
                @endif

                @if(!empty($day['hotel']))
                    <div style="margin-top:5px;">
                        <span class="section-label">Overnight:</span> {{ $day['hotel']['name'] ?? 'Hotel' }}
                        @if(!empty($day['hotel']['type']))
                            <span style="color: #7f8c8d; font-size: 0.9em;">({{ $day['hotel']['type'] }})</span>
                        @endif
                    </div>
                @endif
                @if(!empty($day['hotels']))
                    @foreach($day['hotels'] as $h)
                        @if(!empty($h['name']))
                            <div style="margin-top:5px;">
                                <span class="section-label">Overnight:</span> {{ $h['name'] }}
                                @if(!empty($h['type']))
                                    <span style="color: #7f8c8d; font-size: 0.9em;">({{ $h['type'] }})</span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif

                @if(!empty($day['transport']) && count($day['transport']) > 0)
                    <div style="margin-top:5px;">
                        <span class="section-label">Transport:</span>
                        @foreach($day['transport'] as $t) {{ $t['name'] ?? '' }}@if(!$loop->last), @endif @endforeach
                    </div>
                @endif

                @if(!empty($day['notes']))
                    <div style="margin-top:10px; font-style: italic; color: #555;">
                        "{{ $day['notes'] }}"
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    @php
        $allHotels = [];
        $allTransport = [];
        $allTickets = [];
        $allActivities = [];
        $allMeals = [];
        $totalPax = (int) data_get($itinerary, 'adults', 1) + (int) data_get($itinerary, 'children_2_6', 0) + (int) data_get($itinerary, 'children_6_11', 0);

        foreach ($enrichedItinerary as $day) {
            // Collect Hotels
            if (!empty($day['hotels'])) {
                foreach ($day['hotels'] as $h) {
                    if (!empty($h['name'])) { $allHotels[] = $h; }
                }
            } elseif (!empty($day['hotel']['name'])) {
                $allHotels[] = $day['hotel'];
            }

            // Collect Transport
            if (!empty($day['transport'])) {
                foreach ($day['transport'] as $t) {
                    if (!empty($t['name'])) { $allTransport[] = $t; }
                }
            }

            // Collect Tickets & Activities
            if (!empty($day['activities'])) {
                foreach ($day['activities'] as $act) {
                    if (!empty($act['name'])) { $allActivities[] = $act; }
                }
            }
            if (!empty($day['places'])) {
                foreach ($day['places'] as $p) {
                    if (!empty($p['attraction_name']) || !empty($p['name'])) { $allTickets[] = $p; }
                }
            }

            // Collect Meals
            if (!empty($day['meals'])) {
                foreach ($day['meals'] as $m) {
                    if (!empty($m['name'])) { $allMeals[] = $m; }
                }
            }
        }
    @endphp

    <div class="breakdown-section">
        <div class="breakdown-title">Detailed Component Breakdown</div>

        <!-- 1. Hotel Section -->
        <div class="breakdown-item">
            <div class="breakdown-item-head">1. Accommodation & Rooms</div>
            <div class="breakdown-detail">
                @if (count($allHotels) > 0)
                    @foreach ($allHotels as $h)
                        <div style="margin-bottom: 5px;">
                            • <strong>{{ $h['name'] }}</strong> 
                            @if (!empty($h['type'])) <span style="color:#666">({{ $h['type'] }})</span> @endif
                            @php 
                                $hqty = (int)($h['quantity'] ?? 1);
                                $hprice = (float)($h['price_per_night'] ?? 0);
                                $haddon = (float)($h['add_on_price'] ?? 0);
                            @endphp
                            @if($hqty > 1) <span class="badge" style="background:#eee; padding: 2px 5px; font-size: 9pt;">{{ $hqty }} Rooms</span> @endif
                            @if(!$is_public && ($hprice > 0 || $haddon > 0))
                                <span style="color: #e67e22; font-weight: bold; margin-left: 10px;">
                                    {{ data_get($itinerary, 'currency', 'MYR') }} 
                                    {{ number_format(($hprice + $haddon) * $hqty, 0) }}
                                </span>
                                @if($haddon > 0) <small style="color:#999; font-weight:normal;">(Incl. {{ number_format($haddon, 0) }} addon)</small> @endif
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-muted small">No specific hotel details provided.</div>
                @endif
            </div>
        </div>

        <!-- 2. Transport Section -->
        <div class="breakdown-item">
            <div class="breakdown-item-head">2. Transportation</div>
            <div class="breakdown-detail">
                @if (count($allTransport) > 0)
                    @foreach ($allTransport as $t)
                        <div>• {{ $t['name'] }}
                            @if(!$is_public && !empty($t['price']))
                                <span style="color: #e67e22; font-weight: bold; margin-left: 10px;">{{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format($t['price'], 0) }}</span>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-muted small">Private transport as per itinerary included.</div>
                @endif
            </div>
        </div>

        <!-- 3. Activities & Entry Tickets Section -->
        @if(count($allActivities) > 0 || count($allTickets) > 0)
        <div class="breakdown-item">
            <div class="breakdown-item-head">3. Activities & Entry Tickets</div>
            <div class="breakdown-detail">
                @foreach (array_merge($allActivities, $allTickets) as $item)
                    @php
                        $name = $item['name'] ?? ($item['attraction_name'] ?? 'Item');
                        $et = $item['entry_ticket'] ?? null;
                        $hours = (float)($item['hours'] ?? 0);
                        $rate = (float)($item['price_per_hour'] ?? 0);
                    @endphp
                    <div style="margin-bottom: 8px;">
                        • <strong>{{ $name }}</strong>
                        @if($et)
                            <div style="font-size: 9pt; color: #666; margin-left: 15px;">
                                @if(($et['adult_qty'] ?? 0) > 0) {{ $et['adult_qty'] }} Adult @endif
                                @if(($et['child_2_6_qty'] ?? 0) > 0) , {{ $et['child_2_6_qty'] }} Child (2-6) @endif
                                @if(($et['child_6_11_qty'] ?? 0) > 0) , {{ $et['child_6_11_qty'] }} Child (6-11) @endif
                                
                                @if(!$is_public)
                                    @php
                                        $cost = (($et['adult_price'] ?? $et['price'] ?? 0) * ($et['adult_qty'] ?? 0)) +
                                                (($et['child_2_6_price'] ?? 0) * ($et['child_2_6_qty'] ?? 0)) +
                                                (($et['child_6_11_price'] ?? 0) * ($et['child_6_11_qty'] ?? 0));
                                        if($cost == 0) $cost = (float)($et['price'] ?? 0);
                                    @endphp
                                    <span style="color: #e67e22; font-weight: bold; margin-left: 10px;">
                                        {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format($cost, 0) }}
                                    </span>
                                @endif
                            </div>
                        @endif
                        @if($hours > 0 && $rate > 0)
                            <div style="font-size: 9pt; color: #666; margin-left: 15px;">
                                Service for {{ $hours }} hrs
                                @if(!$is_public)
                                    <span style="color: #e67e22; font-weight: bold; margin-left: 10px;">
                                        {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format($hours * $rate, 0) }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- 4. Meals Section -->
        @if(count($allMeals) > 0)
        <div class="breakdown-item">
            <div class="breakdown-item-head">4. Meals & Dining</div>
            <div class="breakdown-detail">
                @foreach ($allMeals as $m)
                    <div>• {{ $m['name'] }}
                        @php $mqty = (int)($m['quantity'] ?? 1); @endphp
                        @if($mqty > 1) <span style="color:#666">(x{{ $mqty }})</span> @endif
                        @if(!$is_public && !empty($m['price']))
                            <span style="color: #e67e22; font-weight: bold; margin-left: 10px;">
                                {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format($m['price'] * $mqty, 0) }}
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="price-box">
        <div style="font-size: 10pt; text-transform: uppercase;">Final Proposal Quote</div>
        
        @php
            $totalPrice = (float)data_get($itinerary, 'total_price', 0);
            if ($totalPrice == 0) {
                 $totalPrice = (float)data_get($itinerary, 'base_cost', 0) + (float)data_get($itinerary, 'markup_amount', 0);
            }
            
            $adults = (int)data_get($itinerary, 'adults', 1);
            $c1 = (int)data_get($itinerary, 'children_2_6', 0);
            $c2 = (int)data_get($itinerary, 'children_6_11', 0);
            $totalPax = $adults + $c1 + $c2;
            
            $perPaxCost = $totalPax > 0 ? ($totalPrice / $totalPax) : 0;
        @endphp

        <div style="margin: 15px 0;">
             <table style="width: 100%; color: white; font-size: 11pt;">
                <tr>
                    <td style="padding: 5px 0;">Package Cost (Total)</td>
                    <td style="text-align: right; font-weight: bold; font-size: 16pt;">
                        {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format($totalPrice, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; opacity: 0.9;">Per Person Estimate</td>
                    <td style="text-align: right;">
                        {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format($perPaxCost, 2) }}
                        <small style="opacity: 0.7; font-size: 8pt;">(For {{ $totalPax }} Pax)</small>
                    </td>
                </tr>
             </table>
        </div>
        
        <div style="font-size: 9pt; opacity: 0.8; margin-top: 5px; border-top: 1px solid rgba(255,255,255,0.2); paddingTop: 5px;">
            *Total includes all specified services, taxes, and fees.
        </div>
    </div>

    <div class="footer">
        Thank you for choosing {{ data_get($agency, 'company_name', '') }}.<br>
        Contact us: {{ data_get($agency, 'whatsapp_number', 'Inquire for details') }}
    </div>

    @if(!$is_public)
        <div style="page-break-before: always; margin-top: 30px; border-top: 2px dashed #ccc; padding-top: 20px;">
            <div style="background: #e74c3c; color: white; padding: 10px; font-weight: bold; text-align: center; margin-bottom: 20px;">
                INTERNAL DOCUMENT - ADMIN ONLY
            </div>
            
            <table style="width: 100%; border-collapse: collapse; font-size: 10pt;">
                <thead>
                    <tr style="background: #eee;">
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Metric</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">Base Cost (Net)</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                            {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format(data_get($itinerary, 'base_cost', 0), 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">Markup Percentage</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                            {{ data_get($itinerary, 'markup_percentage', 0) }}%
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">Markup Amount</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                            {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format(data_get($itinerary, 'markup_amount', 0), 2) }}
                        </td>
                    </tr>
                    <tr style="background: #fdf2e9; font-weight: bold;">
                        <td style="border: 1px solid #ddd; padding: 8px; color: #d35400;">Total Selling Price</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right; color: #d35400;">
                            {{ data_get($itinerary, 'currency', 'MYR') }} {{ number_format(data_get($itinerary, 'total_price', 0), 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</body>

</html>