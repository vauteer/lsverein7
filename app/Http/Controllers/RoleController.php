<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class RoleController extends Controller
{
    public function index(Request $request):Response
    {
        $this->setLastUrl();

        return inertia('Roles/Index', [
            'roles' => RoleResource::collection(Role::query()
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(15)
                ->withQueryString()
            ),

            'options' => $request->only(['search']),
            'canCreate' => auth()->user()->can('create', Role::class),
        ]);
    }

    public function editOptions(): array
    {
        return [
            'origin' => $this->getLastUrl(),
        ];
    }

    public function create(): Response
    {
        return inertia('Roles/Edit', $this->editOptions());
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $attributes = $request->validated();

        Role::create(array_merge($attributes, [
            'club_id' => currentClubId(),
        ]));

        return redirect($this->getLastUrl())
            ->with('success', 'Funktion hinzugefügt');
    }

    public function edit(Role $role):Response
    {
        return inertia('Roles/Edit', array_merge($this->editOptions(), [
            'role' => $role->getAttributes(),
            'deletable' => !$role->isUsed(),
        ]));
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $attributes = $request->validated();

        $role->update($attributes);

        return redirect($this->getLastUrl())
            ->with('success', 'Funktion geändert');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect($this->getLastUrl())
            ->with('success', 'Funktion gelöscht');
    }

}
