<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberSubscription;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class MemberSubscriptionController extends Controller
{
    protected function rules(): array
    {
        $rules = [
            'subscription_id' => 'exists:App\Models\Subscription,id',
            'memo' => 'nullable|string',
        ];

        return $rules;
    }

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

    public function store(Request $request, Member $member): RedirectResponse
    {
        $attributes = $request->validate($this->rules());

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

    public function update(Request $request, Member $member, memberSubscription $memberSubscription): RedirectResponse
    {
        $attributes = $request->validate($this->rules());

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
