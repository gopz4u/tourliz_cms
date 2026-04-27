<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Proposal for {{ $client_name }}</title>
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
            border-bottom: 2px solid #3498db;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .agency-name {
            font-size: 18pt;
            font-weight: bold;
            color: #2c3e50;
        }

        .proposal-title {
            font-size: 24pt;
            font-weight: bold;
            color: #3498db;
            margin-top: 20px;
            text-transform: uppercase;
        }

        .client-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #3498db;
            margin-bottom: 30px;
        }

        .trip-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .trip-detail-item {
            display: table-cell;
            width: 33%;
            padding: 10px;
            background-color: #ecf0f1;
            text-align: center;
        }

        .trip-detail-label {
            font-size: 9pt;
            text-transform: uppercase;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .trip-detail-value {
            font-size: 12pt;
            font-weight: bold;
            color: #2c3e50;
        }

        .day-block {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .day-header {
            font-size: 14pt;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .day-number {
            color: #3498db;
            margin-right: 10px;
        }

        .activity-time {
            font-weight: bold;
            color: #7f8c8d;
            width: 80px;
            display: inline-block;
        }

        .price-section {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: right;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .price-label {
            font-size: 14pt;
            text-transform: uppercase;
        }

        .total-price {
            font-size: 24pt;
            font-weight: bold;
            color: #f1c40f;
        }

        .terms {
            margin-top: 50px;
            font-size: 9pt;
            color: #7f8c8d;
            border-top: 1px solid #ecf0f1;
            padding-top: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
            padding-left: 15px;
            border-left: 2px solid #ecf0f1;
        }

        .highlight {
            font-weight: bold;
            color: #2980b9;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="agency-name">{{ $agency->name ?? 'Travel Agency' }}</div>
        <div style="font-size: 10pt; color: #7f8c8d;">{{ $agency->email ?? '' }}</div>
    </div>

    <div class="proposal-title">{{ $itinerary->title }}</div>

    <div class="client-info">
        <strong>Prepared For:</strong> {{ $itinerary->client_name }}<br>
        <strong>Date:</strong> {{ \Carbon\Carbon::parse($generated_at)->format('F d, Y') }}
    </div>

    <div class="trip-details">
        <div class="trip-detail-item">
            <div class="trip-detail-label">Destination</div>
            <div class="trip-detail-value">{{ $itinerary->destination->name ?? 'Custom Tour' }}</div>
        </div>
        <div class="trip-detail-item">
            <div class="trip-detail-label">Duration</div>
            <div class="trip-detail-value">{{ $itinerary->duration_days }} Days</div>
        </div>
        <div class="trip-detail-item">
            <div class="trip-detail-label">Start Date</div>
            <div class="trip-detail-value">
                {{ $itinerary->start_date ? $itinerary->start_date->format('M d, Y') : 'TBD' }}</div>
        </div>
    </div>

    <h3>Itinerary Breakdown</h3>

    @foreach($enrichedItinerary as $day)
        <div class="day-block">
            <div class="day-header">
                <span class="day-number">Day {{ $day['day'] }}</span> {{ $day['title'] }}
            </div>

            <div style="padding-left: 10px;">
                @if(!empty($day['places']))
                    <p><strong><span style="color:#e67e22">📍 Places:</span></strong>
                        @foreach($day['places'] as $place)
                            {{ $place['name'] }}@if(!$loop->last), @endif
                        @endforeach
                    </p>
                @endif

                @if(!empty($day['activities']))
                    <ul>
                        @foreach($day['activities'] as $activity)
                            <li>
                                @if(isset($activity['time']))
                                    <span class="activity-time">{{ $activity['time'] }}</span>
                                @endif
                                <span class="highlight">{{ $activity['name'] }}</span>
                                @if(isset($activity['description']))
                                    <br><small>{{ $activity['description'] }}</small>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if(!empty($day['hotel']))
                    <p><strong>🏨 Accommodation:</strong> {{ $day['hotel']['name'] ?? 'Hotel' }}
                        @if(isset($day['hotel']['type']))
                            ({{ $day['hotel']['type'] }})
                        @endif
                    </p>
                @endif

                @if(!empty($day['meals']))
                    <p><strong>🍽️ Meals:</strong>
                        B: {{ $day['meals']['breakfast'] ?? '-' }} |
                        L: {{ $day['meals']['lunch'] ?? '-' }} |
                        D: {{ $day['meals']['dinner'] ?? '-' }}
                    </p>
                @endif
            </div>
        </div>
    @endforeach

    <div class="price-section">
        <div class="price-label">Total Investment</div>
        <div class="total-price">{{ $itinerary->currency }} {{ number_format($itinerary->total_price, 2) }}</div>
        <div style="font-size: 10pt; opacity: 0.8;">Includes all taxes and fees</div>
    </div>

    <div class="terms">
        <strong>Terms & Conditions:</strong><br>
        This proposal is valid for 7 days from the date of issue. Prices are subject to availability at the time of
        booking.
        A deposit of 20% is required to confirm the booking. Full payment is due 30 days prior to departure.
        Cancellation policies apply as per standard terms.
    </div>
</body>

</html>