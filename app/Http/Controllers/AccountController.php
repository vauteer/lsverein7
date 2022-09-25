<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Response;

class AccountController extends Controller
{
    protected const URL_KEY = 'lastAccountUrl';

    private function passwordRules(): array
    {
        return [
            'current_password' => ['nullable', 'string', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'confirmed', Password::min(8)],
        ];
    }

    public function edit(): Response
    {
        $origin = url()->previous();
        session([self::URL_KEY => $origin]);

        $user = auth()->user();

        return inertia('Users/Account', [
            'origin' => $origin,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->profile_image,
            ],
        ]);
    }

    public function update(AccountRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $attributes = $request->validated();
        $passwordAttributes = $request->validate($this->passwordRules());

        if ($passwordAttributes['password'] !== null) {
            //Log::info($passwordAttributes['password']);
            $attributes = array_merge($attributes, [
                'password' => Hash::make($passwordAttributes['password']),
            ]);
        }

        $user->update($attributes);

        User::removeOrphanProfileImages();

        return redirect(session(self::URL_KEY))
            ->with('success', "Das Konto wurde geändert.");
    }

}
