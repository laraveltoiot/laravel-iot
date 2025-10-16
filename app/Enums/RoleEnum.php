<?php declare(strict_types=1);

namespace App\Enums;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case MANAGER = 'manager';

    public static function hasRole(string $roles): bool
    {
        $user = Auth::user();

        if (! $user instanceof User || $user->role === null) {
            return false;
        }

        return str($roles)
            ->explode('|')
            ->map(fn (string $role) => RoleEnum::from($role))
            ->contains(fn (RoleEnum $role) => $role === $user->role);
    }
}
