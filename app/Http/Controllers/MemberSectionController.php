<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberSectionRequest;
use App\Models\MemberSection;
use App\Models\Member;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class MemberSectionController extends Controller
{
    public function editOptions(Member $member): array
    {
        return [
            'origin' => route('members.edit', $member->id),
            'sections' => Section::orderBy('name')->get(['id', 'name']),
            'memberId' => $member->id,
        ];
    }

    public function create(Member $member): Response
    {
        return inertia('Members/MemberSection', $this->editOptions($member));
    }

    public function store(MemberSectionRequest $request, Member $member): RedirectResponse
    {
        $attributes = $request->validated();

        $member->sections()->attach([currentClubId() => $attributes]);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Sparten-Mitgliedschaft hinzugefügt');
    }

    public function edit(Member $member, memberSection $memberSection):Response
    {
        return inertia('Members/MemberSection', array_merge($this->editOptions($member), [
            'memberSection' => $memberSection->getAttributes(),
        ]));
    }

    public function update(MemberSectionRequest $request, Member $member, memberSection $memberSection): RedirectResponse
    {
        $attributes = $request->validated();

        $memberSection->update($attributes);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Sparten-Mitgliedschaft geändert');
    }

    public function destroy(Member $member, memberSection $memberSection): RedirectResponse
    {
        $memberSection->delete();

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Sparten-Mitgliedschaft gelöscht');
    }

}
