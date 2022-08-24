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
    protected function validationRules(): array
    {
        $rules = [
            'subscription_id' => 'exists:App\Models\Subscription,id',
            'memo' => 'nullable|string',
        ];

        return $rules;
    }

    public function create(Request $request, Member $member): Response
    {
        return inertia('Members/MemberSubscription')
            ->with('subscriptions', Subscription::get(['id', 'name'])->mapWithKeys(fn ($item) => [$item->id => $item->name]))
            ->with('memberId', $member->id);
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules());

        $member->subscriptions()->attach([auth()->user()->club_id => $attributes]);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Beitrag hinzugefügt');
    }

    public function edit(Request $request, Member $member, memberSubscription $memberSubscription):Response
    {
        return inertia('Members/MemberSubscription', [
            'memberSubscription' => $memberSubscription->getAttributes(),
        ])
            ->with('subscriptions', Subscription::get(['id', 'name'])->mapWithKeys(fn ($item) => [$item->id => $item->name]))
            ->with('memberId', $member->id);
    }

    public function update(Request $request, Member $member, memberSubscription $memberSubscription): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules());

        $memberSubscription->update($attributes);

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Beitrag geändert');
    }

    public function destroy(Request $request, Member $member, memberSubscription $memberSubscription): RedirectResponse
    {
        $memberSubscription->delete();

        return redirect()->route('members.edit', $member->id)
            ->with('success', 'Beitrag gelöscht');
    }

}
