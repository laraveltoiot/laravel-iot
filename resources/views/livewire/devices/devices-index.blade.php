<div>
    <flux:heading size="xl" class="mb-2">
        Devices ({{ $this->devices->total() }})
    </flux:heading>

    <div class="mb-4 flex items-center gap-3">
        <flux:input
            icon="magnifying-glass"
            placeholder="Search devices"
            wire:model.live.debounce.300ms="search"
            class="flex-1"
            autocomplete="off"
        />

        <flux:modal.trigger name="create-device">
            <flux:button variant="primary" icon="plus" wire:click="openCreate">
                New Device
            </flux:button>
        </flux:modal.trigger>
    </div>

    <flux:table :paginate="$this->devices">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
            <flux:table.column>Owner</flux:table.column>
            <flux:table.column>Active</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'last_seen_at'" :direction="$sortDirection" wire:click="sort('last_seen_at')">Last seen</flux:table.column>
            <flux:table.column class="w-0"></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->devices as $device)
                <flux:table.row :key="$device->id">
                    <flux:table.cell class="font-medium">{{ $device->name }}</flux:table.cell>
                    <flux:table.cell class="text-gray-600 dark:text-gray-300">{{ $device->user->name ?? '—' }}</flux:table.cell>
                    <flux:table.cell>
                        @if($device->is_active)
                            <flux:badge variant="success">Active</flux:badge>
                        @else
                            <flux:badge variant="neutral">Inactive</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        {{ optional($device->last_seen_at)->format('Y-m-d H:i') ?? '—' }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                            <flux:menu>
                                <flux:modal.trigger name="edit-device">
                                    <flux:menu.item href="{{ route('devices.show', $device) }}" wire:navigate>Show</flux:menu.item>
                                    <flux:menu.item wire:click.prevent="openEdit({{ $device->id }})">Edit</flux:menu.item>
                                </flux:modal.trigger>

                                <flux:modal.trigger name="regenerate-secret">
                                    <flux:menu.item wire:click.prevent="openRegenerate({{ $device->id }})">Regenerate secret…</flux:menu.item>
                                </flux:modal.trigger>

                                <flux:modal.trigger name="delete-device-{{ $device->id }}">
                                    <flux:menu.item variant="danger">Delete</flux:menu.item>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>

                        {{-- Delete confirmation modal (per-row) --}}
                        <flux:modal name="delete-device-{{ $device->id }}" class="min-w-[22rem]">
                            <form wire:submit="delete({{ $device->id }})" class="space-y-6">
                                <div>
                                    <flux:heading size="lg">Delete device?</flux:heading>
                                    <flux:text class="mt-2">
                                        <p>You're about to delete <strong>{{ $device->name }}</strong>. This action cannot be reversed.</p>
                                    </flux:text>
                                </div>
                                <div class="flex gap-2">
                                    <flux:spacer />
                                    <flux:modal.close>
                                        <flux:button type="button" variant="ghost">Cancel</flux:button>
                                    </flux:modal.close>
                                    <flux:modal.close>
                                        <flux:button type="submit" variant="danger">Delete device</flux:button>
                                    </flux:modal.close>
                                </div>
                            </form>
                        </flux:modal>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center text-gray-500 dark:text-gray-400">
                        No devices found.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Edit Device Modal --}}
    <flux:modal name="edit-device" class="md:w-96">
        <form wire:submit.prevent="saveEdit" class="space-y-6">
            <div>
                <flux:heading size="lg">Update device</flux:heading>
                <flux:text class="mt-2">Make changes to the selected device. The secret cannot be edited; use Regenerate instead.</flux:text>
            </div>
            <flux:input
                label="Name"
                placeholder="Device name"
                wire:model.live.debounce.300ms="editName"
                :error="$errors->first('editName')"
                autocomplete="off"
            />
            <flux:input
                label="Wi‑Fi SSID"
                placeholder="MyWiFi"
                wire:model.live.debounce.300ms="editWifiSsid"
                :error="$errors->first('editWifiSsid')"
                autocomplete="off"
            />
            <flux:input
                label="Wi‑Fi Password"
                type="password"
                placeholder="••••••••"
                wire:model.live.debounce.300ms="editWifiPassword"
                :error="$errors->first('editWifiPassword')"
                autocomplete="off"
            />
            <div class="flex items-center justify-between">
                <flux:switch wire:model.live="editIsActive" />
                <span class="text-sm text-gray-600 dark:text-gray-300">{{ $editIsActive ? 'Active' : 'Inactive' }}</span>
            </div>

            <div class="transition-all duration-200 {{ $errors->any() ? '' : 'invisible' }}">
                <flux:callout variant="danger">
                    Please fix the highlighted fields.
                </flux:callout>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">
                    Save changes
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Create Device Modal --}}
    <flux:modal name="create-device" class="md:w-96">
        <form wire:submit.prevent="createDevice" class="space-y-6">
            <div>
                <flux:heading size="lg">Create device</flux:heading>
                <flux:text class="mt-2">Add a new device. It will be assigned to your account and a secret will be generated automatically.</flux:text>
            </div>

            <flux:input
                label="Name"
                placeholder="Device name"
                wire:model.live.debounce.300ms="createName"
                :error="$errors->first('createName')"
                autocomplete="off"
            />

            <flux:input
                label="Wi‑Fi SSID"
                placeholder="MyWiFi"
                wire:model.live.debounce.300ms="createWifiSsid"
                :error="$errors->first('createWifiSsid')"
            />

            <flux:input
                label="Wi‑Fi Password"
                type="password"
                placeholder="••••••••"
                wire:model.live.debounce.300ms="createWifiPassword"
                :error="$errors->first('createWifiPassword')"
            />

            <div class="flex items-center justify-between">
                <flux:switch wire:model.live="createIsActive" />
                <span class="text-sm text-gray-600 dark:text-gray-300">{{ $createIsActive ? 'Active' : 'Inactive' }}</span>
            </div>

            <div class="transition-all duration-200 {{ $errors->any() ? '' : 'invisible' }}">
                <flux:callout variant="danger">
                    Please fix the highlighted fields.
                </flux:callout>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="createDevice">
                    <span wire:loading.remove wire:target="createDevice">Create</span>
                    <span wire:loading wire:target="createDevice">Creating...</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Regenerate Secret Modal --}}
    <flux:modal name="regenerate-secret" class="md:w-[28rem]">
        <form wire:submit.prevent="regenerateSecret" class="space-y-6">
            <div>
                <flux:heading size="lg">Regenerate secret?</flux:heading>
                <flux:text class="mt-2">
                    <p>This will generate a new secret for the device. Existing clients using the old secret will stop working.</p>
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger" icon="key">
                    Regenerate secret
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
