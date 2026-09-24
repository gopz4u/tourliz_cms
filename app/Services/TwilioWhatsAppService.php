<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioWhatsAppService
{
    protected $client;
    protected $fromNumber;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->fromNumber = config('services.twilio.whatsapp_from');

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    /**
     * Send a WhatsApp message
     * 
     * @param string $to Phone number (in E.164 format, e.g., +1234567890)
     * @param string $message The message body
     * @return bool
     */
    public function sendMessage(string $to, string $message): bool
    {
        if (!$this->client || !$this->fromNumber) {
            Log::warning('Twilio is not configured properly. Cannot send WhatsApp message.');
            return false;
        }

        try {
            // Twilio requires WhatsApp numbers to be prefixed with 'whatsapp:'
            $toFormat = str_starts_with($to, 'whatsapp:') ? $to : "whatsapp:{$to}";
            $fromFormat = str_starts_with($this->fromNumber, 'whatsapp:') ? $this->fromNumber : "whatsapp:{$this->fromNumber}";

            $this->client->messages->create(
                $toFormat,
                [
                    'from' => $fromFormat,
                    'body' => $message
                ]
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Twilio WhatsApp Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return false;
        }
    }
}
