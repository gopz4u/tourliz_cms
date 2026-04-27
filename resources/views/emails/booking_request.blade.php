<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Booking Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #667eea;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }
        .section {
            background-color: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }
        .section h3 {
            margin-top: 0;
            color: #667eea;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            min-width: 150px;
        }
        .info-value {
            flex: 1;
        }
        .price-breakdown {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .price-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .total-price {
            font-size: 1.2em;
            font-weight: bold;
            color: #667eea;
            border-top: 2px solid #667eea;
            padding-top: 10px;
            margin-top: 10px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: bold;
        }
        .badge-paid {
            background-color: #28a745;
            color: white;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #333;
        }
        .badge-partial {
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Booking Request Received</h1>
    </div>
    
    <div class="content">
        <!-- Package Information -->
        <div class="section">
            <h3>📦 Package Information</h3>
            <div class="info-row">
                <span class="info-label">Package Name:</span>
                <span class="info-value">{{ $payload['package']->name ?? 'N/A' }}</span>
            </div>
            @if(isset($payload['package']->destination))
            <div class="info-row">
                <span class="info-label">Place:</span>
                <span class="info-value">{{ $payload['package']->destination->name ?? 'N/A' }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Travel Date:</span>
                <span class="info-value">{{ isset($payload['travel_date']) ? date('F j, Y', strtotime($payload['travel_date'])) : 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Adults:</span>
                <span class="info-value">{{ $payload['adults'] ?? 0 }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Children:</span>
                <span class="info-value">{{ $payload['children'] ?? 0 }}</span>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="section">
            <h3>👤 Customer Information</h3>
            <div class="info-row">
                <span class="info-label">Full Name:</span>
                <span class="info-value">{{ $payload['name'] ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $payload['email'] ?? 'N/A' }}</span>
            </div>
            @if(!empty($payload['phone']))
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $payload['phone'] }}</span>
            </div>
            @endif
            
            @if(!empty($payload['customer_address']) || !empty($payload['customer_city']))
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value">
                    @if(!empty($payload['customer_address'])){{ $payload['customer_address'] }}@endif
                    @if(!empty($payload['customer_city'])){{ !empty($payload['customer_address']) ? ', ' : '' }}{{ $payload['customer_city'] }}@endif
                    @if(!empty($payload['customer_state'])){{ ', ' . $payload['customer_state'] }}@endif
                    @if(!empty($payload['customer_postal_code'])){{ ' ' . $payload['customer_postal_code'] }}@endif
                    @if(!empty($payload['customer_country'])){{ ', ' . $payload['customer_country'] }}@endif
                </span>
            </div>
            @endif
        </div>

        <!-- Contact Preference -->
        <div class="section">
            <h3>📞 Contact Preference</h3>
            <div class="info-row">
                <span class="info-label">Preferred Method:</span>
                <span class="info-value">
                    @if(isset($payload['contact_method']))
                        @if($payload['contact_method'] == 'whatsapp')
                            WhatsApp
                        @elseif($payload['contact_method'] == 'phone')
                            Phone Call
                        @elseif($payload['contact_method'] == 'query')
                            Query Form
                        @else
                            Email
                        @endif
                    @else
                        Email
                    @endif
                </span>
            </div>
            @if(!empty($payload['whatsapp_number']))
            <div class="info-row">
                <span class="info-label">WhatsApp Number:</span>
                <span class="info-value">{{ $payload['whatsapp_number'] }}</span>
            </div>
            @endif
        </div>

        <!-- Add-ons -->
        @if(!empty($payload['addons']) && is_array($payload['addons']) && count($payload['addons']) > 0)
        <div class="section">
            <h3>➕ Package Add-ons</h3>
            @php
                $packageAddons = $payload['package']->addon_amenities ?? [];
            @endphp
            @foreach($payload['addons'] as $addonKey)
                @if(isset($packageAddons[$addonKey]))
                <div class="info-row">
                    <span class="info-value">• {{ $packageAddons[$addonKey]['name'] ?? $addonKey }}</span>
                    @if(isset($packageAddons[$addonKey]['price']) && $packageAddons[$addonKey]['price'] > 0)
                        <span class="info-value">({{ $payload['package']->currency ?? 'USD' }} {{ number_format($packageAddons[$addonKey]['price'], 2) }})</span>
                    @endif
                </div>
                @endif
            @endforeach
        </div>
        @endif

        <!-- Add-on Services -->
        @if(isset($payload['addon_services']) && $payload['addon_services']->count() > 0)
        <div class="section">
            <h3>🛎️ Add-on Services</h3>
            @foreach($payload['addon_services'] as $service)
            <div class="info-row">
                <span class="info-value">• {{ $service->name }}</span>
                @if($service->price > 0)
                    <span class="info-value">({{ $service->currency ?? 'USD' }} {{ number_format($service->price, 2) }})</span>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <!-- Payment Information -->
        <div class="section">
            <h3>💳 Payment Information</h3>
            <div class="info-row">
                <span class="info-label">Payment Status:</span>
                <span class="info-value">
                    @if(isset($payload['payment_status']))
                        @if($payload['payment_status'] == 'paid')
                            <span class="badge badge-paid">Paid</span>
                        @elseif($payload['payment_status'] == 'partially_paid')
                            <span class="badge badge-partial">Partially Paid</span>
                        @else
                            <span class="badge badge-pending">Pending</span>
                        @endif
                    @else
                        <span class="badge badge-pending">Pending</span>
                    @endif
                </span>
            </div>
            
            @if(!empty($payload['payment_details']))
                @if(!empty($payload['payment_details']['method']))
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value">{{ $payload['payment_details']['method'] }}</span>
                </div>
                @endif
                @if(!empty($payload['payment_details']['transaction_id']))
                <div class="info-row">
                    <span class="info-label">Transaction ID:</span>
                    <span class="info-value">{{ $payload['payment_details']['transaction_id'] }}</span>
                </div>
                @endif
                @if(!empty($payload['payment_details']['amount']))
                <div class="info-row">
                    <span class="info-label">Payment Amount:</span>
                    <span class="info-value">{{ $payload['package']->currency ?? 'USD' }} {{ number_format($payload['payment_details']['amount'], 2) }}</span>
                </div>
                @endif
                @if(!empty($payload['payment_details']['date']))
                <div class="info-row">
                    <span class="info-label">Payment Date:</span>
                    <span class="info-value">{{ date('F j, Y', strtotime($payload['payment_details']['date'])) }}</span>
                </div>
                @endif
            @endif
        </div>

        <!-- Price Breakdown -->
        <div class="section">
            <h3>💰 Price Breakdown</h3>
            <div class="price-breakdown">
                <div class="price-item">
                    <span>Base Price:</span>
                    <strong>{{ $payload['package']->currency ?? 'USD' }} {{ number_format($payload['base_price'] ?? ($payload['package']->price ?? 0), 2) }}</strong>
                </div>
                @if(isset($payload['addons_amount']) && $payload['addons_amount'] > 0)
                <div class="price-item">
                    <span>Add-ons:</span>
                    <strong>{{ $payload['package']->currency ?? 'USD' }} {{ number_format($payload['addons_amount'], 2) }}</strong>
                </div>
                @endif
                @if(isset($payload['services_amount']) && $payload['services_amount'] > 0)
                <div class="price-item">
                    <span>Services:</span>
                    <strong>{{ $payload['package']->currency ?? 'USD' }} {{ number_format($payload['services_amount'], 2) }}</strong>
                </div>
                @endif
                @if(isset($payload['discount_amount']) && $payload['discount_amount'] > 0)
                <div class="price-item">
                    <span>Discount:</span>
                    <strong>- {{ $payload['package']->currency ?? 'USD' }} {{ number_format($payload['discount_amount'], 2) }}</strong>
                </div>
                @endif
                <div class="price-item total-price">
                    <span>Total Amount:</span>
                    <span>{{ $payload['package']->currency ?? 'USD' }} {{ number_format($payload['total_amount'] ?? ($payload['package']->price ?? 0), 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        @if(!empty($payload['notes']))
        <div class="section">
            <h3>📝 Additional Notes</h3>
            <p>{{ $payload['notes'] }}</p>
        </div>
        @endif

        <!-- User Info (if logged in) -->
        @if(isset($payload['user']))
        <div class="section">
            <h3>👤 User Account</h3>
            <div class="info-row">
                <span class="info-label">User ID:</span>
                <span class="info-value">{{ $payload['user']->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">User Email:</span>
                <span class="info-value">{{ $payload['user']->email }}</span>
            </div>
        </div>
        @endif

        @if(isset($payload['booking']))
        <div class="section">
            <p><small>Booking ID: #{{ $payload['booking']->id }} | Created: {{ $payload['booking']->created_at->format('F j, Y g:i A') }}</small></p>
        </div>
        @endif
    </div>
</body>
</html>
