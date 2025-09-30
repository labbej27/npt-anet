<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Numérique pour Tous – Anet')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-flag-blue text-flag-white">
  <header class="border-b border-flag-white/20" x-data="{ open: false }" @keydown.escape="open=false" @click.outside="open=false">
    <nav class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <!-- Logo -->
      <a href="{{ route('home') }}" class="font-extrabold tracking-wide nav-link">
        Numérique pour Tous – Anet
      </a>

      <!-- Menu desktop -->
      <div class="hidden sm:flex gap-4">
        <a class="nav-link" href="{{ route('association') }}">L’association</a>
        <a class="nav-link" href="{{ route('calendar.index') }}">Agenda</a>
        <a class="nav-link" href="{{ route('calendar.full') }}">Calendrier</a>
        <a class="nav-link" href="{{ route('contact') }}">Contact</a>
        <a class="nav-link" href="{{ route('mentions') }}">Mentions légales</a>
        @auth 
          <a class="px-3 py-1 rounded bg-flag-red text-white" href="/admin">Admin</a> 
        @endauth
      </div>

      <!-- Bouton hamburger mobile -->
      <button @click="open = !open" class="sm:hidden focus:outline-none" aria-label="Ouvrir le menu" :aria-expanded="open">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </nav>

    <!-- Menu déroulant mobile -->
    <div class="sm:hidden" x-cloak x-show="open" x-transition.origin.top>
      <div class="flex flex-col px-4 pb-4 space-y-2 bg-flag-blue border-t border-flag-white/20">
        <a class="nav-link" href="{{ route('association') }}" @click="open=false">L’association</a>
        <a class="nav-link" href="{{ route('calendar.index') }}" @click="open=false">Agenda</a>
        <a class="nav-link" href="{{ route('calendar.full') }}" @click="open=false">Calendrier</a>
        <a class="nav-link" href="{{ route('contact') }}" @click="open=false">Contact</a>
        <a class="nav-link" href="{{ route('mentions') }}" @click="open=false">Mentions légales</a>
        @auth 
          <a class="px-3 py-1 rounded bg-flag-red text-white w-fit" href="/admin" @click="open=false">Admin</a> 
        @endauth
      </div>
    </div>
  </header>

  <main class="min-h-[70vh]">
    @yield('content')
  </main>

  <footer class="mt-10 py-6 text-center text-sm text-flag-white/80 border-t border-flag-white/20">
    © {{ date('Y') }} Numérique pour Tous – Anet
  </footer>
</body>
</html>
