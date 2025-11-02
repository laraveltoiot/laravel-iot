<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Device Live Data</h1>

    <div class="grid gap-4" wire:poll.visible>
        <div class="p-4 rounded border bg-white/60 dark:bg-black/20">
            <div class="text-sm text-gray-600 dark:text-gray-300">Latest Value</div>
            <div class="text-3xl font-bold mt-1">
                {{ $value ?? '—' }}
            </div>
        </div>

        <div class="p-4 rounded border bg-white/60 dark:bg-black/20">
            <div class="text-sm text-gray-600 dark:text-gray-300">Last Update</div>
            <div class="text-lg mt-1">
                {{ $timestamp ?? '—' }}
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button class="px-4 py-2 rounded bg-blue-600 text-white" wire:click="refreshData" wire:loading.attr="disabled">
                <span wire:loading.remove>Refresh now</span>
                <span wire:loading>Refreshing…</span>
            </button>
        </div>
    </div>
</div>
