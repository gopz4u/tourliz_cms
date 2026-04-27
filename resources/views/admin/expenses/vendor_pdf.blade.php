<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Booking Voucher - {{ $supplier->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 14px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }

        .voucher-title {
            font-size: 18px;
            text-transform: uppercase;
            margin-top: 5px;
            color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }

        .section-title {
            background: #2c3e50;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            background: #27ae60;
            color: white;
            border-radius: 4px;
            font-size: 12px;
        }

        .price-big {
            font-size: 20px;
            font-weight: bold;
            color: #c0392b;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">TOURLIZ OPERATIONS</div>
        <div class="voucher-title">Booking Confirmation / Voucher</div>
        <div style="margin-top: 5px;">Reference: {{ $itinerary->quote_id ?? 'ITIN-' . $itinerary->id }}</div>
    </div>

    <div class="section-title">SUPPLIER INFORMATION</div>
    <table>
        <tr>
            <th>Company Name</th>
            <td>{{ $supplier->name }}</td>
        </tr>
        <tr>
            <th>Contact Person</th>
            <td>{{ $supplier->contact_person }}</td>
        </tr>
        <tr>
            <th>Phone / Email</th>
            <td>{{ $supplier->phone }} / {{ $supplier->email }}</td>
        </tr>
    </table>

    <div class="section-title">CLIENT & TRIP DETAILS</div>
    <table>
        <tr>
            <th>Client Name</th>
            <td>{{ $itinerary->client_name }}</td>
        </tr>
        @if($itinerary->phone)
        <tr>
            <th>Guest Phone</th>
            <td>{{ $itinerary->phone }}</td>
        </tr>
        @endif
        @if($itinerary->email)
        <tr>
            <th>Guest Email</th>
            <td>{{ $itinerary->email }}</td>
        </tr>
        @endif
        <tr>
            <th>Pax Count</th>
            <td>
                {{ $itinerary->adults }} Adults
                @if($itinerary->children_2_6 > 0), {{ $itinerary->children_2_6 }} Child (2-6y)@endif
                @if($itinerary->children_6_11 > 0), {{ $itinerary->children_6_11 }} Child (6-11y)@endif
            </td>
        </tr>
        <tr>
            <th>Travel Date</th>
            <td>{{ $itinerary->start_date ? $itinerary->start_date->format('d M Y') : 'TBA' }}</td>
        </tr>
        <tr>
            <th>Duration</th>
            <td>{{ $itinerary->duration_days }} Days / {{ max(0, $itinerary->duration_days - 1) }} Nights</td>
        </tr>
        @if(!empty($hotel_details))
            <tr>
                <th>Room Type</th>
                <td>{{ $hotel_details }}</td>
            </tr>
        @endif
        <tr>
            <th>Destination</th>
            <td>{{ $itinerary->destination->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">SERVICE REQUESTED ({{ strtoupper($expense->category) }})</div>
    <table>
        <tr>
            <th>Service Date</th>
            <td>{{ $expense->expense_date->format('d M Y') }}</td>
        </tr>
        <tr>
            <th>Service Description</th>
            <td>{{ $expense->description }}</td>
        </tr>
        @if($expense->category === 'Hotel' && !empty($hotel_nights))
            <tr>
                <th>Booking Duration</th>
                <td>{{ $hotel_nights }} Night(s)</td>
            </tr>
        @endif

        @if(!empty($vehicle_types))
            <tr>
                <th>Vehicle Type(s)</th>
                <td>{{ $vehicle_types }}</td>
            </tr>
        @endif
        @if($expense->category === 'Transport' && !empty($total_pax))
            <tr>
                <th>Total Pax</th>
                <td>{{ $total_pax }} Pax</td>
            </tr>
        @endif
        <tr>
            <th>Payment Amount</th>
            <td class="price-big">{{ $expense->currency }} {{ number_format($expense->amount, 2) }}</td>
        </tr>
    </table>

    @if(!empty($trip_schedule))
        <div class="section-title">TRIP ITINERARY / SCHEDULE</div>
        <div
            style="background: #f8f9fa; padding: 10px; border: 1px solid #ddd; font-family: monospace; white-space: pre-line; margin-bottom: 20px;">
            {{ $trip_schedule }}
        </div>
    @endif

    @if($supplier->bank_name)
        <div class="section-title">PAYMENT SETTLEMENT INFO</div>
        <table>
            <tr>
                <th>Bank Name</th>
                <td>{{ $supplier->bank_name }}</td>
            </tr>
            <tr>
                <th>Account Details</th>
                <td>{{ $supplier->account_name }} - {{ $supplier->account_number }}</td>
            </tr>
            <tr>
                <th>Swift / IFSC</th>
                <td>{{ $supplier->swift_ifsc }}</td>
            </tr>
        </table>
    @endif

    <div style="margin-top: 20px;">
        <p><strong>Important Notes:</strong></p>
        <ul>
            <li>Please ensure quality service as per our standard agreement.</li>
            <li>Submission of final invoice is required for payment processing.</li>
            <li>For any issues on-ground, contact our operations desk immediately.</li>
        </ul>
    </div>

    <div class="footer">
        Generated on {{ $generated_at }} | Tourliz CMS System<br>
        This is a computer-generated voucher and does not require a physical signature.
    </div>
</body>

</html>