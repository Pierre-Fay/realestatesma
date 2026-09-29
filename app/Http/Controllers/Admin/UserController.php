<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserStatusRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display the list of users with their login status.
     */
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()->orderBy('name')->paginate(15);

        return view('admin.users.index', ['users' => $users]);
    }

    /**
     * Enable or disable a user's login access.
     */
    public function update(UpdateUserStatusRequest $request, User $user): RedirectResponse
    {
        $enabled = $request->boolean('is_enabled');

        $this->authorize($enabled ? 'enable' : 'disable', $user);

        $user->update(['is_enabled' => $enabled]);

        if (! $enabled) {
            $user->revokeSessions();
        }

        return Redirect::route('admin.users.index')
            ->with('status', $enabled ? 'user-enabled' : 'user-disabled');
    }
}
