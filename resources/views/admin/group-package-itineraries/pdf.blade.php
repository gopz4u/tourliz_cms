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
            font-size: 11pt;
        }

        .header {
            background-color: #1f2937;
            color: white;
            padding: 25px 40px;
            margin-bottom: 25px;
        }

        .logo {
            font-size: 26pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: -1px;
        }

        .logo span {
            color: #3b82f6;
        }

        .package-title {
            font-size: 22pt;
            font-weight: 800;
            margin-bottom: 5px;
            color: #111827;
            padding: 0 40px;
        }

        .package-meta {
            color: #6b7280;
            font-size: 11pt;
            margin-bottom: 35px;
            padding: 0 40px;
            font-weight: 500;
        }

        .content {
            padding: 0 40px;
        }

        .day-block {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }

        .day-header {
            background-color: #3b82f6;
            color: white;
            padding: 12px 20px;
            font-weight: 800;
            font-size: 15pt;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .day-content {
            padding-left: 20px;
            border-left: 4px solid #f3f4f6;
        }

        .section-title {
            font-weight: 800;
            color: #374151;
            margin-top: 15px;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-size: 9pt;
            letter-spacing: 1px;
        }

        .item-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .item-list li {
            margin-bottom: 12px;
            background-color: #f9fafb;
            padding: 10px 15px;
            border-radius: 6px;
        }

        .price-tag {
            float: right;
            font-weight: 700;
            color: #059669;
            background-color: #ecfdf5;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10pt;
        }

        .summary-box {
            background-color: #111827;
            color: white;
            padding: 30px;
            margin-top: 50px;
            border-radius: 12px;
            page-break-inside: avoid;
        }

        .summary-title {
            font-size: 16pt;
            font-weight: 800;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 15px;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 9pt;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding: 30px 40px;
        }

        .notes-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            color: #1e40af;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin-top: 15px;
            font-size: 10pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .total-amount {
            font-size: 24pt;
            font-weight: 900;
            color: #3b82f6;
        }

        .cost-label {
            font-size: 10pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.6);
        }
    </style>
</head>

