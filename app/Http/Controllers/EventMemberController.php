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

    private function editOptions(Member $member): array
    {
        return [
            'origin' => route('members.edit', $member->id),
            'events' => Event::orderBy('name')->get(['id', 'name'])
                ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name]),
            'memberId' => $member->id,
        ];
    }

    public function create(Request $request, Member $member): Response
    {
        return inertia('Members/EventMember', $this->editOptions($member));
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules());

        $member->events()->attach([currentClubId() => $attributes]);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Ereignis hinzugefügt');
    }

    public function edit(Request $request, Member $member, EventMember $eventMember):Response
    {
        return inertia('Members/EventMember',array_merge($this->editOptions($member), [
            'eventMember' => $eventMember->getAttributes(),
        ]));
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
