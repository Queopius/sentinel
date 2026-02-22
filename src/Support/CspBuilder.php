<?php

declare(strict_types=1);

namespace Queopius\Shield\Support;

use Queopius\Shield\Exceptions\InvalidShieldConfigException;

class CspBuilder
{
    /** @var array<string, true> */
    private array $knownDirectives = [
        'default-src' => true,
        'script-src' => true,
        'style-src' => true,
        'img-src' => true,
        'font-src' => true,
        'connect-src' => true,
        'frame-src' => true,
        'frame-ancestors' => true,
        'base-uri' => true,
        'form-action' => true,
        'object-src' => true,
        'media-src' => true,
        'worker-src' => true,
        'report-uri' => true,
        'report-to' => true,
        'upgrade-insecure-requests' => true,
        'block-all-mixed-content' => true,
    ];

    public function build(array $config, ?string $nonce = null, bool $strictValidation = false): string
    {
        $directives = (array) ($config['directives'] ?? []);
        if ($directives === []) {
            return '';
        }

        if (! empty($config['report_uri'])) {
            $directives['report-uri'] = [(string) $config['report_uri']];
        }

        if (! empty($config['report_to'])) {
            $directives['report-to'] = [(string) $config['report_to']];
        }

        if ($nonce !== null && $nonce !== '') {
            if ((bool) data_get($config, 'nonce.script_src', false)) {
                $directives['script-src'] = $this->appendNonce((array) ($directives['script-src'] ?? []), $nonce);
            }
            if ((bool) data_get($config, 'nonce.style_src', false)) {
                $directives['style-src'] = $this->appendNonce((array) ($directives['style-src'] ?? []), $nonce);
            }
        }

        $parts = [];
        foreach ($directives as $directive => $value) {
            if (! is_string($directive) || trim($directive) === '') {
                continue;
            }
            $directive = strtolower(trim($directive));

            if (! isset($this->knownDirectives[$directive])) {
                if ($strictValidation) {
                    throw new InvalidShieldConfigException("Unknown CSP directive [{$directive}]");
                }

                continue;
            }

            if (is_bool($value)) {
                if ($value) {
                    $parts[] = $directive;
                }

                continue;
            }

            if (! is_array($value)) {
                if ($strictValidation) {
                    throw new InvalidShieldConfigException("CSP directive [{$directive}] expects array|bool");
                }

                continue;
            }

            $clean = array_values(array_filter(array_map(static fn ($item): string => trim((string) $item), $value), static fn (string $v): bool => $v !== ''));
            if ($clean === []) {
                continue;
            }

            $parts[] = $directive.' '.implode(' ', $clean);
        }

        return implode('; ', $parts);
    }

    /**
     * @param  array<int, string>  $existing
     * @return array<int, string>
     */
    private function appendNonce(array $existing, string $nonce): array
    {
        $token = "'nonce-{$nonce}'";
        if (! in_array($token, $existing, true)) {
            $existing[] = $token;
        }

        return $existing;
    }
}
