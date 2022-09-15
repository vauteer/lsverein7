<?php

namespace App\Http\Controllers;

use App\Models\MemberSection;
use App\Models\Member;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class MemberSectionController extends Controller
{
    protected function validationRules(): array
    {
        $rules = [
            'section_id' => 'exists:App\Models\Section,id',
            'from' => 'required|date',
            'to' => 'nullable|date|after:from',
            'memo' => 'nullable|string',
        ];

        return $rules;
    }

    public function editOptions(Member $member): array
    {
        return [
            'origin' => route('members.edit', $member->id),
            'sections' => Section::orderBy('name')->get(['id', 'name']),
            'memberId' => $member->id,
        ];
    }
    public function create(Request $request, Member $member): Response
    {
        return inertia('Members/MemberSection', $this->editOptions($member));
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules());

        $member->sections()->attach([currentClubId() => $attributes]);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Sparten-Mitgliedschaft hinzugefügt');
    }

    public function edit(Request $request, Member $member, memberSection $memberSection):Response
    {
        return inertia('Members/MemberSection', array_merge($this->editOptions($member), [
            'memberSection' => $memberSection->getAttributes(),
        ]));
    }

    public function update(Request $request, Member $member, memberSection $memberSection): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules());

        $memberSection->update($attributes);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Sparten-Mitgliedschaft geändert');
    }

    public function destroy(Request $request, Member $member, memberSection $memberSection): RedirectResponse
    {
        $memberSection->delete();

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Sparten-Mitgliedschaft gelöscht');
    }

}
