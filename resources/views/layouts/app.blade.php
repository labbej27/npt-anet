{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Numérique pour Tous – Anet')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-flag-blue text-flag-white">
  <header class="border-b border-flag-white/20">
    <nav class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ route('home') }}" class="font-extrabold tracking-wide nav-link">Numérique pour Tous – Anet</a>
      <div class="flex gap-4">
        <a class="nav-link" href="{{ route('association') }}">L’association</a>
        <a class="nav-link" href="{{ route('calendar.index') }}">Agenda</a>
        <a class="nav-link" href="{{ route('calendar.full') }}">Calendrier</a>
        <a class="nav-link" href="{{ route('contact') }}">Contact</a>
        <a class="nav-link" href="{{ route('mentions') }}">Mentions légales</a>
        @auth <a class="px-3 py-1 rounded bg-flag-red text-white" href="/admin">Admin</a> @endauth
      </div>
    </nav>
  </header>

  <main class="min-h-[70vh]">
    @yield('content')
  </main>

  <footer class="mt-10 py-6 text-center text-sm text-flag-white/80 border-t border-flag-white/20">
    © {{ date('Y') }} Numérique pour Tous – Anet
  </footer>
</body>
</html>
