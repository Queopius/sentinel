<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Support;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

class EndpointScanner
{
    public function __construct(private readonly HeaderManager $headerManager) {}

    /**
     * @param  array<int, string>  $paths
     * @return array<int, array{
     *   path:string,
     *   status:int,
     *   missing_headers:array<int,string>,
     *   mismatched_headers:array<int,array{header:string,expected:string,actual:string}>,
     *   expected_headers:array<string,string>,
     *   actual_headers:array<string,string>,
     *   ok:bool
     * }>
     */
    public function scan(array $paths, array $config): array
    {
        $kernel = app(Kernel::class);
        $results = [];

        foreach ($paths as $path) {
            $uri = '/'.ltrim($path, '/');
            $request = Request::create($uri, 'GET');
            $response = $kernel->handle($request);

            $expected = $this->headerManager->expectedHeaders($request, $config);
            $missing = [];
            $mismatched = [];
            $actual = [];
            foreach ($response->headers->all() as $name => $values) {
                $actual[$name] = implode(', ', $values);
            }

            foreach (array_keys($expected) as $header) {
                if (! $response->headers->has($header)) {
                    $missing[] = $header;

                    continue;
                }

                $actualValue = (string) $response->headers->get($header, '');
                $expectedValue = (string) ($expected[$header] ?? '');
                if ($actualValue !== $expectedValue) {
                    $mismatched[] = [
                        'header' => $header,
                        'expected' => $expectedValue,
                        'actual' => $actualValue,
                    ];
                }
            }

            $results[] = [
                'path' => $uri,
                'status' => $response->getStatusCode(),
                'missing_headers' => $missing,
                'mismatched_headers' => $mismatched,
                'expected_headers' => $expected,
                'actual_headers' => $actual,
                'ok' => $missing === [] && $mismatched === [],
            ];

            $kernel->terminate($request, $response);
        }

        return $results;
    }
}
