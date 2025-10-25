<?php declare(strict_types=1);

namespace App\Livewire\Devices;

use App\Models\Device;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class DevicesIndex extends Component
{
    use WithPagination;

    public string $sortBy = 'name';

    public string $sortDirection = 'asc';

    public string $search = '';

    // Create state
    public bool $creating = false;

    public string $createName = '';

    public ?string $createWifiSsid = null;

    public ?string $createWifiPassword = null;

    public bool $createIsActive = true;

    // Edit state
    public int $editingId;

    public string $editName = '';

    public ?string $editWifiSsid = null;

    public ?string $editWifiPassword = null;

    public bool $editIsActive = true;

    // Regenerate state
    public int $regeneratingId;

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.devices.devices-index');
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function devices(): LengthAwarePaginator
    {
        return Device::query()
            ->with('user')
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($q): void {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('firmware_version', 'like', '%'.$this->search.'%')
                        ->orWhere('ip_last', 'like', '%'.$this->search.'%')
                        ->orWhere('mac_address', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(50);
    }

    // Create
    public function openCreate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->reset(['createName', 'createWifiSsid', 'createWifiPassword', 'createIsActive']);
        $this->createIsActive = true;
        $this->creating = true;
    }

    public function createDevice(): void
    {
        $this->validate([
            'createName' => 'required|string|min:2|max:255',
            'createWifiSsid' => 'nullable|string|max:255',
            'createWifiPassword' => 'nullable|string|max:10000',
            'createIsActive' => 'boolean',
        ]);

        $user = Auth::user();
        abort_unless($user !== null, 403);

        Device::create([
            'user_id' => $user->id,
            'name' => $this->createName,
            'secret_key' => (string) Str::uuid(),
            'wifi_ssid' => $this->createWifiSsid,
            'wifi_password' => $this->createWifiPassword,
            'is_active' => $this->createIsActive,
        ]);

        $this->reset(['creating', 'createName', 'createWifiSsid', 'createWifiPassword', 'createIsActive']);
        $this->resetPage();

        Flux::toast(
            text: 'Device created successfully.',
            heading: 'Device created',
            variant: 'success'
        );

        Flux::modals()->close('create-device');
    }

    // Edit
    public function openEdit(int $deviceId): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $device = Device::findOrFail($deviceId);
        $this->editingId = $device->id;
        $this->editName = (string) $device->name;
        $this->editWifiSsid = $device->wifi_ssid;
        $this->editWifiPassword = $device->wifi_password;
        $this->editIsActive = (bool) $device->is_active;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editName' => 'required|string|min:2|max:255',
            'editWifiSsid' => 'nullable|string|max:255',
            'editWifiPassword' => 'nullable|string|max:10000',
            'editIsActive' => 'boolean',
        ]);

        $device = Device::findOrFail($this->editingId);

        $device->update([
            'name' => $this->editName,
            'wifi_ssid' => $this->editWifiSsid,
            'wifi_password' => $this->editWifiPassword,
            'is_active' => $this->editIsActive,
        ]);

        $this->reset(['editingId', 'editName', 'editWifiSsid', 'editWifiPassword', 'editIsActive']);
        $this->resetPage();

        Flux::toast(
            text: 'Device details saved successfully.',
            heading: 'Device updated',
            variant: 'success'
        );
        Flux::modals()->close('edit-device');
    }

    // Delete
    public function delete(int $deviceId): void
    {
        $device = Device::findOrFail($deviceId);
        $name = (string) $device->name;
        $device->delete();

        $this->resetPage();

        Flux::toast(
            text: "“{$name}” was removed successfully.",
            heading: 'Device deleted',
            variant: 'success'
        );
    }

    // Regenerate secret
    public function openRegenerate(int $deviceId): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->regeneratingId = $deviceId;
    }

    public function regenerateSecret(): void
    {
        $device = Device::findOrFail($this->regeneratingId);
        $device->secret_key = (string) Str::uuid();
        $device->save();

        $this->reset(['regeneratingId']);

        Flux::toast(
            text: 'A new secret was generated for this device.',
            heading: 'Secret regenerated',
            variant: 'success'
        );

        Flux::modals()->close('regenerate-secret');
    }
}
