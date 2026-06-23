<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tourliz Branding Configuration
    |--------------------------------------------------------------------------
    |
    | Central branding settings used across PDFs, emails, and the admin panel.
    | Update these to match your brand identity.
    |
    */

    'brand' => [
        'name' => env('TOURLIZ_BRAND_NAME', 'Tourliz'),
        'tagline' => env('TOURLIZ_TAGLINE', 'We craft unforgettable travel experiences'),
        'logo_path' => env('TOURLIZ_LOGO_PATH', 'img/tourliz_logo.png'),
        'whatsapp' => env('TOURLIZ_WHATSAPP', '+60 12-345 6789'),
        'email' => env('TOURLIZ_EMAIL', 'hello@tourliz.com'),
        'website' => env('TOURLIZ_WEBSITE', 'https://tourliz.com'),
        'primary_color' => env('TOURLIZ_PRIMARY_COLOR', '#1a73e8'),
        'accent_color' => env('TOURLIZ_ACCENT_COLOR', '#ff6b35'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Settings
    |--------------------------------------------------------------------------
    */

    'pdf' => [
        'default_currency' => env('TOURLIZ_DEFAULT_CURRENCY', 'MYR'),
        'proposal_validity_days' => 7,
        'include_terms' => true,
    ],

];
