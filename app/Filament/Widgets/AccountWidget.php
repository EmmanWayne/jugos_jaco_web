<?php

namespace App\Filament\Widgets;

use Filament\Widgets\AccountWidget as BaseAccountWidget;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class AccountWidget extends BaseAccountWidget
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