<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="logo">Tour<span>liz</span></div>
                    <div style="font-size: 9pt; opacity: 0.7;">Premium Group Tour Experiences</div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div style="font-size: 10pt; opacity: 0.9;">Document ID: GP-{{ $package->id }}-{{ now()->format('Ymd') }}</div>
                    <div style="font-size: 10pt; opacity: 0.9;">Created: {{ now()->format('d M Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="package-title">{{ $package->name }}</div>
    <div class="package-meta">
        {{ $package->duration }} &bull; {{ $package->destination->name ?? 'Special Tour' }} &bull; {{ $package->package_category ?? 'Group Package' }}
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
                        <div class="section-title">Destinations & Spots</div>
                        <ul class="item-list">
                            @foreach($day['places'] as $place)
                                <li>
                                    <strong style="color: #111827;">{{ $place['name'] }}</strong>
                                    @if(isset($place['visit_duration']))
                                        <span style="color: #6b7280; font-size: 0.9em; margin-left: 5px;">&bull; {{ $place['visit_duration'] }} visit</span>
                                    @endif
                                    @if(!($isCustomer ?? false) && isset($place['entry_ticket']) && $place['entry_ticket']['required'] && $place['entry_ticket']['price'] > 0)
                                        <span class="price-tag">{{ $place['entry_ticket']['currency'] }} {{ number_format($place['entry_ticket']['price'], 2) }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <!-- Activities -->
                    @if(!empty($day['activities']))
                        <div class="section-title">Planned Activities</div>
                        <ul class="item-list">
                            @foreach($day['activities'] as $activity)
                                <li>
                                    <strong style="color: #111827;">{{ $activity['name'] }}</strong>
                                    @if(isset($activity['time']))
                                        <span style="color: #3b82f6; font-weight: 600; font-size: 0.9em; margin-left:10px;">{{ $activity['time'] }}</span>
                                    @endif
                                    @if(!($isCustomer ?? false) && isset($activity['entry_ticket']) && $activity['entry_ticket']['price'] > 0)
                                        <span class="price-tag">{{ $activity['entry_ticket']['currency'] }} {{ number_format($activity['entry_ticket']['price'], 2) }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <!-- Hotel & Transport Table -->
                    @if(!empty($day['hotel']) || !empty($day['transport']))
                    <table style="width: 100%; margin-top: 15px;">
                        <tr>
                            @if(!empty($day['hotel']) && !empty($day['hotel']['name']))
                            <td width="48%" style="background-color: #f0fdf4; padding: 15px; border-radius: 8px;">
                                <div class="section-title" style="margin-top: 0; color: #166534;">Stay</div>
                                <strong style="font-size: 11pt;">{{ $day['hotel']['name'] }}</strong><br>
                                <small style="color: #15803d;">Category: {{ $day['hotel']['type'] ?? 'Standard' }}</small>
                            </td>
                            @endif
                            
                            @if(!empty($day['transport']))
                            <td width="4%"></td>
                            <td width="48%" style="background-color: #eff6ff; padding: 15px; border-radius: 8px;">
                                <div class="section-title" style="margin-top: 0; color: #1e40af;">Transport</div>
                                @foreach($day['transport'] as $transport)
                                    <div style="margin-bottom: 5px;">
                                        <strong>{{ $transport['type'] }}</strong><br>
                                        <small style="color: #1d4ed8;">{{ $transport['mode'] ?? 'Local' }} &bull; {{ $transport['from'] }} &rarr; {{ $transport['to'] }}</small>
                                    </div>
                                @endforeach
                            </td>
                            @endif
                        </tr>
                    </table>
                    @endif

                    <!-- Meals -->
                    @if(!empty($day['meals']))
                        <div class="section-title">Meals</div>
                        <table style="background-color: #fffbeb; padding: 12px; border-radius: 8px;">
                            <tr>
                                <td><strong style="color: #92400e;">Breakfast:</strong> {{ $day['meals']['breakfast'] ?? 'No' }}</td>
                                <td><strong style="color: #92400e;">Lunch:</strong> {{ $day['meals']['lunch'] ?? 'No' }}</td>
                                <td><strong style="color: #92400e;">Dinner:</strong> {{ $day['meals']['dinner'] ?? 'No' }}</td>
                            </tr>
                        </table>
                    @endif

                    <!-- Notes -->
                    @if(!empty($day['notes']))
                        <div class="notes-box">
                            <strong>Note for the day:</strong><br>
                            {{ $day['notes'] }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Final Summary -->
        <div class="summary-box">
            <div class="summary-title">{{ ($isCustomer ?? false) ? 'Package Summary' : 'Total Group Package Costing' }}</div>
            
            <table>
                @if(!($isCustomer ?? false))
                <tr>
                    <td style="padding-bottom: 20px;">
                        <span class="cost-label">Accommodation</span><br>
                        <span style="font-size: 13pt; font-weight: 700;">{{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['hotels'], 2) }}</span>
                    </td>
                    <td style="padding-bottom: 20px;">
                        <span class="cost-label">Transportation</span><br>
                        <span style="font-size: 13pt; font-weight: 700;">{{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['transport'], 2) }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="cost-label">Activities & Entry</span><br>
                        <span style="font-size: 13pt; font-weight: 700;">{{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['activities'] + $costBreakdown['entry_tickets'], 2) }}</span>
                    </td>
                @else
                <tr>
                @endif
                    <td>
                        <span class="cost-label">{{ ($isCustomer ?? false) ? 'Final Package Price' : 'Estimated Total Price' }}</span><br>
                        <span class="total-amount">{{ $costBreakdown['currency'] }} {{ number_format($totalPrice ?? $costBreakdown['total'], 2) }}</span>
                    </td>
                </tr>
            </table>

            <div style="margin-top: 30px; font-size: 9pt; opacity: 0.6; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                * This is a dynamic group package proposal. Prices are calculated based on group size and current vendor availability.
                * Taxes and service charges are included unless stated otherwise.
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>TOURLIZ CMS</strong> &bull; Premium Travel Solutions</p>
        <p>www.tourliz.com &bull; help@tourliz.com</p>
    </div>
</body>

</html>