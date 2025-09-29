{{-- resources/views/pages/association.blade.php --}}
@extends('layouts.app')
@section('title','Numérique pour Tous – Anet')
@section('content')
<section class="max-w-5xl mx-auto py-10 space-y-8">
  <header class="space-y-3">
    <h1 class="text-4xl font-extrabold">Numérique pour Tous – Anet</h1>
    <p class="text-flag-white/90">
      Aide à l’<strong>inclusion numérique</strong> en privilégiant les <strong>logiciels libres</strong>.
      Ateliers les mercredis à la <strong>Mairie d’Anet</strong> (14h–17h).
    </p>
    <a class="inline-block px-4 py-2 bg-flag-red text-white rounded hover:bg-flag-red/90"
       href="{{ route('calendar.index') }}">Voir l’agenda & réserver</a>
  </header>

  <div class="grid gap-6 md:grid-cols-3">
    @forelse($articles as $a)
      @php $cover = $a->getFirstMediaUrl('cover','thumb') ?: $a->getFirstMediaUrl('cover'); @endphp
      <article class="bg-white/95 rounded-lg shadow border border-flag-white/20 overflow-hidden">
        @if($cover)
          <a href="{{ route('articles.show',$a->slug) }}">
            <img src="{{ $cover }}" alt="" class="w-full h-40 object-cover">
          </a>
        @endif
        <div class="p-4 space-y-2">
          <h2 class="font-semibold text-lg">
            <a href="{{ route('articles.show',$a->slug) }}" class="text-flag-blue hover:underline">{{ $a->title }}</a>
          </h2>
          @if($a->published_at)
            <div class="text-xs text-gray-500">Publié le {{ $a->published_at->locale('fr')->isoFormat('D MMM YYYY') }}</div>
          @endif
          <p class="text-sm text-gray-700">{{ $a->excerpt }}</p>
          <a class="text-sm text-flag-red hover:underline" href="{{ route('articles.show',$a->slug) }}">Lire →</a>
        </div>
      </article>
    @empty
      <p class="text-flag-white/90">Aucun article pour le moment.</p>
    @endforelse
  </div>
</section>
@endsection
