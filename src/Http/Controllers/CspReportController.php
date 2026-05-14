<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Queopius\Sentinel\Events\CspReportStored;
use Queopius\Sentinel\Models\CspReport;

class CspReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $payload = $request->json()->all();
        if (! is_array($payload) || $payload === []) {
            $this->logInvalid('Empty/invalid CSP report payload');

            return response()->json(['ok' => true], 204);
        }

        $report = (array) ($payload['csp-report'] ?? $payload['body'] ?? $payload);

        if ((bool) config('sentinel.csp_reports.store_database', true)) {
            try {
                $row = CspReport::query()->create([
                    'payload' => $payload,
                    'document_uri' => $this->stringOrNull($report['document-uri'] ?? $report['document_uri'] ?? null),
                    'blocked_uri' => $this->stringOrNull($report['blocked-uri'] ?? $report['blocked_uri'] ?? null),
                    'violated_directive' => $this->stringOrNull($report['violated-directive'] ?? $report['violated_directive'] ?? null),
                    'effective_directive' => $this->stringOrNull($report['effective-directive'] ?? $report['effective_directive'] ?? null),
                    'original_policy' => $this->stringOrNull($report['original-policy'] ?? $report['original_policy'] ?? null),
                    'user_agent' => $request->userAgent(),
                    'received_at' => now(),
                ]);

                event(new CspReportStored($row));
            } catch (\Throwable $e) {
                $this->logInvalid('CSP report persistence failed: '.$e->getMessage());
            }
        }

        return response()->json(['ok' => true], 204);
    }

    private function logInvalid(string $message): void
    {
        if ((bool) config('sentinel.csp_reports.log_invalid_payloads', true)) {
            Log::warning('[Queopius Sentinel] '.$message);
        }
    }

    private function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }
}
