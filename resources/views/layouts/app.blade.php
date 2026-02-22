<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Queopius Shield Dashboard')</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
@php
  $theme = (string) ($uiTheme ?? 'light');
  $isDark = $theme === 'dark';
  $pageBg = $isDark ? 'bg-slate-950' : 'bg-stone-100';
  $text = $isDark ? 'text-slate-100' : 'text-stone-900';
@endphp
<body class="{{ $pageBg }} {{ $text }}">
  <div class="min-h-screen">
    @yield('content')
  </div>
</body>
</html>
