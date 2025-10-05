<div>
    <flux:heading size="xl" class="mb-2">Users ({{ $this->users->total() }})</flux:heading>

    <div class="mb-4">
        <flux:input
            icon="magnifying-glass"
            placeholder="Search user"
            wire:model.live.debounce.300ms="search"
        />
    </div>
    <flux:table :paginate="$this->users">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection" wire:click="sort('email')">Email</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Created</flux:table.column>
            <flux:table.column class="w-0"></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell class="font-medium">{{ $user->name }}</flux:table.cell>
                    <flux:table.cell class="text-gray-600 dark:text-gray-300">{{ $user->email }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ optional($user->created_at)->format('Y-m-d H:i') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                            <flux:menu>
                                <flux:menu.item href="{{ route('users.show', $user) }}">Show</flux:menu.item>
                                <flux:menu.item>Edit</flux:menu.item>
                                <flux:menu.item>Delete</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4" class="text-center text-gray-500 dark:text-gray-400">No users found.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
