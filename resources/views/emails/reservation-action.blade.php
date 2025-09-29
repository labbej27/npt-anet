{{-- resources/views/emails/reservation-action.blade.php --}}
@component('mail::message')
# {{ $reservation->full_name }}, validez votre réservation

Merci pour votre inscription à **{{ $reservation->session->topic }}**.

**Quand :** {{ $reservation->session->date->format('d/m/Y') }}
de {{ $reservation->session->start_time->format('H:i') }}
à {{ $reservation->session->end_time->format('H:i') }}  
**Où :** {{ $reservation->session->location }}

@component('mail::button', ['url' => route('reservation.confirm', $reservation->confirm_token)])
Valider la réservation
@endcomponent

> Votre place sera confirmée après ce clic.  
> Si vous n’êtes pas à l’origine de cette demande, ignorez cet e-mail.

@endcomponent
