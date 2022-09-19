<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\UserNotification;
use App\Notifications\NewUser;
use App\Rules\UniqueUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Response;

class UserController extends Controller
{
    protected const URL_KEY = 'lastUsersUrl';

    public function validationRules($id): array
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

    private function accountRules($id): array
    {
        return [
            'name' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id)
            ],
            'profile_image' => 'nullable|string|max:100',
        ];
    }

    private function passwordRules(): array
    {
        return [
            'current_password' => ['nullable', 'string', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'confirmed', Password::min(8)],
        ];
    }

    public function index(Request $request): Response
    {
        $request->session()->put(self::URL_KEY, url()->full());

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

    public function create(Request $request): Response
    {
        return inertia('Users/Edit', $this->editOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));
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

    public function edit(Request $request, User $user): Response
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
        $attributes = $request->validate($this->validationRules($user->id));
        $user->update($attributes);
        $user->clubs()->updateExistingPivot(currentClubId(), [
            'role' => $request->input('role'),
        ]);

        return redirect(session(self::URL_KEY))
            ->with('success', "{$user->name} wurde geändert.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->clubs()->count() >= 2) {
            $user->clubs()->detach(currentClubId());
            $user->club_id = $user->clubs()->first()->id;
            $user->save();
        }
        else {
            $user->delete();
        }

        return redirect(session(self::URL_KEY))
            ->with('success', 'Benutzer wurde gelöscht.');;
    }

    public function editAccount(Request $request): Response
    {
        $user = auth()->user();

        return inertia('Users/Account', [
            'origin' => url()->previous(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->profile_image,
            ],
        ]);
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $attributes = $request->validate($this->accountRules($user->id));
        $passwordAttributes = $request->validate($this->passwordRules());

        if ($passwordAttributes['password'] !== null) {
            //Log::info($passwordAttributes['password']);
            $attributes = array_merge($attributes, [
                'password' => Hash::make($passwordAttributes['password']),
            ]);
        }

        $user->update($attributes);

        User::removeOrphanProfileImages();

        return redirect()->route('members')
            ->with('success', "Das Konto wurde geändert.");
    }


    public function loginAs(Request $request, User $user): RedirectResponse
    {
        auth()->login($user);

        return redirect()->route('members');
    }
}
