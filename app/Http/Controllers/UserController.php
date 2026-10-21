<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\CodeListItem;
use App\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->with('roleCodelist:id,label')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role_codelist', 'email_verified_at', 'created_at'])
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_codelist' => $user->role_codelist,
                'role_label' => $user->roleCodelist?->label,
                'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
            ]);

        return Inertia::render('users/index', [
            'users' => $users,
            'roles' => $this->roleOptions(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = User::query()->create([
            ...$validated,
            'password' => Str::password(64),
        ]);

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status !== PasswordBroker::RESET_LINK_SENT) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __($status),
            ]);

            return to_route('users.index');
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User created. A password setup link has been sent by email.'),
        ]);

        return to_route('users.index');
    }

    public function edit(Request $request, User $user): Response
    {
        $this->authorize('update', $user);

        return Inertia::render('users/edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_codelist' => $user->role_codelist,
            ],
            'roles' => $this->roleOptions($user->role_codelist),
            'isSelf' => $request->user()->is($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User updated.'),
        ]);

        return to_route('users.index');
    }

    /**
     * @return array<int, array{id: int, label: string}>
     */
    private function roleOptions(?int $selectedRoleId = null): array
    {
        return CodeListItem::query()
            ->whereHas('codeList', fn ($query) => $query->where('key', 'role_codelist'))
            ->where(function ($query) use ($selectedRoleId): void {
                $query->where('active', true);

                if ($selectedRoleId !== null) {
                    $query->orWhereKey($selectedRoleId);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get(['id', 'label'])
            ->map(fn (CodeListItem $role): array => [
                'id' => $role->id,
                'label' => $role->label,
            ])
            ->all();
    }
}
