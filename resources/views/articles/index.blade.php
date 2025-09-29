{{-- resources/views/articles/index.blade.php --}}
@extends('layouts.app')
@section('title','L’association – Numérique pour Tous – Anet')

@section('content')
<div class="max-w-6xl mx-auto py-10 space-y-6">

  {{-- Bloc de présentation (éditable depuis Filament via slug "association-intro") --}}
@php use App\Filament\Resources\PageResource; @endphp

@if(!empty($intro))
  <section class="surface p-6">
    <h1 class="text-3xl font-bold text-gray-900 mb-3">
      {{ $intro->title ?? 'Notre association' }}
    </h1>
    <div class="prose max-w-none text-gray-900">
      {!! $intro->content !!}
    </div>

    @auth
      <div class="mt-3">
        <a class="inline-flex items-center px-3 py-1.5 rounded bg-blue-600 text-white text-sm"
           href="{{ PageResource::getUrl('edit', ['record' => optional(\App\Models\Page::where('slug','association-intro')->first())->id]) ?? (PageResource::getUrl('create').'?slug=association-intro&title=Présentation%20de%20l%27association') }}">
          Éditer la présentation
        </a>
      </div>
    @endauth
  </section>
@else
  {{-- Fallback si rien n’a encore été saisi --}}
  <section class="surface p-6">
    <h1 class="text-3xl font-bold text-gray-900 mb-3">Numérique pour Tous – Anet</h1>
    <p class="text-gray-800">
      Créez la page <strong>association-intro</strong> dans l’admin pour éditer ce texte d’introduction.
    </p>
    @auth
      <div class="mt-3">
        <a class="inline-flex items-center px-3 py-1.5 rounded bg-blue-600 text-white text-sm"
           href="{{ \App\Filament\Resources\PageResource::getUrl('create') . '?slug=association-intro&title=Présentation%20de%20l%27association' }}">
          Créer la présentation dans l’admin
        </a>
      </div>
    @endauth
  </section>
@endif

  {{-- Liste des articles publiés --}}
  @if($articles->isEmpty())
    <div class="surface p-6">Aucun article pour le moment.</div>
  @else
    <div class="grid gap-6 md:grid-cols-3">
      @foreach($articles as $a)
        <article class="card overflow-hidden">
          @if($url = $a->getFirstMediaUrl('cover', 'cover_webp'))
            <a href="{{ route('articles.show', $a->slug) }}">
              <img src="{{ $url }}" alt="{{ $a->title }}" class="w-full h-40 object-cover">
            </a>
          @endif

          <div class="p-4 space-y-2">
            <h2 class="text-lg font-semibold text-gray-900">
              <a href="{{ route('articles.show', $a->slug) }}" class="text-blue-700 hover:text-blue-800 underline">
                {{ $a->title }}
              </a>
            </h2>
            @if($a->published_at)
              <div class="text-xs text-gray-500">
                Publié le {{ $a->published_at->locale(app()->getLocale())->isoFormat('D MMMM YYYY') }}
              </div>
            @endif
            @if($a->excerpt)
              <p class="text-gray-700 text-sm">{{ $a->excerpt }}</p>
            @endif
          </div>
        </article>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $articles->links() }}
    </div>
  @endif
</div>
@endsection
