<?php declare(strict_types=1);

namespace App\Providers;

use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->defineBladeDirectives();
    }

    private function defineBladeDirectives(): void
    {
        Blade::if('role', function (string $role) {
            return RoleEnum::hasRole($role);
        });
    }
}
