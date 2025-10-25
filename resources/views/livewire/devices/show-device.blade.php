<div>
    <flux:heading size="xl" class="mb-2">
        Device: {{ $device->name }}
    </flux:heading>

    <div class="mb-4 flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" href="{{ route('devices-index') }}" wire:navigate>
            Back to devices
        </flux:button>

        <flux:spacer />

        <flux:badge variant="{{ $device->is_active ? 'success' : 'neutral' }}">
            {{ $device->is_active ? 'Active' : 'Inactive' }}
        </flux:badge>
    </div>

    <flux:card>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <flux:text class="text-sm text-gray-500">Owner</flux:text>
                <div class="font-medium">{{ $device->user->name ?? '—' }}</div>
            </div>
            <div>
                <flux:text class="text-sm text-gray-500">Created</flux:text>
                <div class="font-medium">{{ optional($device->created_at)->format('Y-m-d H:i') }}</div>
            </div>
            <div>
                <flux:text class="text-sm text-gray-500">Firmware</flux:text>
                <div class="font-medium">{{ $device->firmware_version ?? '—' }}</div>
            </div>
            <div>
                <flux:text class="text-sm text-gray-500">Last seen</flux:text>
                <div class="font-medium">{{ optional($device->last_seen_at)->format('Y-m-d H:i') ?? '—' }}</div>
            </div>
            <div>
                <flux:text class="text-sm text-gray-500">IP</flux:text>
                <div class="font-medium">{{ $device->ip_last ?? '—' }}</div>
            </div>
            <div>
                <flux:text class="text-sm text-gray-500">MAC</flux:text>
                <div class="font-medium">{{ $device->mac_address ?? '—' }}</div>
            </div>
            <div class="md:col-span-2">
                <flux:text class="text-sm text-gray-500">Wi‑Fi</flux:text>
                <div class="font-medium">
                    SSID: {{ $device->wifi_ssid }}
                    <span class="mx-2">•</span>
                    Password: {{ $device->wifi_password }}
                </div>
            </div>
        </div>
    </flux:card>
</div>
