<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventMemberRequest;
use App\Models\Member;
use App\Models\EventMember;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class EventMemberController extends Controller
{
    private function editOptions(Member $member): array
    {
        return [
            'origin' => route('members.edit', $member->id),
            'events' => Event::orderBy('name')->get(['id', 'name']),
            'memberId' => $member->id,
        ];
    }

    public function create(Member $member): Response
    {
        return inertia('Members/EventMember', $this->editOptions($member));
    }

    public function store(EventMemberRequest $request, Member $member): RedirectResponse
    {
        $attributes = $request->validated();

        $member->events()->attach([currentClubId() => $attributes]);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Ereignis hinzugefügt');
    }

    public function edit(Member $member, EventMember $eventMember):Response
    {
        return inertia('Members/EventMember',array_merge($this->editOptions($member), [
            'eventMember' => $eventMember->getAttributes(),
        ]));
    }

    public function update(EventMemberRequest $request, Member $member, EventMember $eventMember): RedirectResponse
    {
        $attributes = $request->validated();

        $eventMember->update($attributes);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Ereignis geändert');
    }

    public function destroy(Member $member, EventMember $eventMember): RedirectResponse
    {
        $eventMember->delete();

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Ereignis gelöscht');
    }

}
