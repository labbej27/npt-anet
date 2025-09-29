<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    CalendarController,
    ReservationController,
    ArticleController,
    PageController
};

/**
 * Accueil = Association (liste des articles)
 * /association redirige vers /
 */
Route::get('/', [ArticleController::class, 'index'])->name('home');
Route::get('/association', [ArticleController::class, 'index'])->name('association');

/** Agenda (liste + FullCalendar + API) */
Route::get('/agenda', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/agenda/calendrier', [CalendarController::class, 'full'])->name('calendar.full');
Route::get('/api/calendar-events', [CalendarController::class, 'events'])->name('calendar.events');

/** Réservations (double opt-in) */
Route::post('/sessions/{session}/reserver', [ReservationController::class, 'store'])->name('reservation.store');
Route::get('/confirmer/{token}', [ReservationController::class, 'confirm'])->name('reservation.confirm');
Route::get('/annuler/{token}', [ReservationController::class, 'cancel'])->name('reservation.cancel');

/** Articles (détail) */
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

/** Pages dynamiques éditables (via Filament) */
Route::get('/contact', fn () => app(PageController::class)->show('contact'))->name('contact');
Route::get('/mentions-legales', fn () => app(PageController::class)->show('mentions-legales'))->name('mentions');

/** Dashboard / profil (auth) */
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
