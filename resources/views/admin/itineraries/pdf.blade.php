<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $package->name }} - Itinerary</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            font-size: 12pt;
        }

        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 40px;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .logo span {
            color: #3498db;
        }

        .package-title {
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
            padding: 0 40px;
        }

        .package-meta {
            color: #7f8c8d;
            font-size: 12pt;
            margin-bottom: 30px;
            padding: 0 40px;
        }

        .content {
            padding: 0 40px;
        }

        .day-block {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .day-header {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 14pt;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .day-content {
            padding-left: 15px;
            border-left: 3px solid #ecf0f1;
        }

        .section-title {
            font-weight: bold;
            color: #2c3e50;
            margin-top: 10px;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 10pt;
        }

        .activity-item,
        .transport-item,
        .hotel-item {
            margin-bottom: 8px;
        }

        .price-tag {
            float: right;
            font-weight: bold;
            color: #e67e22;
        }

        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 20px;
            margin-top: 40px;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        .cost-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 5px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 16pt;
            font-weight: bold;
            color: #2c3e50;
            border-top: 2px solid #2c3e50;
            padding-top: 10px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10pt;
            color: #95a5a6;
            border-top: 1px solid #ecf0f1;
            padding-top: 20px;
        }

        ul {
            margin-top: 0;
            padding-left: 20px;
        }

        .notes-box {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            font-size: 11pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }
    </style>
</head>

<body>
    <div class="header">
        <table style="width: 100%; color: white;">
            <tr>
                <td>
                    <div class="logo">Tour<span>liz</span></div>
                    <div style="font-size: 10pt; opacity: 0.8;">Generating memorable experiences</div>
                </td>
                <td style="text-align: right; vertical-align: bottom;">
                    <div style="font-size: 11pt;">Date: {{ date('d M Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="package-title">{{ $package->name }}</div>
    <div class="package-meta">
        {{ $package->duration }} | {{ $package->destination->name ?? 'Multiple Destinations' }}
    </div>

    <div class="content">
        @foreach($enrichedItinerary as $day)
            <div class="day-block">
                <div class="day-header">
                    Day {{ $day['day'] }}: {{ $day['title'] }}
                </div>

                <div class="day-content">
                    <!-- Places -->
                    @if(!empty($day['places']))
                        <div class="section-title">Places to Visit</div>
                        <ul>
                            @foreach($day['places'] as $place)
                                <li>
                                    <strong>{{ $place['name'] }}</strong>
                                    @if(isset($place['visit_duration']))
                                        <span style="color: #7f8c8d; font-size: 0.9em;">({{ $place['visit_duration'] }})</span>
                                    @endif
                                    @if(isset($place['entry_ticket']) && $place['entry_ticket']['required'] && $place['entry_ticket']['price'] > 0)
                                        <span class="price-tag">{{ $place['entry_ticket']['currency'] }}
                                            {{ number_format($place['entry_ticket']['price'], 2) }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <!-- Activities -->
                    @if(!empty($day['activities']))
                        <div class="section-title">Activities</div>
                        <ul>
                            @foreach($day['activities'] as $activity)
                                <li>
                                    <strong>{{ $activity['name'] }}</strong>
                                    @if(isset($activity['time']))
                                        at {{ $activity['time'] }}
                                    @endif
                                    @if(isset($activity['entry_ticket']) && $activity['entry_ticket']['price'] > 0)
                                        <span class="price-tag">{{ $activity['entry_ticket']['currency'] }}
                                            {{ number_format($activity['entry_ticket']['price'], 2) }}</span>
                                    @endif
                                    @if(isset($activity['description']))
                                        <br><small style="color: #7f8c8d;">{{ $activity['description'] }}</small>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <!-- Hotel -->
                    @if(!empty($day['hotel']))
                        <div class="section-title">Accommodation</div>
                        <div class="hotel-item">
                            <strong>{{ $day['hotel']['name'] ?? 'Hotel' }}</strong>
                            @if(isset($day['hotel']['type']))
                                ({{ $day['hotel']['type'] }})
                            @endif
                            @if(isset($day['hotel']['price_per_night']) && $day['hotel']['price_per_night'] > 0)
                                <span class="price-tag">{{ $day['hotel']['currency'] ?? 'USD' }}
                                    {{ number_format($day['hotel']['price_per_night'], 2) }}</span>
                            @endif
                            @if(isset($day['hotel']['amenities']) && is_array($day['hotel']['amenities']))
                                <br><small style="color: #7f8c8d;">Amenities:
                                    {{ implode(', ', $day['hotel']['amenities']) }}</small>
                            @endif
                        </div>
                    @endif

                    <!-- Transport -->
                    @if(!empty($day['transport']))
                        <div class="section-title">Transport</div>
                        <ul>
                            @foreach($day['transport'] as $transport)
                                <li>
                                    {{ $transport['type'] }}: {{ $transport['from'] }} to {{ $transport['to'] }}
                                    @if(isset($transport['mode']))
                                        ({{ $transport['mode'] }})
                                    @endif
                                    @if(isset($transport['price']) && $transport['price'] > 0)
                                        <span class="price-tag">{{ $transport['currency'] ?? 'USD' }}
                                            {{ number_format($transport['price'], 2) }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <!-- Meals -->
                    @if(!empty($day['meals']))
                        <div class="section-title">Meals</div>
                        <table style="width: 100%; font-size: 11pt;">
                            <tr>
                                <td width="33%"><strong>Breakfast:</strong> {{ $day['meals']['breakfast'] ?? 'Not included' }}
                                </td>
                                <td width="33%"><strong>Lunch:</strong> {{ $day['meals']['lunch'] ?? 'Not included' }}</td>
                                <td width="33%"><strong>Dinner:</strong> {{ $day['meals']['dinner'] ?? 'Not included' }}</td>
                            </tr>
                        </table>
                    @endif

                    <!-- Notes -->
                    @if(!empty($day['notes']))
                        <div class="notes-box">
                            <strong>Note:</strong> {{ $day['notes'] }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Cost Summary -->
        <div class="summary-box">
            <h3 style="margin-top: 0; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Cost Breakdown</h3>

            <table style="width: 100%;">
                <tr>
                    <td style="padding: 5px 0; border-bottom: 1px dashed #eee;">Accommodation</td>
                    <td style="text-align: right; padding: 5px 0; border-bottom: 1px dashed #eee;">
                        {{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['hotels'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; border-bottom: 1px dashed #eee;">Transport & Transfers</td>
                    <td style="text-align: right; padding: 5px 0; border-bottom: 1px dashed #eee;">
                        {{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['transport'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; border-bottom: 1px dashed #eee;">Activities & Tours</td>
                    <td style="text-align: right; padding: 5px 0; border-bottom: 1px dashed #eee;">
                        {{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['activities'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; border-bottom: 1px dashed #eee;">Entry Tickets</td>
                    <td style="text-align: right; padding: 5px 0; border-bottom: 1px dashed #eee;">
                        {{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['entry_tickets'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 15px 0 0 0; font-size: 16pt; font-weight: bold;">TOTAL ESTIMATED COST</td>
                    <td
                        style="text-align: right; padding: 15px 0 0 0; font-size: 16pt; font-weight: bold; color: #3498db;">
                        {{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['total'], 2) }}
                    </td>
                </tr>
            </table>
            <p style="font-size: 10pt; color: #7f8c8d; margin-top: 20px;">
                * Prices are estimates and subject to change based on availability and season.
                * Flight tickets are not included unless specified.
            </p>
        </div>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Tourliz CMS. All rights reserved.</p>
        <p>Contact us: support@tourliz.com | +1 234 567 890</p>
    </div>
</body>

</html>