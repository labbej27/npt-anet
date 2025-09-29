<?php

namespace App\Http\Controllers;

use App\Models\WorkshopSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        $sessions = WorkshopSession::whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')->orderBy('start_time')->get();

        return view('calendar.index', compact('sessions'));
    }

    public function full()
    {
        return view('calendar.full');
    }

    public function events(): JsonResponse
    {
        $sessions = WorkshopSession::withCount('reservations')
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')->orderBy('start_time')
            ->get();

        $events = $sessions->map(function ($s) {
            $spotsLeft = max(0, $s->capacity - $s->reservations_count);
            $start = Carbon::parse($s->date->format('Y-m-d').' '.$s->start_time->format('H:i'), 'Europe/Paris');
            $end   = Carbon::parse($s->date->format('Y-m-d').' '.$s->end_time->format('H:i'), 'Europe/Paris');

            return [
                'id' => $s->id,
                'title' => sprintf('%s (%d/%d)', $s->start_time->format('H:i'), $s->reservations_count, $s->capacity),
                'start' => $start->toIso8601String(),
                'end'   => $end->toIso8601String(),
                'allDay' => false,
                'extendedProps' => [
                    'spotsLeft' => $spotsLeft,
                    'location'  => $s->location,
                    'topic'     => $s->topic,
                    'start_h'   => $s->start_time->format('H:i'),
                    'end_h'     => $s->end_time->format('H:i'),
                ],
                'backgroundColor' => $spotsLeft > 0 ? null : '#ef4444',
                'borderColor'     => $spotsLeft > 0 ? null : '#ef4444',
            ];
        });

        return response()->json($events);
    }
}
