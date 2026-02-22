<?php

declare(strict_types=1);

namespace Queopius\Shield\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Queopius\Shield\Http\Middleware\AddSecurityHeaders;
use Queopius\Shield\Tests\TestCase;

class ConfigExclusionsTest extends TestCase
{
    public function test_excludes_by_path(): void
    {
        config()->set('shield.exclude.paths', ['probe']);

        $this->get('/probe')->assertHeaderMissing('X-Content-Type-Options');
    }

    public function test_excludes_by_route_name(): void
    {
        Route::middleware([AddSecurityHeaders::class])->get('/named', fn () => response('ok'))->name('named.test');
        config()->set('shield.exclude.route_names', ['named.test']);

        $this->get('/named')->assertHeaderMissing('X-Content-Type-Options');
    }
}
