<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .banner-icon {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .message-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .details-table th, .details-table td {
            padding: 12px;
            border-bottom: 1px solid #edf2f7;
            text-align: left;
        }
        .details-table th {
            color: #718096;
            font-weight: 600;
            width: 40%;
        }
        .price-summary {
            background-color: #ebf4ff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .price-row.total {
            border-top: 1px solid #cbd5e0;
            padding-top: 10px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 18px;
            color: #2b6cb0;
        }
        .btn-container {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.25);
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #5a67d8;
        }
        .footer {
            background-color: #f7fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
            border-top: 1px solid #edf2f7;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            background-color: #d1fae5;
            color: #065f46;
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <span class="banner-icon">🎉</span>
            <h1>Booking Confirmed!</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">Booking ID: #{{ $booking->quote_id }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Dear {{ $booking->name ?: $booking->customer_name ?: 'Valued Customer' }},</p>
            
            <p>We are thrilled to inform you that your booking has been successfully <strong>confirmed</strong>. Thank you for choosing us for your next adventure!</p>

            <div class="message-box">
                <h3 style="margin-top: 0; color: #4a5568;">Trip Details</h3>
                <table class="details-table" style="margin-bottom: 0;">
                    <tr>
                        <th>Package</th>
                        <td>{{ $booking->package->name ?? 'Custom Package' }}</td>
                    </tr>
                    <tr>
                        <th>Destination</th>
                        <td>{{ $booking->package->destination->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Travel Date</th>
                        <td>{{ $booking->travel_date ? $booking->travel_date->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Duration</th>
                        <td>{{ $booking->package->duration ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Guests</th>
                        <td>{{ $booking->adults }} Adults, {{ $booking->children }} Children</td>
                    </tr>
                </table>
            </div>

            <div class="price-summary">
                <div class="price-row">
                    <span>Payment Status</span>
                    <span class="status-badge" style="background-color: {{ $booking->payment_status == 'paid' ? '#d1fae5' : '#fef3c7' }}; color: {{ $booking->payment_status == 'paid' ? '#065f46' : '#92400e' }};">
                        {{ ucfirst(str_replace('_', ' ', $booking->payment_status)) }}
                    </span>
                </div>
                <div class="price-row total">
                    <span>Total Amount</span>
                    <span>{{ $booking->currency }} {{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>

            <p style="margin-top: 25px;">We will send you a detailed itinerary and further instructions shortly. If you have any questions, feel free to reply to this email.</p>

            <div class="btn-container">
                <a href="{{ config('app.url') }}" class="btn">Manage Booking</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Need help? Contact our support team.</p>
        </div>
    </div>
</body>
</html>