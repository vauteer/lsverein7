<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRoleRequest;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class MemberRoleController extends Controller
{
    private function editOptions(Member $member): array
    {
        return [
            'origin' => route('members.edit', $member->id),
            'roles' => Role::orderBy('name')->get(['id', 'name']),
            'memberId' => $member->id,
        ];
    }
    public function create(Member $member): Response
    {
        return inertia('Members/MemberRole', $this->editOptions($member));
    }

    public function store(MemberRoleRequest $request, Member $member): RedirectResponse
    {
        $attributes = $request->validated();

        $member->roles()->attach([currentClubId() => $attributes]);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Funktion hinzugefügt');
    }

    public function edit(Member $member, memberRole $memberRole):Response
    {
        return inertia('Members/MemberRole', array_merge($this->editOptions($member), [
            'memberRole' => $memberRole->getAttributes(),
            'origin' => route('members.edit', $member->id),
        ]));
    }

    public function update(MemberRoleRequest $request, Member $member, memberRole $memberRole): RedirectResponse
    {
        $attributes = $request->validated();

        $memberRole->update($attributes);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Funktion geändert');
    }

    public function destroy(Member $member, memberRole $memberRole): RedirectResponse
    {
        $memberRole->delete();

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Funktion gelöscht');
    }
}
