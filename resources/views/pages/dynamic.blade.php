{{-- resources/views/pages/dynamic.blade.php --}}
@extends('layouts.app')
@section('title', $page->title)
@section('content')
  <section class="max-w-3xl mx-auto py-10">
    <h1 class="text-3xl font-bold mb-6">{{ $page->title }}</h1>
    <article class="prose prose-invert max-w-none">
      {!! $page->content !!}
    </article>
  </section>
@endsection