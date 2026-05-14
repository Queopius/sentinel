<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Queopius\Sentinel\Http\Middleware\AddSecurityHeaders;
use Queopius\Sentinel\Tests\TestCase;

class ConfigExclusionsTest extends TestCase
{
    public function test_excludes_by_path(): void
    {
        config()->set('sentinel.exclude.paths', ['probe']);

        $this->get('/probe')->assertHeaderMissing('X-Content-Type-Options');
    }

    public function test_excludes_by_route_name(): void
    {
        Route::middleware([AddSecurityHeaders::class])->get('/named', fn () => response('ok'))->name('named.test');
        config()->set('sentinel.exclude.route_names', ['named.test']);

        $this->get('/named')->assertHeaderMissing('X-Content-Type-Options');
    }
}
