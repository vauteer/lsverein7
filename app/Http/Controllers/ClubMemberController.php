<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClubMemberRequest;
use App\Models\ClubMember;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class ClubMemberController extends Controller
{
    private function editOptions(Member $member): array
    {
        return [
            'origin' => route('members.edit', $member->id),
            'memberId' => $member->id,
        ];
    }

    public function create(Member $member): Response
    {
        return inertia('Members/ClubMember', $this->editOptions($member));
    }

    public function store(ClubMemberRequest $request, Member $member): RedirectResponse
    {
        $attributes = $request->validated();

        $member->memberships()->attach([currentClubId() => $attributes]);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Mitgliedschaft hinzugefügt');
    }

    public function edit(Member $member, ClubMember $clubMember):Response
    {
        return inertia('Members/ClubMember', array_merge($this->editOptions($member), [
                'clubMember' => $clubMember->getAttributes(),
            ]));
    }

    public function update(ClubMemberRequest $request, Member $member, ClubMember $clubMember): RedirectResponse
    {
        $attributes = $request->validated();

        $clubMember->update($attributes);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Mitgliedschaft geändert');
    }

    public function destroy(Member $member, ClubMember $clubMember): RedirectResponse
    {
        $clubMember->delete();

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Mitgliedschaft gelöscht');
    }

}
