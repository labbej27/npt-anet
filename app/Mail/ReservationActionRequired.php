<?php
// app/Mail/ReservationActionRequired.php
namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationActionRequired extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation, public bool $internal = false) {}

    public function build()
    {
        $subject = $this->internal
            ? 'Nouvelle demande de réservation (à valider)'
            : 'Validez votre réservation – Numérique pour Tous Anet';

        return $this->subject($subject)
            ->markdown('emails.reservation-action');
    }
}
