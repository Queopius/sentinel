<?php

declare(strict_types=1);

return [
    'enabled' => env('SHIELD_ENABLED', true),
    'environments' => ['production', 'staging', 'local', 'testing'],
    'strict_validation' => env('SHIELD_STRICT_VALIDATION', false),

    'exclude' => [
        'paths' => [
            'telescope*',
            '_debugbar*',
        ],
        'route_names' => [],
    ],

    'preset' => env('SHIELD_PRESET', 'web_compatible'),

    'https' => [
        'redirect' => env('SHIELD_HTTPS_REDIRECT', false),
        'redirect_status' => 308,
        'force_scheme' => env('SHIELD_HTTPS_FORCE_SCHEME', false),
        'exclude_paths' => ['up', 'health*'],
        'exclude_route_names' => [],
        'only_in_environments' => ['production', 'staging'],
        'trust_proxy_warning_enabled' => true,
    ],

    'headers' => [
        'hsts' => [
            'enabled' => true,
            'max_age' => 31536000,
            'include_subdomains' => true,
            'preload' => false,
        ],
        'csp' => [
            'enabled' => true,
            'report_only' => true,
            'report_uri' => env('SHIELD_CSP_REPORT_URI'),
            'report_to' => null,
            'nonce' => [
                'enabled' => false,
                'script_src' => false,
                'style_src' => false,
            ],
            'directives' => [
                'default-src' => ["'self'"],
                'script-src' => ["'self'"],
                'style-src' => ["'self'", "'unsafe-inline'"],
                'img-src' => ["'self'", 'data:', 'https:'],
                'font-src' => ["'self'", 'data:'],
                'connect-src' => ["'self'"],
                'frame-ancestors' => ["'self'"],
                'base-uri' => ["'self'"],
                'form-action' => ["'self'"],
                'object-src' => ["'none'"],
                'upgrade-insecure-requests' => true,
            ],
        ],
        'x_content_type_options' => [
            'enabled' => true,
            'value' => 'nosniff',
        ],
        'referrer_policy' => [
            'enabled' => true,
            'value' => 'strict-origin-when-cross-origin',
        ],
        'x_frame_options' => [
            'enabled' => true,
            'value' => 'SAMEORIGIN',
        ],
        'permissions_policy' => [
            'enabled' => true,
            'value' => 'camera=(), microphone=(), geolocation=()',
        ],
        'cross_origin' => [
            'opener_policy' => 'same-origin',
            'embedder_policy' => null,
            'resource_policy' => 'same-origin',
        ],
        'custom' => [],
    ],

    'ui' => [
        'enabled' => env('SHIELD_UI_ENABLED', false),
        'path' => env('SHIELD_UI_PATH', 'shield'),
        'middleware' => ['web', 'auth'],
        'require_ability' => null,
        'logo_url' => 'https://raw.githubusercontent.com/queopius/shield/main/.github/assets/logo-queopius-shield.png',
        'theme' => env('SHIELD_UI_THEME', 'light'), // light|dark|auto
        'show_csp_reports' => true,
        'endpoint_scan' => [
            'enabled' => true,
            'paths' => ['/', '/login', '/api'],
            'max_paths' => 8,
        ],
    ],

    'csp_reports' => [
        'enabled' => env('SHIELD_CSP_REPORTS_ENABLED', false),
        'route_path' => env('SHIELD_CSP_REPORTS_PATH', 'shield/csp-reports'),
        'store_database' => true,
        'prune_days' => 30,
        'middleware' => ['api'],
        'log_invalid_payloads' => true,
    ],

    'audit' => [
        'enabled' => true,
        'perform_live_probe' => false,
        'internal_probe_path' => '/',
        'warnings' => [
            'allow_unsafe_inline_warning' => true,
            'require_frame_ancestors_warning' => true,
        ],
    ],

    'health_endpoint' => [
        'enabled' => false,
        'path' => 'up/shield',
        'middleware' => ['api'],
    ],

    'views' => [
        'publishable' => true,
        'namespace' => 'shield',
    ],

    'presets' => [
        'web_compatible' => [
            'headers' => [
                'csp' => [
                    'enabled' => true,
                    'report_only' => true,
                    'directives' => [
                        'default-src' => ["'self'"],
                        'script-src' => ["'self'", "'unsafe-inline'"],
                        'style-src' => ["'self'", "'unsafe-inline'"],
                        'img-src' => ["'self'", 'data:', 'https:'],
                        'font-src' => ["'self'", 'data:'],
                        'connect-src' => ["'self'"],
                        'object-src' => ["'none'"],
                    ],
                ],
            ],
        ],
        'api_strict' => [
            'headers' => [
                'csp' => [
                    'enabled' => true,
                    'report_only' => false,
                    'directives' => [
                        'default-src' => ["'none'"],
                        'frame-ancestors' => ["'none'"],
                        'base-uri' => ["'none'"],
                        'object-src' => ["'none'"],
                    ],
                ],
                'x_frame_options' => [
                    'enabled' => true,
                    'value' => 'DENY',
                ],
            ],
        ],
        'admin_panel' => [],
        'web_strict' => [],
        'report_only_bootstrap' => [],
    ],
];
