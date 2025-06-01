<?php

declare(strict_types=1);

use Filament\Pages\Auth\Login;

return [
    'spa_url_exceptions' => [],

    'theme' => [
        'css' => env('FILAMENT_THEME_CSS'),
        'logo' => [
            'light' => env('FILAMENT_LOGO_LIGHT_PATH'),
            'dark' => env('FILAMENT_DARK_PATH'),
        ],
        'favicon' => env('FILAMENT_FAVICON_PATH'),
        'font' => env('FILAMENT_FONT', 'Jost'),
        'colours' => [],
    ],

    'login_page' => Login::class,

    'success_notification_titles' => [
        'All Done and Dusted! 🎉', 'Mission Accomplished! 🚀', 'You Nailed It! 🎯', 'Great Job! 🎊',
        'That’s Taken Care Of! ✅', 'Sweet! All Set! 🍬', 'Done and Done! ✔️', 'Smooth Sailing! ⛵',
        'Cheers to That! 🥂', "Success! You're Awesome! 🌟",
    ],

    'tenancy' => [
        'model' => null,
        'panel_cache_minutes' => env('FILAMENT_PANEL_CACHE_MINUTES', 10),
    ],

    'plugins' => [],

    'default_retention_months' => 3,
];
