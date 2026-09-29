<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAgentRequest;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AgentController extends Controller
{
    /**
     * Display the list of agents.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Agent::class);

        $agents = Agent::query()->with('user')->orderBy('name')->paginate(15);

        return view('admin.agents.index', ['agents' => $agents]);
    }

    /**
     * Show the form to create a new agent account.
     */
    public function create(): View
    {
        $this->authorize('create', Agent::class);

        return view('admin.agents.create');
    }

    /**
     * Atomically create the agent's login (User) and public profile (Agent).
     */
    public function store(StoreAgentRequest $request): RedirectResponse
    {
        $this->authorize('create', Agent::class);

        $data = $request->validated();

        DB::transaction(function () use ($request, $data): void {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['login_email'],
                'password' => $data['password'],
                'role' => UserRole::AGENT,
                'is_enabled' => true,
            ]);
            $user->email_verified_at = now();
            $user->save();

            $user->agent()->create([
                'name' => $data['name'],
                'slug' => Agent::uniqueSlugFor($data['name']),
                'email' => $data['public_email'],
                'phone' => $data['phone'] ?? null,
                'bio' => $data['bio'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);
        });

        return Redirect::route('admin.agents.index')->with('status', 'agent-created');
    }
}
