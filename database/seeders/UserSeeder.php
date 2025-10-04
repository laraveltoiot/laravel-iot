<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bogdan = User::create([
            'email' => 'bogdygewald@yahoo.de',
            'name' => 'Bogdan Gewald',
            'password' => Hash::make('supertest'),
        ]);

        User::factory(100)->create();

    }
}
