<?php

namespace App\Filament\Widgets;

use Filament\Widgets\FilamentInfoWidget as BaseFilamentInfoWidget;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class FilamentInfoWidget extends BaseFilamentInfoWidget
{
    public static function canView(): bool
    {
        $user = Auth::user();
        return UserRole::canUserViewWidget($user, static::class);
    }

    public function mount(): void
    {
        if (! UserRole::canUserViewWidget(Auth::user(), static::class)) {
            abort(403);
        }
    }
}
