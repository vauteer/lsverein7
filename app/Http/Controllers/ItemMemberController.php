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
    protected function validationRules(): array
    {
        $rules = [
            'item_id' => 'exists:App\Models\Item,id',
            'from' => 'required|date',
            'to' => 'nullable|date|after:from',
            'memo' => 'nullable|string',
        ];

        return $rules;
    }

    public function create(Request $request, Member $member): Response
    {
        return inertia('Members/ItemMember', [
            'origin' => route('members.edit', $member->id),
            'items' => Item::orderBy('name')->get(['id', 'name'])
                ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name]),
            'memberId' => $member->id,
        ]);
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules());

        $member->items()->attach([currentClubId() => $attributes]);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Funktion hinzugefügt');
    }

    public function edit(Request $request, Member $member, ItemMember $itemMember):Response
    {
        return inertia('Members/ItemMember', [
            'itemMember' => $itemMember->getAttributes(),
            'origin' => route('members.edit', $member->id),
            'items' => Item::orderBy('name')->get(['id', 'name'])
                ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name]),
            'memberId' => $member->id,
        ]);
    }

    public function update(Request $request, Member $member, ItemMember $itemMember): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules());

        $itemMember->update($attributes);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Funktion geändert');
    }

    public function destroy(Request $request, Member $member, ItemMember $itemMember): RedirectResponse
    {
        $itemMember->delete();

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Funktion gelöscht');
    }
}
