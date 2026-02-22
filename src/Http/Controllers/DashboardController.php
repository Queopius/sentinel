<?php

declare(strict_types=1);

namespace Queopius\Shield\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Queopius\Shield\Models\CspReport;
use Queopius\Shield\ViewModels\ShieldDashboardViewModel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function __invoke(Request $request, ShieldDashboardViewModel $viewModel): View|Response|JsonResponse|StreamedResponse
    {
        $ability = config('shield.ui.require_ability');
        if (is_string($ability) && $ability !== '' && Gate::denies($ability)) {
            abort(403);
        }

        $data = $viewModel->data($request);
        if ((string) $request->query('export') === 'endpoints') {
            return $this->exportEndpoints($data, (string) $request->query('format', 'json'));
        }

        $reports = [];
        if ((bool) ($data['showCspReports'] ?? false) && $this->canQueryReports()) {
            $reports = CspReport::query()->latest('received_at')->limit(20)->get();
        }

        return view('shield::dashboard', [
            ...$data,
            'recentReports' => $reports,
        ]);
    }

    private function canQueryReports(): bool
    {
        try {
            CspReport::query()->limit(1)->get();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @param  array<string,mixed>  $data
     */
    private function exportEndpoints(array $data, string $format): Response|JsonResponse|StreamedResponse
    {
        $endpointScan = (array) ($data['endpointScan'] ?? []);
        $rows = (array) ($endpointScan['rows'] ?? []);
        $summary = (array) ($endpointScan['summary'] ?? []);
        $timestamp = now()->toIso8601String();

        if ($format === 'csv') {
            $filename = 'shield-endpoint-scan-'.now()->format('Ymd-His').'.csv';

            return response()->streamDownload(static function () use ($rows): void {
                $handle = fopen('php://output', 'wb');
                if ($handle === false) {
                    return;
                }

                fputcsv($handle, ['path', 'status', 'ok', 'score', 'severity', 'missing_count', 'mismatched_count', 'missing_headers', 'mismatched_headers']);
                foreach ($rows as $row) {
                    $missing = (array) ($row['missing_headers'] ?? []);
                    $mismatched = (array) ($row['mismatched_headers'] ?? []);
                    $mismatchedLabels = array_map(
                        static fn (array $item): string => (string) ($item['header'] ?? 'unknown'),
                        $mismatched
                    );

                    fputcsv($handle, [
                        (string) ($row['path'] ?? ''),
                        (string) ($row['status'] ?? ''),
                        (bool) ($row['ok'] ?? false) ? 'yes' : 'no',
                        (string) ((int) ($row['score'] ?? 100)),
                        (string) ($row['severity'] ?? 'low'),
                        (string) count($missing),
                        (string) count($mismatched),
                        implode('|', $missing),
                        implode('|', $mismatchedLabels),
                    ]);
                }

                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        return response()->json([
            'generated_at' => $timestamp,
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }
}
