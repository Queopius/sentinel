<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Support;

use Illuminate\Http\Request;

class NonceManager
{
    private const KEY = '_sentinel_nonce';

    public function forRequest(Request $request): string
    {
        $nonce = $request->attributes->get(self::KEY);
        if (is_string($nonce) && $nonce !== '') {
            return $nonce;
        }

        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set(self::KEY, $nonce);

        return $nonce;
    }
}
