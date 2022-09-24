<?php

namespace App\Http\Controllers;

use App\Http\Resources\SectionResource;
use App\Models\ClubMember;
use App\Models\Member;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Response;

class ClubMemberController extends Controller
{
    protected function rules(): array
    {
        $rules = [
            'from' => 'required|date',
            'to' => 'nullable|date|after:from',
            'memo' => 'nullable|string',
        ];

        return $rules;
    }

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

    public function store(Request $request, Member $member): RedirectResponse
    {
        $attributes = $request->validate($this->rules());

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

    public function update(Request $request, Member $member, ClubMember $clubMember): RedirectResponse
    {
        $attributes = $request->validate($this->rules());

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
