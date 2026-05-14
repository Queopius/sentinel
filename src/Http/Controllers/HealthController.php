<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Queopius\Sentinel\Support\SecurityAuditService;

class HealthController extends Controller
{
    public function __invoke(Request $request, SecurityAuditService $auditService): JsonResponse
    {
        $audit = $auditService->audit($request, (array) config('sentinel', []));
        $summary = $audit['summary'];
        $status = ($summary['warnings_count'] ?? 0) > 0 ? 'warn' : 'ok';

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'https_detected' => (bool) ($summary['https_detected'] ?? false),
            'hsts' => (bool) ($summary['hsts_applied'] ?? false),
            'csp' => (string) ($summary['csp_mode'] ?? 'off'),
            'warnings_count' => (int) ($summary['warnings_count'] ?? 0),
        ]);
    }
}
