<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    protected $signature = 'webpush:generate-vapid-keys';
    protected $description = 'Generate VAPID keys for Tourliz PWA push notifications';

    public function handle(): int
    {
        try {
            $keys = VAPID::createVapidKeys();
        } catch (\Throwable $e) {
            $this->error('Unable to generate VAPID keys on this PHP/OpenSSL installation: ' . $e->getMessage());
            $this->line('Run this command on the production server, then copy the generated values into its .env file.');

            return self::FAILURE;
        }

        $this->line('Add these values to your production .env file:');
        $this->line('VAPID_PUBLIC_KEY=' . $keys['publicKey']);
        $this->line('VAPID_PRIVATE_KEY=' . $keys['privateKey']);
        $this->line('VAPID_SUBJECT=mailto:notifications@tourliz.com');

        return self::SUCCESS;
    }
}
