<?php

namespace App\Http\Controllers;

use App\Mail\ReservationActionRequired; // e-mail "Valider la réservation"
use App\Mail\ReservationConfirmed;      // e-mail final + ICS
use App\Models\Reservation;
use App\Models\WorkshopSession;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    /**
     * Crée une demande de réservation (status = pending) et envoie l'e-mail de validation.
     */
    public function store(Request $request, WorkshopSession $session)
    {
        // Ne compte que les confirmés côté modèle (spotsLeft)
        abort_if($session->spotsLeft() <= 0, 409, 'Ce créneau est complet.');

        $data = $request->validate([
            'full_name' => 'required|string|min:2',
            'email'     => 'required|email',
            'phone'     => 'nullable|string',
        ]);

        // Normaliser l’e-mail (aligne avec l’unique index DB)
        $email = mb_strtolower(trim($data['email']));
        $data['email'] = $email;

        // Existe déjà pour ce créneau + email ?
        $existing = $session->reservations()->where('email', $email)->first();

        if ($existing) {
            // Si annulée → on réactive en pending et on renvoie le mail de validation
            if ($existing->status === Reservation::STATUS_CANCELLED) {
                $existing->update([
                    'full_name'     => $data['full_name'],
                    'phone'         => $data['phone'] ?? null,
                    'status'        => Reservation::STATUS_PENDING,
                    'confirm_token' => Str::uuid()->toString(),
                    'cancel_token'  => $existing->cancel_token ?: Str::uuid()->toString(),
                ]);

                Mail::to($existing->email)->send(new ReservationActionRequired($existing));

                return redirect()
                    ->route('calendar.index')
                    ->with('ok', 'Votre demande a été réactivée. Vérifiez votre e-mail et cliquez sur « Valider la réservation ».');
            }

            // Déjà pending ou confirmed → message propre
            return back()->withErrors([
                'email' => 'Vous êtes déjà inscrit (en attente ou confirmé) sur ce créneau.',
            ])->withInput();
        }

        // Création initiale en pending (double opt-in)
        try {
            $reservation = $session->reservations()->create([
                ...$data,
                'status'        => Reservation::STATUS_PENDING,
                'cancel_token'  => Str::uuid()->toString(),
                'confirm_token' => Str::uuid()->toString(),
            ]);
        } catch (QueryException $e) {
            // Concurrence / double-clic : l’unique index (23000) a parlé → message propre
            if ((int) $e->getCode() === 23000) {
                return back()->withErrors([
                    'email' => 'Vous êtes déjà inscrit (en attente ou confirmé) sur ce créneau.',
                ])->withInput();
            }
            throw $e;
        }

        // E-mail : demande de validation
        Mail::to($reservation->email)->send(new ReservationActionRequired($reservation));
        // Optionnel : notifier en interne la demande (sans ICS)
        // if (config('mail.from.address')) {
        //     Mail::to(config('mail.from.address'))->send(new ReservationActionRequired($reservation, true));
        // }

        return redirect()
            ->route('calendar.index')
            ->with('ok', 'Merci ! Vérifiez votre e-mail et cliquez sur « Valider la réservation » pour confirmer votre place.');
    }

    /**
     * Clic sur le lien de validation (confirm_token) : on confirme + envoie l’ICS.
     */
    public function confirm(string $token)
    {
        $res = Reservation::where('confirm_token', $token)->firstOrFail();

        if ($res->status === Reservation::STATUS_CONFIRMED) {
            return redirect()->route('calendar.index')->with('ok', 'Votre réservation est déjà confirmée ✔️');
        }

        // Re-vérifier la capacité au moment du clic (les pending ne réservent pas la place)
        if ($res->session->spotsLeft() <= 0) {
            $res->update([
                'status'        => Reservation::STATUS_CANCELLED,
                'confirm_token' => null,
            ]);

            return redirect()->route('calendar.index')->withErrors([
                'full' => 'Désolé, ce créneau s’est rempli avant votre confirmation.',
            ]);
        }

        // Confirmer
        $res->update([
            'status'        => Reservation::STATUS_CONFIRMED,
            'confirm_token' => null,
        ]);

        // E-mail final avec ICS (participant + option interne)
        Mail::to($res->email)->send(new ReservationConfirmed($res));
        if (config('mail.from.address')) {
            Mail::to(config('mail.from.address'))->send(new ReservationConfirmed($res, true));
        }

        return redirect()->route('calendar.index')->with('ok', 'Réservation confirmée ! Un e-mail avec l’invitation calendrier vous a été envoyé.');
    }

    /**
     * Annulation via lien (cancel_token).
     */
    public function cancel(string $token)
    {
        $res = Reservation::where('cancel_token', $token)->firstOrFail();

        if ($res->status !== Reservation::STATUS_CANCELLED) {
            $res->update(['status' => Reservation::STATUS_CANCELLED]);
        }

        return redirect()->route('calendar.index')->with('ok', 'Votre réservation a été annulée.');
    }
}
