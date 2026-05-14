@php
  $dark = (bool) ($isDark ?? false);
@endphp
<div class="rounded-xl border {{ $dark ? 'border-slate-700 bg-slate-900' : 'border-stone-200 bg-white' }} p-4">
  <h3 class="text-lg font-semibold mb-3">{{ __('sentinel.warnings.title') }}</h3>
  @if($warnings === [])
    <p class="text-sm text-emerald-700">{{ __('sentinel.warnings.empty') }}</p>
  @else
    <ul class="space-y-2 text-sm {{ $dark ? 'text-slate-200' : 'text-stone-700' }}">
      @foreach($warnings as $warning)
        <li class="rounded-lg border {{ $dark ? 'border-amber-700 bg-amber-950/40' : 'border-amber-200 bg-amber-50' }} p-2">{{ $warning }}</li>
      @endforeach
    </ul>
  @endif
</div>
