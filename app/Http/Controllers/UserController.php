<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;

final class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        $count = $users->count();
        return view('user.index', [
            'users' => $users,
            'count' => $count,
        ]);
    }
}
