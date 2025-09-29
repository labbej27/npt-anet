<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// 2FA / Profil
use Jeffgreco13\FilamentBreezy\BreezyCore;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Administration – NPT Anet')
            ->colors(['primary' => Color::Amber])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([ Pages\Dashboard::class ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([ Widgets\AccountWidget::class, Widgets\FilamentInfoWidget::class ])

            // ⬇️ Lien dans la NAV GAUCHE
            ->navigationItems([
                NavigationItem::make('Voir le site')
                    ->icon('heroicon-o-globe-alt')
                    ->url(url('/'))
                    // selon ta version, une des deux peut exister ; si erreur, commente-les :
                    // ->openUrlInNewTab()
                    // ->isExternalUrl(true)
                    ->sort(9999), // tout en bas
            ])

            ->middleware([
                \App\Http\Middleware\SetLocale::class, // ⬅️ on le crée juste après
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([ Authenticate::class ])

            ->plugins([
                BreezyCore::make()
                    ->myProfile(shouldRegisterUserMenu: true)
                    ->enableTwoFactorAuthentication(force: true),
            ]);
    }
}
