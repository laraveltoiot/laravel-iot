<?php declare(strict_types=1);

namespace App\Livewire;

use App\Models\IotTask;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class DeviceData extends Component
{
    #[Layout('components.layouts.auth.simple')]
    public function render(): Factory|View|\Illuminate\View\View
    {
        $tasks = IotTask::query()
            ->orderByDesc('id')
            ->get(['id', 'command', 'payload', 'status', 'created_at']);

        return view('livewire.device-data', [
            'tasks' => $tasks,
        ]);
    }
}
