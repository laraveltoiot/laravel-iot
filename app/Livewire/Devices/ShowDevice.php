<?php declare(strict_types=1);

namespace App\Livewire\Devices;

use App\Models\Device;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class ShowDevice extends Component
{
    public Device $device;

    public function mount(Device $device): void
    {
        $this->device = $device->load('user');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.devices.show-device');
    }
}
