<div class="space-y-6">
    <flux:heading size="xl">User Details</flux:heading>

    <div class="grid gap-4">
        <div>
            <span class="text-gray-500 dark:text-gray-400">Name:</span>
            <span class="font-medium">{{ $this->user->name }}</span>
        </div>
        <div>
            <span class="text-gray-500 dark:text-gray-400">Email:</span>
            <span class="font-medium">{{ $this->user->email }}</span>
        </div>
        <div>
            <span class="text-gray-500 dark:text-gray-400">Created:</span>
            <span class="font-medium">{{ optional($this->user->created_at)->format('Y-m-d H:i') }}</span>
        </div>
    </div>

    <div class="pt-2">
        <a href="{{ route('users.index') }}">
            <flux:button icon="arrow-left">Back to Users</flux:button>
        </a>
    </div>
</div>
