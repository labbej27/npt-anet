{{-- resources/views/calendar/index.blade.php --}}
@extends('layouts.app')
@section('title','Agenda des ateliers')

@section('content')
@php
    use Illuminate\Support\Carbon;

    // Regrouper par semaine (lundi -> dimanche)
    $weeks = $sessions->groupBy(function ($s) {
        return $s->date->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
    });
@endphp

<div class="max-w-4xl mx-auto py-10">
  <h1 class="text-2xl font-bold mb-6">Agenda des ateliers</h1>

  @if(session('ok'))
    <div class="surface p-3 mb-4 bg-green-50 text-green-800 border-green-200">
      {{ session('ok') }}
    </div>
  @endif

  @forelse($weeks as $weekStartStr => $weekSessions)
    @php
      $weekStart = Carbon::parse($weekStartStr)->locale(app()->getLocale());
      $weekEnd   = $weekStart->copy()->addDays(6);

      // Regrouper la semaine par jour
      $days = $weekSessions->groupBy(fn($s) => $s->date->toDateString())->sortKeys();

      // Total places restantes sur la semaine
      $weekLeft = $weekSessions->sum(fn($s) =>
          method_exists($s,'spotsLeft') ? $s->spotsLeft()
          : max(0, ($s->capacity ?? 0) - ($s->reservations_count ?? $s->reservations()->count()))
      );
    @endphp

    <section class="surface mb-8 overflow-hidden">
      <div class="px-4 py-3 border-b bg-gray-50 text-gray-700 flex items-center justify-between">
        <div class="font-semibold">
          Semaine {{ $weekStart->isoWeek }} — du
          {{ $weekStart->isoFormat('dddd D MMMM') }}
          au
          {{ $weekEnd->isoFormat('dddd D MMMM YYYY') }}
        </div>
        <div class="text-sm">
          Places restantes sur la semaine : <strong>{{ $weekLeft }}</strong>
        </div>
      </div>

      <div class="divide-y">
        @foreach($days as $dateStr => $daySessions)
          @php
            $date = Carbon::parse($dateStr)->locale(app()->getLocale());
            $dayLeft = $daySessions->sum(fn($s) =>
              method_exists($s,'spotsLeft') ? $s->spotsLeft()
              : max(0, ($s->capacity ?? 0) - ($s->reservations_count ?? $s->reservations()->count()))
            );
          @endphp

          <div class="p-4">
            <div class="mb-3 flex items-center justify-between">
              <h2 class="text-lg font-medium text-gray-900">
                {{ $date->isoFormat('dddd D MMMM YYYY') }}
              </h2>
              <span class="text-sm text-gray-600">
                Places restantes : <strong>{{ $dayLeft }}</strong>
              </span>
            </div>

            <div class="grid gap-3">
              @foreach($daySessions->sortBy('start_time') as $s)
                @php
                  $left = method_exists($s,'spotsLeft')
                    ? $s->spotsLeft()
                    : max(0, ($s->capacity ?? 0) - ($s->reservations_count ?? $s->reservations()->count()));
                @endphp

                <form method="POST"
                      action="{{ route('reservation.store', $s) }}"
                      class="card p-3 flex flex-col md:flex-row md:items-center md:justify-between gap-3
                             {{ $left === 0 ? 'ring-1 ring-red-200 bg-red-50' : '' }}">
                  @csrf

                  <div>
                    <div class="font-medium text-gray-900">
                      {{ $s->topic }}
                    </div>
                    <div class="text-sm text-gray-700">
                      Lieu : {{ $s->location }} • Capacité : {{ $s->capacity }} •
                      Horaire : {{ $s->start_time->format('H:i') }}–{{ $s->end_time->format('H:i') }} •
                      Places restantes : <strong>{{ $left }}</strong>
                    </div>
                  </div>

                  @if($left > 0)
                    <div class="flex flex-wrap gap-2">
                      <input required name="full_name" placeholder="Nom complet" class="form-field">
                      <input required type="email" name="email" placeholder="E-mail" class="form-field">
                      <input name="phone" placeholder="Téléphone (optionnel)" class="form-field">
                      <button class="btn-primary">Réserver</button>
                    </div>
                  @else
                    <span class="chip chip-danger">Complet</span>
                  @endif
                </form>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    </section>
  @empty
    <p class="surface p-4">Aucun créneau à venir.</p>
  @endforelse
</div>
@endsection
