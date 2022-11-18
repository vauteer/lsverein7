<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberSubscriptionRequest;
use App\Models\Member;
use App\Models\MemberSubscription;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Response;

class MemberSubscriptionController extends Controller
{
    public function editOptions(Member $member): array
    {
        return [
            'origin' => route('members.edit', $member->id),
            'subscriptions' => Subscription::orderBy('name')->get(['id', 'name']),
            'memberId' => $member->id,
        ];
    }

    public function create(Member $member): Response
    {
        return inertia('Members/MemberSubscription', $this->editOptions($member));
    }

    public function store(MemberSubscriptionRequest $request, Member $member): RedirectResponse
    {
        $attributes = $request->validated();

        $member->subscriptions()->attach([currentClubId() => $attributes]);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Beitrag hinzugefügt');
    }

    public function edit(Member $member, memberSubscription $memberSubscription):Response
    {
        return inertia('Members/MemberSubscription', array_merge($this->editOptions($member), [
            'memberSubscription' => $memberSubscription->getAttributes(),
        ]));
    }

    public function update(MemberSubscriptionRequest $request, Member $member, memberSubscription $memberSubscription): RedirectResponse
    {
        $attributes = $request->validated();
        Log::info("MSC.update: ", $attributes);

        $memberSubscription->update($attributes);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Beitrag geändert');
    }

    public function destroy(Member $member, memberSubscription $memberSubscription): RedirectResponse
    {
        $memberSubscription->delete();

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Beitrag gelöscht');
    }

}
