{{-- resources/views/articles/show.blade.php --}}
@extends('layouts.app')
@section('title', $article->title)

@section('content')
<div class="max-w-4xl mx-auto py-8 space-y-6">

  @php
    // 1) Couverture
    $cover = $article->getFirstMedia('cover');

    // 2) Galerie prioritaire
    $gallery = $article->getMedia('gallery');

    // 3) Fallback : si pas d’images dans "gallery", on prend toutes les images
    //    sauf la couverture (utile si tu as uploadé dans la collection par défaut).
    if ($gallery->isEmpty()) {
        $gallery = $article->media->filter(function ($m) use ($cover) {
            return !$cover || $m->id !== $cover->id; // exclure la cover
        })->values();
    }
  @endphp

  {{-- Couverture en haut --}}
  @if($cover)
    <section class="surface p-0 overflow-hidden">
      @php
        $coverSrc = $cover->hasGeneratedConversion('cover_webp')
          ? $cover->getUrl('cover_webp')
          : $cover->getUrl();
        $coverAlt = e($cover->getCustomProperty('alt') ?: $article->title);
      @endphp
      <img src="{{ $coverSrc }}" alt="{{ $coverAlt }}" class="article-cover" style="width:100%;height:auto;display:block">
    </section>
  @endif

  {{-- Titre + méta --}}
  <header class="surface p-5">
    <h1 class="text-3xl font-bold text-gray-900">{{ $article->title }}</h1>
    @if($article->published_at)
      <div class="text-sm text-gray-600 mt-1">
        Publié le {{ $article->published_at->locale(app()->getLocale())->isoFormat('D MMMM YYYY') }}
      </div>
    @endif
  </header>

  {{-- Contenu --}}
  <article class="surface p-6">
    <div class="prose max-w-none">
      {!! $article->content !!}
    </div>
  </article>

 {{-- Carrousel (sous le texte) --}}
@if($gallery->isNotEmpty())
  <section class="surface p-0 overflow-hidden">
    <div class="gallery" data-gallery>
      <button class="gal-nav gal-prev" type="button" aria-label="Image précédente">⟵</button>

      <div class="gal-track" data-track>
        @foreach($gallery as $media)
          @php
            $src  = $media->hasGeneratedConversion('gallery_webp')
              ? $media->getUrl('gallery_webp')
              : ($media->hasGeneratedConversion('cover_webp') ? $media->getUrl('cover_webp') : $media->getUrl());
            $zoom = $media->hasGeneratedConversion('gallery_webp')
              ? $media->getUrl('gallery_webp')
              : $media->getUrl();
            $alt  = e($media->getCustomProperty('alt') ?: $article->title);
          @endphp
          <figure class="gal-slide" data-zoom="{{ $zoom }}">
            <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy" class="gal-img">
          </figure>
        @endforeach
      </div>

      <button class="gal-nav gal-next" type="button" aria-label="Image suivante">⟶</button>

      <div class="gal-dots" data-dots></div>
    </div>
  </section>
@endif

{{-- Lightbox pour zoom (laisser à la fin de la page) --}}
<div id="lightbox" class="gal-lightbox" hidden>
  <button class="gal-lightbox-close" aria-label="Fermer">&times;</button>
  <img id="lightboxImg" src="" alt="" class="gal-lightbox-img">
</div>

@push('scripts')
  @vite('resources/js/gallery.js')
@endpush


@endsection
