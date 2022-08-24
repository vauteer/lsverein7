<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberRole;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class MemberRoleController extends Controller
{
    protected function validationRules(): array
    {
        $rules = [
            'role_id' => 'exists:App\Models\Role,id',
            'from' => 'required|date',
            'to' => 'nullable|date|after:from',
            'memo' => 'nullable|string',
        ];

        return $rules;
    }

    public function create(Request $request, Member $member): Response
    {
        return inertia('Members/MemberRole')
            ->with('roles', Role::get(['id', 'name'])->mapWithKeys(fn ($item) => [$item->id => $item->name]))
            ->with('memberId', $member->id);
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules());

        $member->roles()->attach([auth()->user()->club_id => $attributes]);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Funktion hinzugefügt');
    }

    public function edit(Request $request, Member $member, memberRole $memberRole):Response
    {
        return inertia('Members/MemberRole', [
            'memberRole' => $memberRole->getAttributes(),
        ])
            ->with('roles', Role::get(['id', 'name'])->mapWithKeys(fn ($item) => [$item->id => $item->name]))
            ->with('memberId', $member->id);
    }

    public function update(Request $request, Member $member, memberRole $memberRole): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules());

        $memberRole->update($attributes);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Funktion geändert');
    }

    public function destroy(Request $request, Member $member, memberRole $memberRole): RedirectResponse
    {
        $memberRole->delete();

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Funktion gelöscht');
    }
}