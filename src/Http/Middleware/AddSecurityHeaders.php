<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Queopius\Sentinel\Support\HeaderManager;
use Queopius\Sentinel\Support\SentinelPresetResolver;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    public function __construct(
        private readonly HeaderManager $headerManager,
        private readonly SentinelPresetResolver $presetResolver,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $config = $this->presetResolver->resolvedConfig((array) config('sentinel', []));
        if (! $this->shouldApply($request, $config)) {
            return $response;
        }

        try {
            $headers = $this->headerManager->expectedHeaders($request, $config);
            foreach ($headers as $name => $value) {
                if (! $response->headers->has($name)) {
                    $response->headers->set($name, $value);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Queopius Sentinel failed to append security headers: '.$e->getMessage());
        }

        return $response;
    }

    /** @param array<string,mixed> $config */
    private function shouldApply(Request $request, array $config): bool
    {
        if (! (bool) ($config['enabled'] ?? true)) {
            return false;
        }

        $envs = (array) ($config['environments'] ?? []);
        if ($envs !== [] && ! app()->environment($envs)) {
            return false;
        }

        foreach ((array) Arr::get($config, 'exclude.paths', []) as $pattern) {
            if ($request->is((string) $pattern)) {
                return false;
            }
        }

        $name = $request->route()->getName();
        if (is_string($name) && in_array($name, (array) Arr::get($config, 'exclude.route_names', []), true)) {
            return false;
        }

        return true;
    }
}
