<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Support;

use Illuminate\Http\Request;

class ProxyDetector
{
    /**
     * @return array{proxy_header_present: bool, request_secure: bool, maybe_misconfigured_proxy: bool}
     */
    public function detect(Request $request): array
    {
        $forwardedProto = $request->headers->get('x-forwarded-proto');
        $headerPresent = is_string($forwardedProto) && $forwardedProto !== '';
        $requestSecure = $request->isSecure();

        return [
            'proxy_header_present' => $headerPresent,
            'request_secure' => $requestSecure,
            'maybe_misconfigured_proxy' => $headerPresent && ! $requestSecure,
        ];
    }
}
