<?php declare(strict_types=1);

use App\Livewire\Devices\DevicesIndex;
use App\Livewire\Devices\ShowDevice;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Users\ShowUser;
use App\Livewire\Users\UsersIndex;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// only for admins
Route::middleware('role:admin')->prefix('admin')->name('admin')->group(function () {
    Route::get('/users', UsersIndex::class)->name('users.index');
    Route::get('/users/{user}', ShowUser::class)->name('users.show');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::get('/devices', DevicesIndex::class)->name('devices-index');
    Route::get('/devices/{device}', ShowDevice::class)->name('devices.show');
});

require __DIR__.'/auth.php';
