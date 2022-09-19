<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->club_id === null) {
            $user->club_id = $user->clubs()->first()->id;
            $user->save();
        }

        $club = $user?->club;
        $locale = 'de';

        $data = [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profileUrl' => $user->profileUrl(),
                    'admin' => $user->admin,
                    'clubAdmin' => $user->hasAdminRights(),
                ] : null,
                'club' => $club ? [
                    'id' => $club->id,
                    'name' => $club->name,
                    'logoUrl' => $club->logoUrl(),
                    'showName' => $club->display !== 2,
                    'showLogo' => $club->display < 3,
                    'blsv' => $club->blsv_member,
                    'useItems' => $club->use_items,
                ] : null,
            ],
            'locale' => $locale,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
        ];

        return array_merge(parent::share($request), $data);
    }
}
