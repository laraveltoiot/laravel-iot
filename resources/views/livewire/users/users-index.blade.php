<div>
    <flux:heading size="xl" class="mb-2">
        Users ({{ $this->users->total() }})
    </flux:heading>

    <div class="mb-4 flex items-center gap-3">
        <flux:input
            icon="magnifying-glass"
            placeholder="Search user"
            wire:model.live.debounce.300ms="search"
            class="flex-1"
        />

        <flux:modal.trigger name="create-user">
            <flux:button variant="primary" icon="plus" wire:click="openCreate">
                New User
            </flux:button>
        </flux:modal.trigger>
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
                    <flux:table.cell class="whitespace-nowrap">
                        {{ optional($user->created_at)->format('Y-m-d H:i') }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                            <flux:menu>
                                <flux:menu.item href="{{ route('users.show', $user) }}">Show</flux:menu.item>

                                {{-- EDIT: trigger + populate form --}}
                                <flux:modal.trigger name="edit-user">
                                    <flux:menu.item
                                        wire:click.prevent="openEdit({{ $user->id }})"
                                    >
                                        Edit
                                    </flux:menu.item>
                                </flux:modal.trigger>

                                {{-- DELETE: trigger delete modal (ca înainte) --}}
                                <flux:modal.trigger name="delete-user-{{ $user->id }}">
                                    <flux:menu.item variant="danger">Delete</flux:menu.item>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>

                        {{-- Delete confirmation modal (per-row) --}}
                        <flux:modal name="delete-user-{{ $user->id }}" class="min-w-[22rem]">
                            <form wire:submit="delete({{ $user->id }})" class="space-y-6">
                                <div>
                                    <flux:heading size="lg">Delete user?</flux:heading>
                                    <flux:text class="mt-2">
                                        <p>You're about to delete <strong>{{ $user->name }}</strong> ({{ $user->email }}).</p>
                                        <p>This action cannot be reversed.</p>
                                    </flux:text>
                                </div>
                                <div class="flex gap-2">
                                    <flux:spacer />
                                    <flux:modal.close>
                                        <flux:button type="button" variant="ghost">Cancel</flux:button>
                                    </flux:modal.close>
                                    <flux:modal.close>
                                        <flux:button type="submit" variant="danger">Delete user</flux:button>
                                    </flux:modal.close>
                                </div>
                            </form>
                        </flux:modal>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4" class="text-center text-gray-500 dark:text-gray-400">
                        No users found.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="edit-user" class="md:w-96">
        <form wire:submit.prevent="saveEdit" class="space-y-6">
            <div>
                <flux:heading size="lg">Update profile</flux:heading>
                <flux:text class="mt-2">Make changes to the selected user.</flux:text>
            </div>
            <flux:input
                label="Name"
                placeholder="User name"
                wire:model.live.debounce.300ms="editName"
                :error="$errors->first('editName')"
            />
            <flux:input
                label="Email"
                type="email"
                placeholder="user@example.com"
                wire:model.live.debounce.300ms="editEmail"
                :error="$errors->first('editEmail')"
            />
            @if ($errors->any())
                <flux:callout variant="danger">
                    Please fix the highlighted fields.
                </flux:callout>
            @endif

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

    {{-- Create User Modal --}}
    <flux:modal name="create-user" class="md:w-96">
        <form wire:submit.prevent="createUser" class="space-y-6">
            <div>
                <flux:heading size="lg">Create user</flux:heading>
                <flux:text class="mt-2">Add a new user to your workspace.</flux:text>
            </div>

            <flux:input
                label="Name"
                placeholder="User name"
                wire:model.live.debounce.300ms="createName"
                :error="$errors->first('createName')"
            />

            <flux:input
                label="Email"
                type="email"
                placeholder="user@example.com"
                wire:model.live.debounce.300ms="createEmail"
                :error="$errors->first('createEmail')"
            />

            @if ($errors->any())
                <flux:callout variant="danger">
                    Please fix the highlighted fields.
                </flux:callout>
            @endif

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Create</span>
                    <span wire:loading>Creating...</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

</div>
