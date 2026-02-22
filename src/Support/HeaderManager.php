<?php

declare(strict_types=1);

namespace Queopius\Shield\Support;

use Illuminate\Http\Request;

class HeaderManager
{
    public function __construct(
        private readonly CspBuilder $cspBuilder,
        private readonly HstsBuilder $hstsBuilder,
        private readonly NonceManager $nonceManager,
    ) {}

    /**
     * @return array<string, string>
     */
    public function expectedHeaders(Request $request, array $config): array
    {
        $headers = [];
        $strictValidation = (bool) ($config['strict_validation'] ?? false);
        $h = (array) ($config['headers'] ?? []);

        if ((bool) data_get($h, 'x_content_type_options.enabled', false)) {
            $headers['X-Content-Type-Options'] = (string) data_get($h, 'x_content_type_options.value', 'nosniff');
        }

        if ((bool) data_get($h, 'referrer_policy.enabled', false)) {
            $headers['Referrer-Policy'] = (string) data_get($h, 'referrer_policy.value', 'strict-origin-when-cross-origin');
        }

        if ((bool) data_get($h, 'x_frame_options.enabled', false)) {
            $headers['X-Frame-Options'] = (string) data_get($h, 'x_frame_options.value', 'SAMEORIGIN');
        }

        if ((bool) data_get($h, 'permissions_policy.enabled', false)) {
            $value = data_get($h, 'permissions_policy.value');
            if (is_array($value)) {
                $pairs = [];
                foreach ($value as $k => $v) {
                    $pairs[] = $k.'='.$v;
                }
                $value = implode(', ', $pairs);
            }
            $headers['Permissions-Policy'] = (string) $value;
        }

        $coop = data_get($h, 'cross_origin.opener_policy');
        if (is_string($coop) && $coop !== '') {
            $headers['Cross-Origin-Opener-Policy'] = $coop;
        }

        $coep = data_get($h, 'cross_origin.embedder_policy');
        if (is_string($coep) && $coep !== '') {
            $headers['Cross-Origin-Embedder-Policy'] = $coep;
        }

        $corp = data_get($h, 'cross_origin.resource_policy');
        if (is_string($corp) && $corp !== '') {
            $headers['Cross-Origin-Resource-Policy'] = $corp;
        }

        if ((bool) data_get($h, 'hsts.enabled', false) && $request->isSecure()) {
            $headers['Strict-Transport-Security'] = $this->hstsBuilder->build((array) data_get($h, 'hsts', []));
        }

        if ((bool) data_get($h, 'csp.enabled', false)) {
            $cspConfig = (array) data_get($h, 'csp', []);
            $nonce = null;
            if ((bool) data_get($cspConfig, 'nonce.enabled', false)) {
                $nonce = $this->nonceManager->forRequest($request);
            }

            $value = $this->cspBuilder->build($cspConfig, $nonce, $strictValidation);
            if ($value !== '') {
                $headers[(bool) data_get($h, 'csp.report_only', false) ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy'] = $value;
            }
        }

        foreach ((array) data_get($h, 'custom', []) as $key => $value) {
            if (is_string($key) && $key !== '' && is_string($value) && $value !== '') {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }
}
