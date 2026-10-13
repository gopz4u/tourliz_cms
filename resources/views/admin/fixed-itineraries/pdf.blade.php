<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixed Itinerary - {{ $itinerary->title }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 25px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #0056b3; padding-bottom: 15px; }
        .header h1 { color: #0056b3; margin: 0 0 8px 0; font-size: 26px; }
        .header p { margin: 0; color: #666; font-size: 13px; }
        .package-info { margin-bottom: 25px; background: #f8f9fa; padding: 15px; border-radius: 6px; }
        .package-info table { width: 100%; border-collapse: collapse; }
        .package-info td { padding: 6px; vertical-align: top; font-size: 13px; }
        .package-info .label { font-weight: bold; width: 25%; color: #555; }
        .itinerary-details { margin-bottom: 30px; }
        .itinerary-details h2 { color: #0056b3; font-size: 20px; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-top: 0; }
        .day-block { margin-bottom: 20px; page-break-inside: avoid; border-left: 3px solid #0056b3; padding-left: 12px; }
        .day-title { font-weight: bold; font-size: 15px; margin-bottom: 6px; color: #222; }
        .day-content { font-size: 13px; color: #444; }
        .day-items { margin-top: 8px; padding-left: 10px; background: #fdfdfd; }
        .day-item-line { font-size: 11px; margin-bottom: 3px; color: #495057; }
        .list-section { margin-top: 20px; }
        .list-section h3 { color: #0056b3; font-size: 15px; margin-bottom: 6px; border-bottom: 1px solid #eee; padding-bottom: 4px; }
        .list-section ul { margin: 0; padding-left: 20px; font-size: 12px; }
        .price-section { text-align: right; margin-top: 30px; font-size: 22px; font-weight: bold; color: #0a58ca; background: #f0f7ff; padding: 18px; border-radius: 6px; border: 1px solid #cce5ff; }
        .footer { text-align: center; margin-top: 40px; font-size: 11px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Tourliz Holidays</h1>
        <p>123 Travel Street, Kuala Lumpur, Malaysia | +60 123 456 789 | hello@tourliz.com</p>
    </div>

    <div class="package-info">
        <table>
            <tr>
                <td class="label">Package:</td>
                <td style="font-weight: bold; color: #0056b3;">{{ $itinerary->title }}</td>
                <td class="label">Duration:</td>
                <td>{{ $itinerary->duration }}</td>
            </tr>
            <tr>
                <td class="label">Primary Destination:</td>
                <td>{{ $itinerary->destination->name ?? 'Multiple Destinations' }}</td>
                <td class="label">{{ $itinerary->suppliers->count() > 1 ? 'Suppliers:' : 'Supplier:' }}</td>
                <td>
                    @if($itinerary->suppliers->count() > 0)
                        {{ $itinerary->suppliers->map(function($s) { return $s->name . ' (' . ($s->category_label ?? 'Vendor') . ')'; })->join(', ') }}
                    @else
                        {{ $itinerary->supplier->name ?? 'Direct Inventory' }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Countries Covered:</td>
                <td>
                    @if($itinerary->countries->count() > 0)
                        {{ $itinerary->countries->pluck('name')->join(', ') }}
                    @else
                        Malaysia
                    @endif
                </td>
                <td class="label">Date Issued:</td>
                <td>{{ now()->format('d M Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="itinerary-details">
        <h2>Itinerary Overview</h2>
        @if($itinerary->short_description)
            <p style="font-style: italic; color: #555;">{{ $itinerary->short_description }}</p>
        @endif
        @if($itinerary->itinerary_description)
            <p>{!! nl2br(e($itinerary->itinerary_description)) !!}</p>
        @endif

        @if(is_array($itinerary->days) && count($itinerary->days) > 0)
            <h2 style="margin-top: 25px;">Day-by-Day Schedule</h2>
            @foreach($itinerary->days as $index => $rawDay)
                @php
                    $day = (array) $rawDay;
                    $dayNum = $day['day_number'] ?? ($index + 1);
                    $dayItems = is_array($day['items'] ?? null) ? $day['items'] : [];
                @endphp
                <div class="day-block">
                    <div class="day-title">Day {{ $dayNum }}: {{ $day['title'] ?? 'Day ' . $dayNum }}</div>
                    <div class="day-content">
                        @if(!empty($day['description']))
                            <div>{!! nl2br(e($day['description'])) !!}</div>
                        @endif

                        @if(count($dayItems) > 0)
                            <div class="day-items">
                                @foreach($dayItems as $rawItem)
                                    @php $item = (array) $rawItem; @endphp
                                    <div class="day-item-line">
                                        <strong>[{{ strtoupper($item['item_type'] ?? 'ITEM') }}]</strong>
                                        {{ $item['title'] ?? '' }}
                                        @if(!empty($item['room_type'])) <em>({{ $item['room_type'] }})</em> @endif
                                        @if(!empty($item['description'])) - {{ $item['description'] }} @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($day['overnight_location']))
                            <div style="margin-top: 4px; font-size: 11px; color: #666;">
                                <em>Overnight: {{ $day['overnight_location'] }}</em>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif

        @if(!empty($itinerary->inclusions))
            <div class="list-section">
                <h3>Inclusions</h3>
                <ul>
                    @foreach($itinerary->inclusions as $inc)
                        <li>{{ $inc }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!empty($itinerary->exclusions))
            <div class="list-section">
                <h3>Exclusions</h3>
                <ul>
                    @foreach($itinerary->exclusions as $exc)
                        <li>{{ $exc }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!empty($itinerary->terms_conditions))
            <div class="list-section">
                <h3>Terms & Conditions</h3>
                <p style="font-size: 11px; color: #555;">{!! nl2br(e($itinerary->terms_conditions)) !!}</p>
            </div>
        @endif
    </div>

    <div class="price-section">
        <div style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">
            Total Fixed Package Price: {{ $itinerary->currency ?: 'MYR' }} {{ number_format($itinerary->fixed_price, 2) }}
        </div>
        <div style="font-size: 12px; font-weight: normal; color: #555;">
            Multi-Currency Equivalents: 
            @foreach($itinerary->converted_prices as $c)
                @if($c['code'] !== ($itinerary->currency ?: 'MYR'))
                    <span style="display: inline-block; margin-right: 15px;"><strong>{{ $c['code'] }}:</strong> {{ $c['symbol'] }} {{ number_format($c['amount'], 2) }}</span>
                @endif
            @endforeach
        </div>
    </div>

    <div class="footer">
        This is an official fixed package quotation generated by Tourliz Holidays.<br>
        Thank you for choosing Tourliz Holidays!
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
