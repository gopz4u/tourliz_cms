<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $payload;

    /**
    * Create a new message instance.
    */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
    * Build the message.
    */
    public function build()
    {
        $packageName = $this->payload['package']->name ?? 'Package';

        return $this->subject('New Booking Request - ' . $packageName)
            ->view('emails.booking_request');
    }
}

