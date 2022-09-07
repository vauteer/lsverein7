<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Response;

class RoleController extends Controller
{
    protected const URL_KEY = 'lastRolesUrl';

    protected function validationRules($id): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                Rule::unique('roles')
                    ->where(fn ($query) => $query->where('club_id', currentClubId()))
                    ->ignore($id),
            ],
            'global' => 'boolean',
        ];

        return $rules;
    }


    public function index(Request $request):Response
    {
        $request->session()->put(self::URL_KEY, url()->full());
        return inertia('Roles/Index', [
            'roles' => RoleResource::collection(Role::query()
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(15)
                ->withQueryString()
            ),

            'filters' => $request->only(['search']),
            'canCreate' => auth()->user()->can('create', Role::class),
        ]);
    }

    public function editOptions(): array
    {
        return [
            'origin' => session(self::URL_KEY),
        ];
    }

    public function create(Request $request): Response
    {
        return inertia('Roles/Edit', $this->editOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));

        Role::create(array_merge($attributes, [
            'club_id' => currentClubId(),
        ]));

        return redirect(session(self::URL_KEY))
            ->with('success', 'Funktion hinzugefügt');
    }

    public function edit(Request $request, Role $role):Response
    {
        return inertia('Roles/Edit', array_merge($this->editOptions(), [
            'role' => $role->getAttributes(),
            'deletable' => !$role->isInUse(),
        ]));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($role->id));

        $role->update($attributes);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Funktion geändert');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        $role->delete();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Funktion gelöscht');
    }

}
