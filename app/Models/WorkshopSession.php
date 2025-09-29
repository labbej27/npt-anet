<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WorkshopSession extends Model
{
    protected $fillable = ['date','start_time','end_time','capacity','location','topic'];

    protected $casts = [
        'date'       => 'date',
        // TIME en DB mais on garde un Carbon « H:i » côté PHP
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];

    /* =======================
     |  Relations
     |=======================*/
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** Réservations confirmées (celles qui « comptent ») */
    public function confirmedReservations(): HasMany
    {
        return $this->hasMany(Reservation::class)->where('status', Reservation::STATUS_CONFIRMED);
    }

    /** Réservations en attente (double opt-in non cliqué) */
    public function pendingReservations(): HasMany
    {
        return $this->hasMany(Reservation::class)->where('status', Reservation::STATUS_PENDING);
    }

    /** Réservations annulées */
    public function cancelledReservations(): HasMany
    {
        return $this->hasMany(Reservation::class)->where('status', Reservation::STATUS_CANCELLED);
    }

    /* =======================
     |  Compteurs & helpers
     |=======================*/

    /** Compte confirmés – utilise withCount('confirmedReservations') si présent, sinon requête */
    public function confirmedCount(): int
    {
        $attr = 'confirmed_reservations_count'; // nom Eloquent lors d’un withCount('confirmedReservations')
        return $this->getAttribute($attr) ?? $this->confirmedReservations()->count();
    }

    /** Compte en attente */
    public function pendingCount(): int
    {
        $attr = 'pending_reservations_count';
        return $this->getAttribute($attr) ?? $this->pendingReservations()->count();
    }

    /** Compte annulés */
    public function cancelledCount(): int
    {
        $attr = 'cancelled_reservations_count';
        return $this->getAttribute($attr) ?? $this->cancelledReservations()->count();
    }

    /** Places restantes (ne décompte que les confirmés) */
    public function spotsLeft(): int
    {
        return max(0, $this->capacity - $this->confirmedCount());
    }

    /** Attribut pratique : est-ce complet ? */
    protected function isFull(): Attribute
    {
        return Attribute::get(fn () => $this->spotsLeft() <= 0);
    }

    /* =======================
     |  Listes d’inscrits (affichage)
     |=======================*/

    /** Liste des inscrits confirmés (nom, email, phone) */
    public function confirmedAttendees()
    {
        return $this->confirmedReservations()
            ->orderBy('full_name')
            ->get(['full_name','email','phone']);
    }

    /** Liste des inscrits en attente */
    public function pendingAttendees()
    {
        return $this->pendingReservations()
            ->orderBy('full_name')
            ->get(['full_name','email','phone']);
    }

    /** CSV des inscrits confirmés (ex. pour affichage rapide) */
    public function confirmedAttendeesCsv(string $separator = ', '): string
    {
        return $this->confirmedAttendees()
            ->map(fn ($r) => "{$r->full_name} <{$r->email}>")
            ->join($separator);
    }

    /* =======================
     |  Scopes utiles
     |=======================*/
    public function scopeUpcoming($q)
    {
        return $q->whereDate('date', '>=', now()->toDateString());
    }

    public function scopeOnWednesday($q)
    {
        // 3 = mercredi (Carbon::WEDNESDAY) — selon SGBD, on pourrait utiliser DAYOFWEEK
        return $q->whereRaw('WEEKDAY(`date`) = 2');
    }
}
