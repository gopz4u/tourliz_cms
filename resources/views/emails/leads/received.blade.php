<!DOCTYPE html>
<html>
<head>
    <title>New Lead Received</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>New Lead Received</h2>
    
    <p>A new lead has been generated from <strong>{{ $lead->source }}</strong>.</p>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ddd; width: 150px;"><strong>Name:</strong></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $lead->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Email:</strong></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $lead->email ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Phone:</strong></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $lead->phone ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Date Received:</strong></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $lead->created_at->format('M d, Y H:i A') }}</td>
        </tr>
    </table>
    
    <br>
    <p>Please log in to the CMS to view more details.</p>
</body>
</html>
