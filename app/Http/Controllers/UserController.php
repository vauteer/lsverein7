<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\UserNotification;
use App\Rules\UniqueUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Response;

class UserController extends Controller
{
    protected const URL_KEY = 'lastUsersUrl';

    public function rules($id): array
    {
        return [
            'name' => 'required|string',
            'email' => [
                'required',
                'email',
                new UniqueUser($id)
            ],
        ];
    }

    public function index(Request $request): Response
    {
        session([self::URL_KEY => url()->full()]);

        return inertia('Users/Index', [
            'users' => UserResource::collection(User::hasClub()
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
            ),

            'options' => $request->only(['search']),

            'canCreate' => auth()->user()->hasAdminRights(),
        ]);
    }

    private function editOptions(): array
    {
        return [
            'origin' => session(self::URL_KEY),
            'roles' => optionsFromArray(User::availableRoles(), false),
        ];
    }

    public function create(): Response
    {
        return inertia('Users/Edit', $this->editOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->rules(-1));
        $role = $request->validate(['role' => 'required|int'])['role'];

        $user = User::where('email', $attributes['email'])->first();
        if ($user)
        {
            $user->clubs()->attach(currentClubId(), [
                'role' => $role,
            ]);
        }
        else {
            $password = Str::random(8);
            Log::info("Created User {$attributes['name']} with Password {$password}");

            $user = User::create(array_merge($attributes, [
                'password' => Hash::make($password),
                'club_id' => currentClubId(),
                'created_by' => $request->user()->id,
            ]));

            $user->clubs()->attach(currentClubId(), [
                'role' => $role,
            ]);

            $user->notify(new UserNotification("Für Sie wurde ein Zugang erstellt.", "Ihr Passwort lautet: {$password}"));

        }

        return redirect(session(self::URL_KEY))
            ->with('success', "{$user->name} wurde hinzugefügt.");
    }

    public function edit(User $user): Response
    {
        return inertia('Users/Edit', array_merge($this->editOptions(), [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->clubRole(),
            ],
            'deletable' => auth()->user()->hasAdminRights() && auth()->id() !== $user->id,
        ]));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $attributes = $request->validate($this->rules($user->id));
        $user->update($attributes);
        $user->clubs()->updateExistingPivot(currentClubId(), [
            'role' => $request->input('role'),
        ]);

        return redirect(session(self::URL_KEY))
            ->with('success', "{$user->name} wurde geändert.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->clubs()->count() >= 2) {
            $user->clubs()->detach(currentClubId());
            $user->update(['club_id' => $user->clubs()->first()->id]);
        }
        else {
            $user->delete();
        }

        return redirect(session(self::URL_KEY))
            ->with('success', 'Benutzer wurde gelöscht.');
    }

    public function loginAs(User $user): RedirectResponse
    {
        auth()->login($user);

        return redirect()->route('members');
    }

}
