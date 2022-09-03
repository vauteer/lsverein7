<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Eventmember;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class EventMemberController extends Controller
{
    protected function validationRules(): array
    {
        $rules = [
            'event_id' => 'exists:App\Models\Event,id',
            'date' => 'required|date',
            'memo' => 'nullable|string',
        ];

        return $rules;
    }

    public function create(Request $request, Member $member): Response
    {
        return inertia('Members/EventMember', [
            'origin' => route('members.edit', $member->id),
            'events' => Event::get(['id', 'name'])->mapWithKeys(fn ($item) => [$item->id => $item->name]),
            'memberId' => $member->id,
        ]);
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules());

        $member->events()->attach([auth()->user()->club_id => $attributes]);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Ereignis hinzugefügt');
    }

    public function edit(Request $request, Member $member, EventMember $eventMember):Response
    {
        return inertia('Members/EventMember', [
            'eventMember' => $eventMember->getAttributes(),
            'origin' => route('members.edit', $member->id),
            'events' => Event::get(['id', 'name'])->mapWithKeys(fn ($item) => [$item->id => $item->name]),
            'memberId' => $member->id,
        ]);
    }

    public function update(Request $request, Member $member, Eventmember $eventMember): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules());

        $eventMember->update($attributes);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Ereignis geändert');
    }

    public function destroy(Request $request, Member $member, EventMember $eventMember): RedirectResponse
    {
        $eventMember->delete();

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Ereignis gelöscht');
    }

}
