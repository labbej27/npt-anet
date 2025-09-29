@extends('layouts.app')
@section('title','Calendrier des ateliers')

@section('content')
<div class="max-w-5xl mx-auto py-8">
  <h1 class="text-2xl font-bold mb-6">Calendrier des ateliers</h1>
  <div id="calendar"></div>
</div>

<!-- Modale réservation -->
<dialog id="reserveDialog" class="md:w-[600px] w-[95%]">
  <form method="dialog">
    <button class="absolute right-3 top-2 text-gray-500">✕</button>
  </form>
  <div class="p-5 space-y-4">
    <h2 class="text-xl font-semibold">Réserver ce créneau</h2>
    <div id="reserveInfos" class="text-sm text-gray-600"></div>
    <form id="reserveForm" method="POST" class="space-y-3">
      @csrf
      <div class="grid md:grid-cols-3 gap-3">
        <input name="full_name" required placeholder="Nom complet" class="form-field">
        <input name="email" type="email" required placeholder="E-mail" class="form-field">
        <input name="phone" placeholder="Téléphone (optionnel)" class="form-field">
      </div>
      <div class="flex items-center gap-2">
        <button class="btn-primary">Confirmer la réservation</button>
        <button type="button" onclick="document.getElementById('reserveDialog').close()" class="btn-secondary">Annuler</button>
      </div>
    </form>
  </div>
</dialog>
@endsection
