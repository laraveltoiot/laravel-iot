<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\IotTask;
use Illuminate\Database\Seeder;

final class IotTasksLedSeeder extends Seeder
{
    public function run(): void
    {
        // Task 1: tine LED-ul aprins (pending)
        IotTask::query()->create([
            'command' => 'set_led',
            'payload' => [
                'pin' => 'onboard',
                'state' => 'on', // on | off | toggle
            ],
            'status' => 'pending',
        ]);

        // Task 2: LED clipeste (completed)
        IotTask::query()->create([
            'command' => 'set_led',
            'payload' => [
                'mode' => 'blink', // blink | pulse
                'interval_ms' => 300,
                'duration_ms' => 3000,
            ],
            'status' => 'completed',
        ]);
    }
}
