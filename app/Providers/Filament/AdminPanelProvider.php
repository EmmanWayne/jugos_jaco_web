<?php

namespace App\Providers\Filament;

use App\Helpers\AppHelper;
use App\Models\SystemSetting;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use App\Filament\Pages\Locations\ClientLocations;
use App\Filament\Resources\ClientResource;
use App\Http\Resources\ClientResource as ResourcesClientResource;
use Filament\Enums\ThemeMode as EnumsThemeMode;
use Filament\Navigation\UserMenuItem;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Support\Enums\ThemeMode;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = SystemSetting::getSettings();
        $logoUrl = $settings?->logo_url ?? asset('images/logo.png');

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->defaultThemeMode(EnumsThemeMode::Light)
            ->darkMode(false)
            ->brandName(fn() => AppHelper::getAppName())
            ->brandLogo($logoUrl)
            ->brandLogoHeight('3.5rem')
            ->favicon($logoUrl)
            ->topNavigation()
            ->colors($settings ? ['primary' => $settings->getThemeColors()] : [
                'primary' => [
                    50 => '238, 242, 255',
                    100 => '224, 231, 255',
                    200 => '199, 210, 254',
                    300 => '165, 180, 252',
                    400 => '129, 140, 248',
                    500 => '#001C4D',
                    600 => '#001233',
                    700 => '#001233',
                    800 => '#001233',
                    900 => '#001233',
                    950 => '#001233',
                ],
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    
            
    }
}
