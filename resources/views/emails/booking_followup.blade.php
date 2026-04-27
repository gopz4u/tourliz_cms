<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Following Up on Your Inquiry</title>
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
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .package-card {
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            display: flex;
            align-items: center;
        }
        .package-icon {
            font-size: 24px;
            margin-right: 15px;
            background-color: #ebf4ff;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #4299e1;
        }
        .package-info h4 {
            margin: 0 0 5px;
            color: #2d3748;
        }
        .package-info p {
            margin: 0;
            font-size: 14px;
            color: #718096;
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
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Tour Request Follow-up</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">Regarding Quote #{{ $booking->quote_id }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Hello {{ $booking->name ?: $booking->customer_name ?: 'Valued Customer' }},</p>
            
            <p>I hope you're having a great day.</p>
            
            <p>I'm writing to follow up on your recent interest in our <strong>{{ $booking->package->name ?? 'Travel Package' }}</strong>. We're excited about the possibility of helping you plan this trip!</p>

            <div class="package-card">
                <div class="package-icon">✈️</div>
                <div class="package-info">
                    <h4>{{ $booking->package->name ?? 'Custom Package' }}</h4>
                    <p>{{ $booking->package->duration ?? 'N/A' }} • {{ $booking->adults }} Adults, {{ $booking->children }} Children</p>
                    <p>Travel Date: {{ $booking->travel_date ? $booking->travel_date->format('M d, Y') : 'TBD' }}</p>
                </div>
            </div>

            <p>Do you have any questions or specific requirements we can assist you with? Whether it's customizing the itinerary or discussing accommodation options, we are here to help.</p>

            <div class="btn-container">
                <a href="{{ config('app.url') }}" class="btn">View Package Details</a>
            </div>
            
            <p style="text-align: center; color: #718096; font-size: 0.9em;">Or simply reply to this email, and we'll get back to you immediately.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>