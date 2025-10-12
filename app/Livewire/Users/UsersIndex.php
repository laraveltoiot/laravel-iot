<?php declare(strict_types=1);

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

final class UsersIndex extends Component
{
    use WithPagination;

    public string $sortBy = 'name';
    public string $sortDirection = 'asc';
    public string $search = '';

    // ----------- Edit state -----------
    public int $editingId;

    #[Validate('required|string|min:2|max:255', onUpdate: false)]
    public string $editName = '';

    #[Validate('required|email|max:255', onUpdate: false)]
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
        $user = User::findOrFail($userId);
        $this->editingId = $user->id;
        $this->editName = (string) $user->name;
        $this->editEmail = (string) $user->email;
    }

    public function saveEdit(): void
    {
        $this->validate();

        $user = User::find($this->editingId);

        $user->update([
            'name'  => $this->editName,
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
