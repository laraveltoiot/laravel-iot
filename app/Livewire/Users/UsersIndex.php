<?php declare(strict_types=1);

namespace App\Livewire\Users;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class UsersIndex extends Component
{
    use WithPagination;

    public string $sortBy = 'name';

    public string $sortDirection = 'asc';

    public string $search = '';

    // ----------- Edit state -----------
    public int $editingId;

    // ----------- Create state -----------
    public bool $creating = false;

    public string $createName = '';

    public string $createEmail = '';

    public string $editName = '';

    public string $editEmail = '';

    // ----------- Rendering -----------
    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.users.users-index');
    }

    // ----------- Sorting + Search -----------
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

    // ----------- Delete -----------
    public function delete(int $userId): void
    {
        $user = User::find($userId);
        $name = $user->name;
        $user->delete();

        $this->resetPage();

        Flux::toast(
            text: "“{$name}” was removed successfully.",
            heading: 'User deleted',
            variant: 'success'
        );
    }

    // ----------- Edit -----------
    public function openEdit(int $userId): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $user = User::findOrFail($userId);
        $this->editingId = $user->id;
        $this->editName = (string) $user->name;
        $this->editEmail = (string) $user->email;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editName' => 'required|string|min:2|max:255',
            'editEmail' => 'required|email|max:255|unique:users,email,'.$this->editingId,
        ]);

        $user = User::find($this->editingId);

        $user->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
        ]);

        // Reset state
        $this->reset(['editingId', 'editName', 'editEmail']);
        $this->resetPage();

        Flux::toast(
            text: 'User details saved successfully.',
            heading: 'Profile updated',
            variant: 'success'
        );
        Flux::modals()->close('edit-user');
    }

    // ----------- Create -----------
    public function openCreate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->reset(['createName', 'createEmail']);
        $this->creating = true;
    }

    public function createUser(): void
    {
        $this->validate([
            'createName' => 'required|string|min:2|max:255',
            'createEmail' => 'required|email|max:255|unique:users,email',
        ]);

        User::create([
            'name' => $this->createName,
            'email' => $this->createEmail,
            'password' => Hash::make(Str::random(25)),
        ]);

        $this->reset(['createName', 'createEmail', 'creating']);
        $this->resetPage();

        Flux::toast(
            text: 'User created successfully.',
            heading: 'User created',
            variant: 'success'
        );

        Flux::modals()->close('create-user');
        // TODO Send email invitation
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($q): void {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(50);
    }
}
