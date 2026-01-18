<?php declare(strict_types=1);

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class ShowUser extends Component
{
    #[Layout('layouts.app')]
    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.users.show-user');
    }
}
