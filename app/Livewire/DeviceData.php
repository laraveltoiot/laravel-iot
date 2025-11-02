<?php declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class DeviceData extends Component
{
    public ?string $value = null;

    public ?string $timestamp = null;

    public function mount(): void
    {
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $latestValue = Cache::get('device:latest:value');
        $latestTs = Cache::get('device:latest:ts');

        $this->value = is_scalar($latestValue) || $latestValue === null
            ? ($latestValue !== null ? (string) $latestValue : null)
            : json_encode($latestValue);

        $this->timestamp = $latestTs !== null ? (string) $latestTs : null;
    }

    #[Layout('components.layouts.auth.simple')]
    public function render(): Factory|View|\Illuminate\View\View
    {
        $this->refreshData();

        return view('livewire.device-data');
    }
}
