<?php
// app/Mail/ReservationConfirmed.php
namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ReservationConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation, public bool $internal = false) {}

    public function build()
    {
        $res = $this->reservation;
        $session = $res->session;

        $subject = $this->internal
            ? 'Nouvelle réservation – ' . $res->full_name
            : 'Confirmation de réservation – Numérique pour Tous Anet';

        // --- Normalisation des heures pour éviter la double date ---
        $startStr = $session->start_time instanceof \DateTimeInterface
            ? $session->start_time->format('H:i')
            : (string) $session->start_time;

        $endStr = $session->end_time instanceof \DateTimeInterface
            ? $session->end_time->format('H:i')
            : (string) $session->end_time;

        $dateStr = $session->date instanceof \DateTimeInterface
            ? $session->date->format('Y-m-d')
            : (string) $session->date;

        // Construit des DateTimes corrects en Europe/Paris
        $dtStart = Carbon::createFromFormat('Y-m-d H:i', "$dateStr $startStr", 'Europe/Paris');
        $dtEnd   = Carbon::createFromFormat('Y-m-d H:i', "$dateStr $endStr", 'Europe/Paris');

        // ICS minimal
        $ics = "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//Anet//Workshops//FR\nBEGIN:VEVENT\nUID:" . uniqid() . "@anet\nDTSTAMP:" . now()->utc()->format('Ymd\\THis\\Z') . "\nDTSTART:" . $dtStart->utc()->format('Ymd\\THis\\Z') . "\nDTEND:" . $dtEnd->utc()->format('Ymd\\THis\\Z') . "\nSUMMARY:Atelier – Inclusion numérique\nLOCATION:" . $session->location . "\nEND:VEVENT\nEND:VCALENDAR\n";

        return $this->subject($subject)
            ->view('emails.reservation-confirmed')
            ->attachData($ics, 'atelier.ics', ['mime' => 'text/calendar']);
    }
}
