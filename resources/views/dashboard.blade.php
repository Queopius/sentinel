@extends('sentinel::layouts.app')

@section('title', 'Queopius Sentinel Dashboard')

@section('content')
  @php
    $currentSection = (string) ($selectedSection ?? 'overview');
    $navSections = (array) ($sections ?? [
      'overview' => 'Overview',
      'headers' => 'Headers',
      'endpoints' => 'Endpoints',
      'reports' => 'Reports',
      'config' => 'Config',
    ]);

    $isDark = (string) ($uiTheme ?? 'light') === 'dark';
    $cardClass = $isDark ? 'bg-slate-900 border-slate-800' : 'bg-white border-stone-200';
    $subtleClass = $isDark ? 'text-slate-400' : 'text-stone-500';
    $mutedClass = $isDark ? 'text-slate-300' : 'text-stone-700';
    $asideClass = $isDark ? 'bg-slate-900 border-slate-800' : 'bg-white border-stone-200';
    $surfaceClass = $isDark ? 'bg-slate-800 border-slate-700' : 'bg-stone-50 border-stone-200';
    $sentinelLogoUrl = (string) config('sentinel.ui.logo_url', '');
    $baseSentinelPath = \Illuminate\Support\Facades\Route::has('sentinel.dashboard')
      ? route('sentinel.dashboard', [], false)
      : '/sentinel';
    $baseQuery = request()->query();
    $sentinelQuery = static function (array $overrides = [], array $remove = []) use ($baseQuery): string {
      $query = array_merge($baseQuery, $overrides);
      foreach ($remove as $key) {
        unset($query[$key]);
      }

      return http_build_query($query);
    };
  @endphp

  <div class="max-w-7xl mx-auto p-6 lg:flex lg:items-start gap-6">
    <aside class="lg:w-64 w-full mb-4 lg:mb-0">
      <div class="rounded-xl border {{ $asideClass }} p-4 lg:sticky lg:top-4">
        @if($sentinelLogoUrl !== '')
          <img
            src="{{ $sentinelLogoUrl }}"
            alt="Queopius Sentinel"
            class="mx-auto mb-3 h-auto max-h-16 w-auto max-w-full"
            loading="lazy"
          >
        @endif
        <h1 class="text-xl font-bold">{{ __('sentinel.title') }}</h1>
        <p class="text-xs {{ $subtleClass }} mb-4">{{ __('sentinel.subtitle') }}</p>

        <nav class="space-y-1">
          @foreach($navSections as $sectionKey => $sectionLabel)
            <a href="{{ $baseSentinelPath }}?{{ $sentinelQuery(['section' => $sectionKey, 'theme' => (string) ($uiTheme ?? 'light')], ['export', 'format', 'scan_paths']) }}"
               class="block rounded-lg px-3 py-2 text-sm {{ $currentSection === $sectionKey ? 'bg-stone-900 text-white' : ($isDark ? 'text-slate-200 hover:bg-slate-800' : 'text-stone-700 hover:bg-stone-100') }}">
              {{ __('sentinel.sections.'.$sectionKey) }}
            </a>
          @endforeach
        </nav>

        <div class="mt-4">
          @php
            $languageLabels = [
              'es' => 'ES',
              'en' => 'EN',
              'fr' => 'FR',
              'pt' => 'PT-BR',
            ];
          @endphp
          <p class="mb-1 text-[11px] {{ $subtleClass }}">{{ __('sentinel.language') }}</p>
          <label for="sentinel-locale" class="sr-only">Language</label>
          <select
            id="sentinel-locale"
            class="w-full rounded border px-2 py-1 text-xs {{ $isDark ? 'border-slate-600 bg-slate-900 text-slate-100' : 'border-stone-300 bg-white text-stone-700' }}"
            onchange="window.location=this.value"
          >
            @foreach(config('app.supported_locales', ['es', 'en', 'fr', 'pt']) as $locale)
              <option
                value="{{ \Illuminate\Support\Facades\Route::has('locale.switch') ? route('locale.switch', $locale, false) : $baseSentinelPath.'?'.$sentinelQuery(['locale' => $locale], []) }}"
                @selected(app()->getLocale() === $locale)
                class="{{ $isDark ? 'bg-slate-900 text-slate-100' : 'text-stone-900' }}"
              >
                {{ $languageLabels[$locale] ?? strtoupper($locale) }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="mt-4">
          <p class="mb-1 text-[11px] {{ $subtleClass }}">{{ __('sentinel.theme') }}</p>
          <div class="flex items-center gap-2 text-xs">
            <a href="{{ $baseSentinelPath }}?{{ $sentinelQuery(['section' => $currentSection, 'theme' => 'light'], ['export', 'format', 'scan_paths']) }}" class="rounded border px-2 py-1 {{ (string) ($uiTheme ?? 'light') === 'light' ? 'bg-stone-900 text-white border-stone-900' : ($isDark ? 'border-slate-600 text-slate-200' : 'border-stone-300 text-stone-700') }}">{{ __('sentinel.themes.light') }}</a>
            <a href="{{ $baseSentinelPath }}?{{ $sentinelQuery(['section' => $currentSection, 'theme' => 'dark'], ['export', 'format', 'scan_paths']) }}" class="rounded border px-2 py-1 {{ (string) ($uiTheme ?? 'light') === 'dark' ? 'bg-stone-900 text-white border-stone-900' : ($isDark ? 'border-slate-600 text-slate-200' : 'border-stone-300 text-stone-700') }}">{{ __('sentinel.themes.dark') }}</a>
          </div>
        </div>

        <p class="mt-4 text-[11px] {{ $subtleClass }}">{{ now()->toDateTimeString() }}</p>
      </div>
    </aside>

    <main class="flex-1 space-y-5">
      @if($currentSection === 'overview')
        <div class="grid md:grid-cols-5 gap-3">
          <div class="rounded-xl border {{ $cardClass }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.cards.https_detected') }}</p><p class="text-2xl font-bold">{{ $summary['https_detected'] ? 'YES' : 'NO' }}</p></div>
          <div class="rounded-xl border {{ $cardClass }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.cards.redirect_https') }}</p><p class="text-2xl font-bold">{{ $summary['https_redirect_enabled'] ? 'ON' : 'OFF' }}</p></div>
          <div class="rounded-xl border {{ $cardClass }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.cards.hsts') }}</p><p class="text-2xl font-bold">{{ $summary['hsts_applied'] ? 'ON' : 'OFF' }}</p></div>
          <div class="rounded-xl border {{ $cardClass }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.cards.csp_mode') }}</p><p class="text-2xl font-bold uppercase">{{ $summary['csp_mode'] }}</p></div>
          <div class="rounded-xl border {{ $cardClass }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.cards.warnings') }}</p><p class="text-2xl font-bold">{{ $summary['warnings_count'] }}</p></div>
        </div>

        <div class="grid lg:grid-cols-2 gap-4">
          @include('sentinel::partials.checks', ['checks' => $checks])
          @include('sentinel::partials.warnings', ['warnings' => $warnings])
        </div>

        <div class="grid lg:grid-cols-3 gap-4">
          <div class="rounded-xl border {{ $cardClass }} p-4">
            <h3 class="text-lg font-semibold mb-3">{{ __('sentinel.charts.checks_distribution') }}</h3>
            @php
              $checksTotal = max(1, (int) (($checkMetrics['ok'] ?? 0) + ($checkMetrics['warning'] ?? 0) + ($checkMetrics['fail'] ?? 0)));
              $checkRows = [
                ['label' => __('sentinel.charts.ok'), 'value' => (int) ($checkMetrics['ok'] ?? 0), 'color' => 'bg-emerald-500'],
                ['label' => __('sentinel.charts.warning'), 'value' => (int) ($checkMetrics['warning'] ?? 0), 'color' => 'bg-amber-500'],
                ['label' => __('sentinel.charts.fail'), 'value' => (int) ($checkMetrics['fail'] ?? 0), 'color' => 'bg-rose-500'],
              ];
            @endphp
            <div class="space-y-3">
              @foreach($checkRows as $row)
                @php $pct = (int) round(($row['value'] / $checksTotal) * 100); @endphp
                <div>
                  <div class="mb-1 flex items-center justify-between text-xs {{ $subtleClass }}">
                    <span>{{ $row['label'] }}</span>
                    <span>{{ $row['value'] }} ({{ $pct }}%)</span>
                  </div>
                  <div class="h-2 rounded-full {{ $isDark ? 'bg-slate-700' : 'bg-stone-200' }} overflow-hidden">
                    <div class="h-full {{ $row['color'] }}" style="width: {{ $pct }}%"></div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="rounded-xl border {{ $cardClass }} p-4 lg:col-span-2">
            <div class="mb-3 flex items-center justify-between gap-3">
              <h3 class="text-lg font-semibold">{{ __('sentinel.charts.csp_timeline', ['days' => $selectedTimelineDays]) }}</h3>
              <div class="flex items-center gap-2">
                @foreach($timelineOptions as $daysOption)
                  <a href="{{ $baseSentinelPath }}?{{ $sentinelQuery(['section' => 'overview', 'theme' => (string) ($uiTheme ?? 'light'), 'days' => $daysOption], ['export', 'format', 'scan_paths']) }}"
                     class="rounded-full border px-3 py-1 text-xs {{ (int) $selectedTimelineDays === (int) $daysOption ? 'bg-stone-900 text-white border-stone-900' : ($isDark ? 'border-slate-600 text-slate-200' : 'bg-white text-stone-700 border-stone-300') }}">
                    {{ $daysOption }}d
                  </a>
                @endforeach
              </div>
            </div>
            @php
              $timelineLabels = (array) ($cspTimelineMetrics['labels'] ?? []);
              $timelineValues = (array) ($cspTimelineMetrics['values'] ?? []);
              $timelineMax = 1;
              foreach ($timelineValues as $rawValue) {
                  $timelineMax = max($timelineMax, (int) $rawValue);
              }
              $timelineCount = count($timelineValues);
              $timelineStep = $timelineCount > 1 ? (100 / ($timelineCount - 1)) : 100;
              $timelinePoints = [];
              foreach ($timelineValues as $idx => $rawValue) {
                  $value = (int) $rawValue;
                  $x = $timelineCount > 1 ? ($idx * $timelineStep) : 50;
                  $y = 100 - (($value / $timelineMax) * 100);
                  $timelinePoints[] = number_format($x, 3, '.', '').','.number_format($y, 3, '.', '');
              }
            @endphp
            <div class="h-52 rounded-lg border {{ $isDark ? 'border-slate-700 bg-slate-800' : 'border-stone-200 bg-stone-50' }} p-3">
              <svg viewBox="0 0 100 100" class="h-full w-full">
                <polyline fill="none" stroke="#2563eb" stroke-width="1.8" points="{{ implode(' ', $timelinePoints) }}"></polyline>
              </svg>
            </div>
            <div class="mt-2 flex items-center justify-between text-[11px] {{ $subtleClass }}">
              <span>{{ $timelineLabels[0] ?? 'start' }}</span>
              <span>{{ end($timelineLabels) ?: 'end' }}</span>
            </div>
          </div>
        </div>

        <div class="rounded-xl border {{ $cardClass }} p-4">
          <h3 class="text-lg font-semibold mb-3">{{ __('sentinel.charts.top_directives') }}</h3>
          @php
            $topLabels = (array) ($cspTopDirectivesMetrics['labels'] ?? []);
            $topValues = (array) ($cspTopDirectivesMetrics['values'] ?? []);
            $topMax = 1;
            foreach ($topValues as $rawValue) {
                $topMax = max($topMax, (int) $rawValue);
            }
          @endphp
          <div class="space-y-2">
            @forelse($topLabels as $index => $directive)
              @php
                $count = (int) ($topValues[$index] ?? 0);
                $pct = (int) round(($count / $topMax) * 100);
              @endphp
              <div>
                <div class="mb-1 flex items-center justify-between text-xs">
                  <span class="font-mono {{ $mutedClass }}">{{ $directive }}</span>
                  <span class="{{ $subtleClass }}">{{ $count }}</span>
                </div>
                <div class="h-2 rounded-full {{ $isDark ? 'bg-slate-700' : 'bg-stone-200' }} overflow-hidden">
                  <div class="h-full bg-violet-600" style="width: {{ $pct }}%"></div>
                </div>
              </div>
            @empty
              <p class="text-sm {{ $subtleClass }}">{{ __('sentinel.charts.no_data') }}</p>
            @endforelse
          </div>
        </div>

        <div class="rounded-xl border {{ $cardClass }} p-4">
          <h3 class="text-lg font-semibold mb-3">{{ __('sentinel.hardening.title') }}</h3>
          @if(isset($hardeningPlan) && count($hardeningPlan) > 0)
            <div class="space-y-2">
              @foreach($hardeningPlan as $item)
                <div class="rounded-lg border {{ $isDark ? 'border-slate-700' : 'border-stone-200' }} p-3">
                  <div class="mb-1 flex items-center justify-between gap-2">
                    <p class="font-semibold text-sm">{{ $item['title'] }}</p>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase {{ $item['priority'] === 'high' ? 'bg-rose-100 text-rose-700' : ($item['priority'] === 'medium' ? 'bg-amber-100 text-amber-700' : ($isDark ? 'bg-slate-700 text-slate-200' : 'bg-stone-100 text-stone-700')) }}">{{ $item['priority'] }}</span>
                  </div>
                  <p class="text-sm {{ $subtleClass }}">{{ $item['description'] }}</p>
                </div>
              @endforeach
            </div>
          @else
            <p class="text-sm {{ $subtleClass }}">{{ __('sentinel.hardening.empty') }}</p>
          @endif
        </div>
      @endif

      @if($currentSection === 'headers')
        <div class="rounded-xl border {{ $cardClass }} p-4">
          <h3 class="text-lg font-semibold mb-3">{{ __('sentinel.headers.title') }}</h3>
          <div class="overflow-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="text-left border-b {{ $isDark ? 'border-slate-700' : '' }}"><th class="py-2 pr-2">Header</th><th class="py-2">Value</th></tr>
              </thead>
              <tbody>
                @forelse($expectedHeaders as $name => $value)
                  <tr class="border-b {{ $isDark ? 'border-slate-700' : '' }}"><td class="py-2 pr-2 font-mono text-xs">{{ $name }}</td><td class="py-2 break-all">{{ $value }}</td></tr>
                @empty
                  <tr><td class="py-2 {{ $subtleClass }}" colspan="2">{{ __('sentinel.headers.empty') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      @endif

      @if($currentSection === 'endpoints' && isset($endpointScan) && ($endpointScan['enabled'] ?? false))
        <div class="rounded-xl border {{ $cardClass }} p-4">
          <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-semibold">{{ __('sentinel.endpoints.title') }}</h3>
            <div class="flex items-center gap-2 text-xs">
              <a href="{{ $baseSentinelPath }}?{{ $sentinelQuery(['section' => 'endpoints', 'theme' => (string) ($uiTheme ?? 'light'), 'export' => 'endpoints', 'format' => 'json']) }}" class="rounded border px-3 py-1 {{ $isDark ? 'border-slate-600 text-slate-200' : 'border-stone-300 bg-white text-stone-700' }}">{{ __('sentinel.endpoints.export_json') }}</a>
              <a href="{{ $baseSentinelPath }}?{{ $sentinelQuery(['section' => 'endpoints', 'theme' => (string) ($uiTheme ?? 'light'), 'export' => 'endpoints', 'format' => 'csv']) }}" class="rounded border px-3 py-1 {{ $isDark ? 'border-slate-600 text-slate-200' : 'border-stone-300 bg-white text-stone-700' }}">{{ __('sentinel.endpoints.export_csv') }}</a>
            </div>
          </div>

          <form method="GET" class="mb-3 rounded-lg border {{ $surfaceClass }} p-3">
            <input type="hidden" name="section" value="endpoints">
            <input type="hidden" name="theme" value="{{ (string) ($uiTheme ?? 'light') }}">
            <label for="scan_paths" class="mb-1 block text-xs font-semibold {{ $mutedClass }}">{{ __('sentinel.endpoints.scan_paths') }}</label>
            <textarea id="scan_paths" name="scan_paths" rows="3" class="w-full rounded border {{ $isDark ? 'border-slate-600 bg-slate-900 text-slate-100' : 'border-stone-300 bg-white text-stone-800' }} p-2 text-xs font-mono">{{ implode("\n", (array) ($endpointScan['selected_paths'] ?? [])) }}</textarea>
            <div class="mt-2 flex items-center gap-2">
              <button type="submit" class="rounded bg-stone-900 px-3 py-1 text-xs font-semibold text-white">{{ __('sentinel.endpoints.run_scan') }}</button>
              <a href="{{ $baseSentinelPath }}?{{ $sentinelQuery(['section' => 'endpoints', 'theme' => (string) ($uiTheme ?? 'light')], ['scan_paths', 'export', 'format']) }}" class="rounded border px-3 py-1 text-xs {{ $isDark ? 'border-slate-600 text-slate-200' : 'border-stone-300 bg-white text-stone-700' }}">{{ __('sentinel.endpoints.reset') }}</a>
            </div>
          </form>

          <div class="mb-3 grid md:grid-cols-8 gap-3">
            <div class="rounded-lg border {{ $isDark ? 'border-slate-700' : 'border-stone-200' }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.endpoints.stats.scanned') }}</p><p class="text-xl font-semibold">{{ $endpointScan['summary']['total'] }}</p></div>
            <div class="rounded-lg border {{ $isDark ? 'border-slate-700' : 'border-stone-200' }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.endpoints.stats.ok') }}</p><p class="text-xl font-semibold text-emerald-700">{{ $endpointScan['summary']['ok'] }}</p></div>
            <div class="rounded-lg border {{ $isDark ? 'border-slate-700' : 'border-stone-200' }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.endpoints.stats.with_missing') }}</p><p class="text-xl font-semibold text-amber-700">{{ $endpointScan['summary']['with_missing'] }}</p></div>
            <div class="rounded-lg border {{ $isDark ? 'border-slate-700' : 'border-stone-200' }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.endpoints.stats.with_mismatched') }}</p><p class="text-xl font-semibold text-orange-700">{{ $endpointScan['summary']['with_mismatched'] }}</p></div>
            <div class="rounded-lg border {{ $isDark ? 'border-slate-700' : 'border-stone-200' }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.endpoints.stats.total_missing') }}</p><p class="text-xl font-semibold text-rose-700">{{ $endpointScan['summary']['total_missing_headers'] }}</p></div>
            <div class="rounded-lg border {{ $isDark ? 'border-slate-700' : 'border-stone-200' }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.endpoints.stats.total_mismatched') }}</p><p class="text-xl font-semibold text-orange-700">{{ $endpointScan['summary']['total_mismatched_headers'] }}</p></div>
            <div class="rounded-lg border {{ $isDark ? 'border-slate-700' : 'border-stone-200' }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.endpoints.stats.average_score') }}</p><p class="text-xl font-semibold">{{ $endpointScan['summary']['average_score'] }}/100</p></div>
            <div class="rounded-lg border {{ $isDark ? 'border-slate-700' : 'border-stone-200' }} p-3"><p class="text-xs {{ $subtleClass }}">{{ __('sentinel.endpoints.stats.worst_score') }}</p><p class="text-xl font-semibold">{{ $endpointScan['summary']['worst_score'] }}/100</p></div>
          </div>

          <div class="overflow-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="text-left border-b {{ $isDark ? 'border-slate-700' : '' }}">
                  <th class="py-2 pr-2">{{ __('sentinel.endpoints.table.path') }}</th><th class="py-2 pr-2">{{ __('sentinel.endpoints.table.status') }}</th><th class="py-2 pr-2">{{ __('sentinel.endpoints.table.score') }}</th><th class="py-2 pr-2">{{ __('sentinel.endpoints.table.severity') }}</th><th class="py-2 pr-2">{{ __('sentinel.endpoints.table.headers_ok') }}</th><th class="py-2 pr-2">{{ __('sentinel.endpoints.table.missing_headers') }}</th><th class="py-2">{{ __('sentinel.endpoints.table.mismatched_headers') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($endpointScan['rows'] as $row)
                  <tr class="border-b align-top {{ $isDark ? 'border-slate-700' : '' }}">
                    <td class="py-2 pr-2 font-mono text-xs">{{ $row['path'] }}</td>
                    <td class="py-2 pr-2">{{ $row['status'] }}</td>
                    <td class="py-2 pr-2 font-semibold">{{ (int) ($row['score'] ?? 100) }}/100</td>
                    <td class="py-2 pr-2">
                      @php
                        $severity = (string) ($row['severity'] ?? 'low');
                        $severityClass = 'bg-emerald-100 text-emerald-700';
                        if ($severity === 'critical') {
                          $severityClass = 'bg-rose-100 text-rose-700';
                        } elseif ($severity === 'high') {
                          $severityClass = 'bg-orange-100 text-orange-700';
                        } elseif ($severity === 'medium') {
                          $severityClass = 'bg-amber-100 text-amber-700';
                        }
                      @endphp
                      <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase {{ $severityClass }}">{{ $severity }}</span>
                    </td>
                    <td class="py-2 pr-2"><span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase {{ $row['ok'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $row['ok'] ? __('sentinel.endpoints.table.yes') : __('sentinel.endpoints.table.no') }}</span></td>
                    <td class="py-2">
                      @if(count($row['missing_headers']) > 0)
                        <div class="flex flex-wrap gap-1">@foreach($row['missing_headers'] as $missing)<span class="rounded {{ $isDark ? 'bg-slate-700' : 'bg-stone-100' }} px-2 py-0.5 text-[10px] font-mono">{{ $missing }}</span>@endforeach</div>
                      @else
                        <span class="{{ $subtleClass }}">-</span>
                      @endif
                    </td>
                    <td class="py-2">
                      @if(count($row['mismatched_headers']) > 0)
                        <details>
                          <summary class="cursor-pointer text-xs {{ $mutedClass }}">{{ __('sentinel.endpoints.table.mismatch_count', ['count' => count($row['mismatched_headers'])]) }}</summary>
                          <div class="mt-1 space-y-1">
                            @foreach($row['mismatched_headers'] as $diff)
                              <div class="rounded {{ $isDark ? 'bg-slate-800' : 'bg-stone-100' }} p-2 text-[10px]">
                                <p><span class="font-semibold">Header:</span> <span class="font-mono">{{ $diff['header'] }}</span></p>
                                <p><span class="font-semibold">Expected:</span> <span class="font-mono break-all">{{ $diff['expected'] }}</span></p>
                                <p><span class="font-semibold">Actual:</span> <span class="font-mono break-all">{{ $diff['actual'] }}</span></p>
                              </div>
                            @endforeach
                          </div>
                        </details>
                      @else
                        <span class="{{ $subtleClass }}">-</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr><td class="py-2 {{ $subtleClass }}" colspan="7">{{ __('sentinel.endpoints.table.empty') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      @endif

      @if($currentSection === 'reports')
        @if($learningSuggestions !== [])
          <div class="rounded-xl border {{ $cardClass }} p-4">
            <h3 class="text-lg font-semibold mb-3">{{ __('sentinel.reports.learning') }}</h3>
            <table class="w-full text-sm">
              <thead><tr class="border-b {{ $isDark ? 'border-slate-700' : '' }}"><th class="text-left py-2">{{ __('sentinel.reports.directive') }}</th><th class="text-left py-2">{{ __('sentinel.reports.host') }}</th><th class="text-left py-2">{{ __('sentinel.reports.hits') }}</th></tr></thead>
              <tbody>
                @foreach($learningSuggestions as $item)
                  <tr class="border-b {{ $isDark ? 'border-slate-700' : '' }}"><td class="py-2">{{ $item['directive'] }}</td><td class="py-2">{{ $item['host'] }}</td><td class="py-2">{{ $item['hits'] }}</td></tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        @if($showCspReports && isset($recentReports) && count($recentReports) > 0)
          <div class="rounded-xl border {{ $cardClass }} p-4">
            <h3 class="text-lg font-semibold mb-3">{{ __('sentinel.reports.recent') }}</h3>
            <div class="overflow-auto">
              <table class="w-full text-sm">
                <thead><tr class="border-b {{ $isDark ? 'border-slate-700' : '' }}"><th class="text-left py-2">{{ __('sentinel.reports.received') }}</th><th class="text-left py-2">{{ __('sentinel.reports.directive') }}</th><th class="text-left py-2">{{ __('sentinel.reports.blocked_uri') }}</th><th class="text-left py-2">{{ __('sentinel.reports.document_uri') }}</th></tr></thead>
                <tbody>
                  @foreach($recentReports as $r)
                    <tr class="border-b {{ $isDark ? 'border-slate-700' : '' }}"><td class="py-2">{{ optional($r->received_at)->toDateTimeString() }}</td><td class="py-2">{{ $r->effective_directive ?: $r->violated_directive }}</td><td class="py-2 break-all">{{ $r->blocked_uri }}</td><td class="py-2 break-all">{{ $r->document_uri }}</td></tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @endif

        @if($learningSuggestions === [] && (!isset($recentReports) || count($recentReports) === 0))
          <div class="rounded-xl border {{ $cardClass }} p-4"><p class="text-sm {{ $subtleClass }}">{{ __('sentinel.reports.empty') }}</p></div>
        @endif
      @endif

      @if($currentSection === 'config')
        <div class="rounded-xl border {{ $cardClass }} p-4">
          <h3 class="text-lg font-semibold mb-2">{{ __('sentinel.config.title') }}</h3>
          <pre class="text-xs {{ $isDark ? 'bg-slate-800' : 'bg-stone-100' }} rounded p-3 overflow-auto">{{ json_encode($configSnapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
      @endif
    </main>
  </div>
@endsection
