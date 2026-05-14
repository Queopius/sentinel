<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Queopius\Sentinel\Support\SentinelPresetResolver;
use Symfony\Component\HttpFoundation\Response;

class EnforceHttps
{
    public function __construct(private readonly SentinelPresetResolver $presetResolver) {}

    public function handle(Request $request, Closure $next): Response
    {
        $config = $this->presetResolver->resolvedConfig((array) config('sentinel', []));

        if ((bool) data_get($config, 'https.force_scheme', false)) {
            URL::forceScheme('https');
        }

        if ($this->shouldRedirect($request, $config)) {
            $status = (int) data_get($config, 'https.redirect_status', 308);
            $secureUrl = 'https://'.$request->getHttpHost().$request->getRequestUri();

            return new RedirectResponse($secureUrl, in_array($status, [301, 308], true) ? $status : 308);
        }

        return $next($request);
    }

    private function shouldRedirect(Request $request, array $config): bool
    {
        if (! (bool) data_get($config, 'enabled', true)) {
            return false;
        }

        if (! (bool) data_get($config, 'https.redirect', false)) {
            return false;
        }

        $envs = (array) data_get($config, 'https.only_in_environments', []);
        if ($envs !== [] && ! app()->environment($envs)) {
            return false;
        }

        foreach ((array) data_get($config, 'https.exclude_paths', []) as $pattern) {
            if ($request->is((string) $pattern)) {
                return false;
            }
        }

        $name = $request->route()?->getName();
        if (is_string($name) && in_array($name, (array) Arr::get($config, 'https.exclude_route_names', []), true)) {
            return false;
        }

        return ! $request->isSecure();
    }
}
