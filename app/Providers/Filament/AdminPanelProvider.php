<?php

namespace App\Providers\Filament;

use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')                 // /admin
            ->brandName('EduShare Admin')
            ->homeUrl(fn () => url('/'))

            // look & feel
            ->colors([
                'primary' => Color::Indigo,
                'gray'    => Color::Zinc,
                'danger'  => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->darkMode(true)
            ->font('Inter')
            ->breadcrumbs(true)
            ->navigationGroups(['Content','Taxonomy','Moderation','System'])

            // ✅ USE THE WEB MIDDLEWARE GROUP (includes session + csrf)
            ->middleware(['web'])

            // ✅ FORCE FILAMENT TO USE THE WEB GUARD
            ->authGuard('web')

            // ✅ REGISTER LOGIN/PROFILE ROUTES
            ->login()
            ->profile()

            // auto-discover your resources/pages/widgets
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')

            // user menu shortcut
            ->userMenuItems([
                MenuItem::make()
                    ->label('View Site')
                    ->url(fn () => url('/'))
                    ->icon('heroicon-o-home'),
            ]);
    }
}
