<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ItemMember;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ItemMemberController extends Controller
{
    protected function rules(): array
    {
        $rules = [
            'item_id' => 'exists:App\Models\Item,id',
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
            'items' => Item::orderBy('name')->get(['id', 'name']),
            'memberId' => $member->id,
        ];
    }

    public function create(Member $member): Response
    {
        return inertia('Members/ItemMember', $this->editOptions($member));
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $attributes = $request->validate($this->rules());

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

    public function update(Request $request, Member $member, ItemMember $itemMember): RedirectResponse
    {
        $attributes = $request->validate($this->rules());

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
