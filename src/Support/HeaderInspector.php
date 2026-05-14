<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Support;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HeaderInspector
{
    public function __construct(private readonly HeaderManager $headerManager) {}

    /**
     * @return array{expected: array<string,string>, actual: array<string,string>, missing: array<int,string>, mismatched: array<int, array{header:string, expected:string, actual:string}>}
     */
    public function inspect(Request $request, Response $response, array $config): array
    {
        $expected = $this->headerManager->expectedHeaders($request, $config);

        $actual = [];
        foreach ($response->headers->all() as $k => $vals) {
            $actual[$k] = implode(', ', $vals);
        }

        $missing = [];
        $mismatched = [];
        foreach ($expected as $key => $value) {
            $actualValue = $response->headers->get($key);
            if ($actualValue === null) {
                $missing[] = $key;

                continue;
            }
            if ($actualValue !== $value) {
                $mismatched[] = ['header' => $key, 'expected' => $value, 'actual' => $actualValue];
            }
        }

        return compact('expected', 'actual', 'missing', 'mismatched');
    }
}
