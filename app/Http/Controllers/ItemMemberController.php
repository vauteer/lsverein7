<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemMemberRequest;
use App\Models\Member;
use App\Models\ItemMember;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ItemMemberController extends Controller
{
    private function editOptions(Member $member): array
    {
        return [
            'origin' => route('members.edit', $member->id),
            'items' => Item::orderBy('name')->get(['id', 'name']),
            'memberId' => $member->id,
        ];
    }

    public function create(Member $member): Response
    {
        return inertia('Members/ItemMember', $this->editOptions($member));
    }

    public function store(ItemMemberRequest $request, Member $member): RedirectResponse
    {
        $attributes = $request->validated();

        $member->items()->attach([currentClubId() => $attributes]);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Funktion hinzugefügt');
    }

    public function edit(Member $member, ItemMember $itemMember):Response
    {
        return inertia('Members/ItemMember', array_merge($this->editOptions($member), [
            'itemMember' => $itemMember->getAttributes(),
        ]));
    }

    public function update(ItemMemberRequest $request, Member $member, ItemMember $itemMember): RedirectResponse
    {
        $attributes = $request->validated();

        $itemMember->update($attributes);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Funktion geändert');
    }

    public function destroy(Member $member, ItemMember $itemMember): RedirectResponse
    {
        $itemMember->delete();

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Funktion gelöscht');
    }
}
