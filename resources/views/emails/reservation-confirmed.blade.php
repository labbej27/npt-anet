{{-- resources/views/emails/reservation-confirmed.blade.php --}}
<p>Bonjour {{ $reservation->full_name }},</p>
<p>Votre inscription est <strong>confirmée</strong> à l’atelier « {{ $reservation->session->topic }} ».</p>
<ul>
<li><strong>Date :</strong> {{ $reservation->session->date->format('d/m/Y') }}</li>
<li><strong>Heure :</strong> {{ $reservation->session->start_time->format('H:i') }}–{{ $reservation->session->end_time->format('H:i') }}</li>
<li><strong>Lieu :</strong> {{ $reservation->session->location }}</li>
</ul>
<p>Besoin d’annuler ? <a href="{{ route('reservation.cancel', $reservation->cancel_token) }}">Cliquez ici</a>.</p>
<p>À bientôt !</p>