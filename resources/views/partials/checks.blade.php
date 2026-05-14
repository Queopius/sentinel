@php
  $dark = (bool) ($isDark ?? false);
@endphp
<div class="rounded-xl border {{ $dark ? 'border-slate-700 bg-slate-900' : 'border-stone-200 bg-white' }} p-4">
  <h3 class="text-lg font-semibold mb-3">{{ __('sentinel.checks.title') }}</h3>
  <div class="space-y-2">
    @foreach($checks as $check)
      @php
        $badge = $check['status'] === 'ok' ? 'bg-emerald-100 text-emerald-800' : ($check['status'] === 'warning' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800');
      @endphp
      <div class="flex items-center justify-between gap-3 rounded-lg border {{ $dark ? 'border-slate-700' : 'border-stone-200' }} p-2">
        <span class="font-mono text-xs {{ $dark ? 'text-slate-300' : 'text-stone-700' }}">{{ $check['key'] }}</span>
        <span class="rounded-full px-2 py-1 text-xs {{ $badge }}">{{ strtoupper($check['status']) }}</span>
      </div>
      <p class="text-xs {{ $dark ? 'text-slate-400' : 'text-stone-600' }} -mt-1">{{ $check['message'] }}</p>
    @endforeach
  </div>
</div>
