<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Feature Flags / Switches
    |--------------------------------------------------------------------------
    |
    | These switches control whether modules like HR Management or Website Control
    | are active and visible in the software.
    |
    | When set to false in .env:
    | 1. Navigation menus are completely hidden.
    | 2. Direct URL access to their routes aborts with 404.
    |
    */

    'hr_management' => (bool) env('ENABLE_HR_MANAGEMENT', false),
    'website_control' => (bool) env('ENABLE_WEBSITE_CONTROL', false),
];
