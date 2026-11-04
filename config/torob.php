<?php

return [
    // ============================================================
    //  INBOUND (ترب → ما) — Torob v3 Product / Order API
    // ============================================================
    'public_key'        => env('TOROB_PUBLIC_KEY'),
    'jwt_algo'          => env('TOROB_JWT_ALGO', 'EdDSA'),
    'token_header'      => env('TOROB_TOKEN_HEADER', 'X-Torob-Token'),
    'expected_audience' => env('TOROB_EXPECTED_AUDIENCE'),
    'per_page'          => (int) env('TOROB_PER_PAGE', 100),
    'order_tracking_enabled' => env('TOROB_ORDER_TRACKING_ENABLED', false),
];