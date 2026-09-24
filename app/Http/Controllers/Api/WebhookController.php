<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * Handle incoming Meta Webhooks (Verification & Lead Data)
     */
    public function metaLead(Request $request)
    {
        // 1. Webhook Verification (when setting up in Meta App Dashboard)
        if ($request->isMethod('get')) {
            $verifyToken = config('services.meta.webhook_verify_token');
            
            $mode = $request->query('hub_mode');
            $token = $request->query('hub_verify_token');
            $challenge = $request->query('hub_challenge');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                return response($challenge, 200);
            }

            return response('Forbidden', 403);
        }

        // 2. Handle incoming POST Lead Data
        $payload = $request->all();
        \Illuminate\Support\Facades\Log::info('Meta Webhook Payload Received', ['payload' => $payload]);

        if (isset($payload['entry']) && is_array($payload['entry'])) {
            foreach ($payload['entry'] as $entry) {
                if (isset($entry['changes']) && is_array($entry['changes'])) {
                    foreach ($entry['changes'] as $change) {
                        if ($change['field'] === 'leadgen') {
                            $leadgenId = $change['value']['leadgen_id'];
                            $formId = $change['value']['form_id'];
                            
                            $this->processMetaLead($leadgenId, $formId);
                        }
                    }
                }
            }
        }

        return response('EVENT_RECEIVED', 200);
    }

    /**
     * Fetch lead details from Meta Graph API and save to DB
     */
    protected function processMetaLead($leadgenId, $formId)
    {
        // Check if already processed
        if (\App\Models\Lead::where('leadgen_id', $leadgenId)->exists()) {
            return;
        }

        $accessToken = config('services.meta.system_user_access_token');
        
        if (!$accessToken) {
            \Illuminate\Support\Facades\Log::error('Meta System User Access Token is missing.');
            return;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::get("https://graph.facebook.com/v18.0/{$leadgenId}", [
                'access_token' => $accessToken
            ]);

            if ($response->successful()) {
                $leadData = $response->json();
                
                // Parse field data
                $parsedData = [];
                if (isset($leadData['field_data'])) {
                    foreach ($leadData['field_data'] as $field) {
                        $parsedData[$field['name']] = $field['values'][0] ?? null;
                    }
                }

                $lead = \App\Models\Lead::create([
                    'name' => $parsedData['full_name'] ?? ($parsedData['first_name'] ?? '') . ' ' . ($parsedData['last_name'] ?? ''),
                    'email' => $parsedData['email'] ?? null,
                    'phone' => $parsedData['phone_number'] ?? null,
                    'source' => 'Meta Ads',
                    'form_id' => $formId,
                    'leadgen_id' => $leadgenId,
                    'raw_data' => $leadData
                ]);

                // Send Notifications
                $this->sendLeadNotifications($lead);
            } else {
                \Illuminate\Support\Facades\Log::error('Meta Graph API Error', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to process Meta Lead', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send Email & WhatsApp Notifications
     */
    protected function sendLeadNotifications($lead)
    {
        // 1. Email Notification
        $adminEmail = config('mail.from.address', 'sales@tourliz.com');
        try {
            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\LeadReceivedEmail($lead));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send lead email', ['error' => $e->getMessage()]);
        }

        // 2. WhatsApp Notification
        $salesPhone = config('services.twilio.sales_whatsapp_number');
        if ($salesPhone) {
            $message = "*New Lead Received!*\n\n";
            $message .= "Name: {$lead->name}\n";
            $message .= "Phone: {$lead->phone}\n";
            $message .= "Email: {$lead->email}\n";
            $message .= "Source: {$lead->source}";

            $twilio = new \App\Services\TwilioWhatsAppService();
            $twilio->sendMessage($salesPhone, $message);
        }
    }
}
