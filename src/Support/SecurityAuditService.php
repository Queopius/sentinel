<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Support;

use Illuminate\Http\Request;

class SecurityAuditService
{
    public function __construct(
        private readonly HeaderManager $headerManager,
        private readonly ProxyDetector $proxyDetector,
    ) {}

    /**
     * @param  array<string,mixed>  $config
     * @return array{checks: array<int, array{key:string,status:string,message:string}>, warnings: array<int,string>, summary: array<string,mixed>, expected_headers: array<string,string>}
     */
    public function audit(Request $request, array $config): array
    {
        $warnings = [];
        $checks = [];
        $headers = (array) ($config['headers'] ?? []);
        $expectedHeaders = $this->headerManager->expectedHeaders($request, $config);
        $proxy = $this->proxyDetector->detect($request);

        $httpsRedirectEnabled = (bool) data_get($config, 'https.redirect', false);
        $checks[] = $this->check('https_detected', $request->isSecure(), 'HTTPS detected', 'Request is not HTTPS');
        $checks[] = $this->check('https_redirect', $httpsRedirectEnabled, 'HTTPS redirect enabled', 'HTTPS redirect disabled');

        $hstsEnabled = (bool) data_get($headers, 'hsts.enabled', false);
        $hstsApplied = array_key_exists('Strict-Transport-Security', $expectedHeaders);
        $checks[] = $this->check('hsts', $hstsApplied, 'HSTS active', 'HSTS not active on this request');
        if ($hstsEnabled && ! $request->isSecure()) {
            $warnings[] = 'HSTS configured but request is not HTTPS, so header was not applied.';
        }

        $cspEnabled = (bool) data_get($headers, 'csp.enabled', false);
        $cspReportOnly = (bool) data_get($headers, 'csp.report_only', false);
        $checks[] = $this->check('csp', $cspEnabled, $cspReportOnly ? 'CSP report-only enabled' : 'CSP enforce enabled', 'CSP disabled');

        if ($cspEnabled && (bool) data_get($config, 'audit.warnings.allow_unsafe_inline_warning', true)) {
            $scriptSrc = (array) data_get($headers, 'csp.directives.script-src', []);
            if (in_array("'unsafe-inline'", $scriptSrc, true)) {
                $warnings[] = "CSP script-src contains 'unsafe-inline'.";
            }
        }

        $xfoEnabled = (bool) data_get($headers, 'x_frame_options.enabled', false);
        $frameAncestors = (array) data_get($headers, 'csp.directives.frame-ancestors', []);
        if (! $xfoEnabled && $frameAncestors === []) {
            $warnings[] = 'X-Frame-Options missing and CSP frame-ancestors not configured.';
        }

        if ((bool) data_get($headers, 'hsts.preload', false) && ! (bool) data_get($headers, 'hsts.include_subdomains', false)) {
            $warnings[] = 'HSTS preload active without includeSubDomains.';
        }

        if (! (bool) data_get($headers, 'permissions_policy.enabled', false)) {
            $warnings[] = 'Permissions-Policy is disabled.';
        }

        if ((bool) $proxy['maybe_misconfigured_proxy'] && (bool) data_get($config, 'https.trust_proxy_warning_enabled', true)) {
            $warnings[] = 'Proxy headers detected but request is not secure. Review trusted proxy configuration.';
        }

        if ($cspReportOnly && app()->environment('production')) {
            $warnings[] = 'CSP is still in report-only mode in production.';
        }

        $cookies = $request->cookies->all();
        if ($cookies !== []) {
            $warnings[] = 'Cookie audit in passive mode: validate Secure/HttpOnly/SameSite in session and app cookies.';
        }

        $summary = [
            'https_detected' => $request->isSecure(),
            'https_redirect_enabled' => $httpsRedirectEnabled,
            'hsts_applied' => $hstsApplied,
            'csp_mode' => $cspEnabled ? ($cspReportOnly ? 'report-only' : 'enforce') : 'off',
            'warnings_count' => count($warnings),
            'proxy' => $proxy,
        ];

        return [
            'checks' => $checks,
            'warnings' => $warnings,
            'summary' => $summary,
            'expected_headers' => $expectedHeaders,
        ];
    }

    /**
     * @return array{key:string,status:string,message:string}
     */
    private function check(string $key, bool $ok, string $okMessage, string $failMessage): array
    {
        return [
            'key' => $key,
            'status' => $ok ? 'ok' : 'fail',
            'message' => $ok ? $okMessage : $failMessage,
        ];
    }
}
