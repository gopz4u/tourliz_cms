<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation - {{ $itinerary->title }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0056b3; padding-bottom: 20px; }
        .header h1 { color: #0056b3; margin: 0 0 10px 0; font-size: 28px; }
        .header p { margin: 0; color: #666; font-size: 14px; }
        .customer-info { margin-bottom: 30px; background: #f8f9fa; padding: 15px; border-radius: 5px; }
        .customer-info table { width: 100%; border-collapse: collapse; }
        .customer-info td { padding: 5px; vertical-align: top; }
        .customer-info .label { font-weight: bold; width: 30%; color: #555; }
        .itinerary-details { margin-bottom: 30px; }
        .itinerary-details h2 { color: #0056b3; font-size: 22px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .day-block { margin-bottom: 20px; page-break-inside: avoid; }
        .day-title { font-weight: bold; font-size: 18px; margin-bottom: 10px; color: #444; }
        .day-content { margin-left: 15px; }
        .price-section { text-align: right; margin-top: 40px; font-size: 24px; font-weight: bold; color: #d9534f; background: #f9f9f9; padding: 20px; border-radius: 5px; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Tourliz Holidays</h1>
        <p>123 Travel Street, Kuala Lumpur, Malaysia | +60 123 456 789 | hello@tourliz.com</p>
    </div>

    <div class="customer-info">
        <table>
            <tr>
                <td class="label">Customer Name:</td>
                <td>{{ $itinerary->b2cDetail->customer_name ?? 'N/A' }}</td>
                <td class="label">Quotation Ref:</td>
                <td>{{ $itinerary->b2cDetail->quotation_reference ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Travel Dates:</td>
                <td>
                    {{ $itinerary->b2cDetail->travel_date_start ? \Carbon\Carbon::parse($itinerary->b2cDetail->travel_date_start)->format('d M Y') : 'TBD' }}
                    @if($itinerary->b2cDetail->travel_date_end)
                        - {{ \Carbon\Carbon::parse($itinerary->b2cDetail->travel_date_end)->format('d M Y') }}
                    @endif
                </td>
                <td class="label">Date Issued:</td>
                <td>{{ now()->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Pax:</td>
                <td>{{ $itinerary->b2cDetail->adults ?? 2 }} Adults, {{ $itinerary->b2cDetail->children ?? 0 }} Children</td>
                <td class="label">Rooms:</td>
                <td>{{ $itinerary->b2cDetail->rooms ?? 1 }}</td>
            </tr>
        </table>
    </div>

    <div class="itinerary-details">
        <h2>{{ $itinerary->title }}</h2>
        <p><strong>Duration:</strong> {{ $itinerary->duration_days }} Days, {{ $itinerary->duration_nights }} Nights</p>
        <p><strong>Destination:</strong> {{ $itinerary->destination->name ?? 'N/A' }}</p>
        
        <p>{{ $itinerary->description }}</p>

        @if($itinerary->days->count() > 0)
            <h3 style="margin-top: 30px; color: #0056b3;">Daily Itinerary</h3>
            @foreach($itinerary->days->sortBy('day_number') as $day)
                <div class="day-block">
                    <div class="day-title">Day {{ $day->day_number }}: {{ $day->title }}</div>
                    <div class="day-content">
                        {!! nl2br(e($day->description)) !!}
                        @if($day->items && $day->items->count() > 0)
                            <div style="margin-top: 6px; padding-left: 10px; border-left: 2px solid #0056b3;">
                                @foreach($day->items as $item)
                                    <div style="font-size: 11px; margin-bottom: 2px; color: #495057;">
                                        <strong>[{{ strtoupper($item->item_type) }}]</strong> {{ $item->title }}
                                        @if($item->room_type) <em>({{ $item->room_type }})</em> @endif
                                        @if($item->description) - {{ $item->description }} @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @if($day->overnight_location)
                            <br><small><em>Overnight: {{ $day->overnight_location }}</em></small>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
        
        @if($itinerary->b2cDetail->inclusions)
            <h3 style="margin-top: 30px; color: #0056b3;">Inclusions</h3>
            <div style="margin-left: 15px;">
                {!! nl2br(e($itinerary->b2cDetail->inclusions)) !!}
            </div>
        @endif
        
        @if($itinerary->b2cDetail->exclusions)
            <h3 style="margin-top: 30px; color: #0056b3;">Exclusions</h3>
            <div style="margin-left: 15px;">
                {!! nl2br(e($itinerary->b2cDetail->exclusions)) !!}
            </div>
        @endif
    </div>

    <div class="price-section">
        <div style="font-size: 22px; font-weight: bold; margin-bottom: 6px;">
            Total Package Price: {{ $itinerary->currency_symbol }} {{ number_format($itinerary->b2cDetail->selling_price ?? 0, 2) }}
            <span style="font-size: 14px; color: #666; font-weight: normal;">({{ $itinerary->currency ?: 'MYR' }})</span>
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
        This is a computer-generated quotation and does not require a signature. <br>
        Thank you for choosing Tourliz Holidays!
    </div>

    <script>
        // Auto-print the window for easy PDF saving
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
