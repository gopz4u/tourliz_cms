<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #5a52e5; }
        .logo { font-size: 24px; font-weight: bold; color: #0f172a; text-transform: uppercase; }
        .logo span { color: #5a52e5; }
        .content { padding: 30px 0; }
        .rating { font-size: 20px; color: #fbbf24; margin-bottom: 10px; }
        .comment { font-style: italic; background: #f9fafb; padding: 15px; border-radius: 8px; border-left: 4px solid #5a52e5; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; padding-top: 20px; border-top: 1px solid #eee; }
        .button { display: inline-block; padding: 12px 25px; background-color: #5a52e5; color: #ffffff !important; text-decoration: none; border-radius: 30px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Tour<span>liz</span></div>
        </div>
        <div class="content">
            <h2>Good news, {{ $review->name }}!</h2>
            <p>Your review for <strong>{{ $review->package->name }}</strong> has been approved and is now visible on our website.</p>
            
            <div class="rating">
                @for($i=1; $i<=5; $i++)
                    {{ $i <= $review->rating ? '★' : '☆' }}
                @endfor
            </div>
            
            <div class="comment">
                "{{ $review->comment }}"
            </div>
            
            <p>Thank you for sharing your feedback. It helps other travelers make better decisions and helps us improve our services.</p>
            
            <a href="{{ config('app.url') . '/packages/' . $review->package_id }}" class="button">View Your Review</a>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Tourliz. All rights reserved.</p>
            <p>Premium Travel Experiences</p>
        </div>
    </div>
</body>
</html>
