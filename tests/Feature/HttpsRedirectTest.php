<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Tests\Feature;

use Queopius\Sentinel\Tests\TestCase;

class HttpsRedirectTest extends TestCase
{
    public function test_redirects_to_https_when_enabled(): void
    {
        config()->set('sentinel.https.redirect', true);
        config()->set('sentinel.https.only_in_environments', ['testing']);

        $this->get('http://localhost/force-https')->assertRedirect('https://localhost/force-https');
    }

    public function test_respects_exclusions(): void
    {
        config()->set('sentinel.https.redirect', true);
        config()->set('sentinel.https.only_in_environments', ['testing']);
        config()->set('sentinel.https.exclude_paths', ['force-https']);

        $this->get('http://localhost/force-https')->assertOk();
    }

    public function test_does_not_redirect_if_environment_not_allowed(): void
    {
        config()->set('sentinel.https.redirect', true);
        config()->set('sentinel.https.only_in_environments', ['production']);

        $this->get('http://localhost/force-https')->assertOk();
    }
}
