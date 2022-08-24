<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventMemberResource;
use App\Http\Resources\MemberResource;
use App\Http\Resources\ClubMemberResource;
use App\Http\Resources\MemberRoleResource;
use App\Http\Resources\MemberSectionResource;
use App\Http\Resources\MemberSubscriptionResource;
use App\Models\ClubMember;
use App\Models\EventMember;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberSection;
use App\Models\MemberSubscription;
use App\Models\Section;
use App\Models\Subscription;
use App\Rules\Iban;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;

class MemberController extends Controller
{
    protected function validationRules(): array
    {
        $rules = [
            'surname' => 'required|string',
            'first_name' => 'required|string',
            'gender' => 'required',
            'birthday' => 'required|date',
            'death_day' => 'nullable|date',
            'street' => 'string',
            'zipcode' => 'string',
            'city' => 'string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'payment_method' => 'required',
            'bank_name' => 'nullable|string',
            'account_owner' => 'nullable|string',
            'iban' => ['nullable', new Iban()],
            'bic' => 'nullable|string',
        ];

        return $rules;
    }

    protected function entryValidationRules(): array
    {
        return [
            'entry_date' => 'required|date',
            'section' => 'required|integer',
            'subscription' => 'nullable|integer',
        ];
    }

    public function index(Request $request):Response
    {
        return inertia('Members/Index', [
            'members' => MemberResource::collection(Member::query()
                ->when($request->input('search'), function($query, $search) {
                    $query->where('surname', 'like', "%{$search}%");
                    $query->orWhere('first_name', 'like', "%{$search}%");
                })
                ->orderBy('surname')
                ->orderBy('first_name')
                ->paginate(15)
                ->withQueryString()
            ),

            'filters' => $request->only(['search']),
            'canCreate' => auth()->user()->can('create', Member::class),
        ]);
    }

    public function create(Request $request): Response
    {
        return inertia('Members/Edit')
            ->with('genders', Member::genders())
            ->with('paymentMethods', Member::paymentMethods())
            ->with('sections', Section::get(['id', 'name'])->mapWithKeys(fn ($item) => [$item->id => $item->name]))
            ->with('subscriptions', Subscription::get(['id', 'name'])->mapWithKeys(fn ($item) => [$item->id => $item->name]));
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));
        $entryData = $request->validate($this->entryValidationRules());
        $attributes['iban'] = normalizeIban($attributes['iban']);

        $member = Member::create(array_merge($attributes, ['club_id' => auth()->user()->club_id]));

        $member->clubs()->attach([$member->club_id => [
            'from' => $entryData['entry_date']
        ]]);
        $member->sections()->attach([$entryData['section'] => [
            'from' => $entryData['entry_date'],
            'memo' => 'Eintritt',
        ]]);

        if ($entryData['subscription']) {
            $member->subscriptions()->attach([$entryData['subscription'] => [
                'memo' => 'Eintritt',
            ]]);
        }

        return redirect()->route('members')
            ->with('success', 'Mitglied hinzugefügt');
    }

    public function edit(Request $request, Member $member):Response
    {
        return inertia('Members/Edit', [ 'member' => $member->getAttributes() ])
            ->with('genders', Member::genders())
            ->with('paymentMethods', Member::paymentMethods())
            ->with('memberClubs', ClubMemberResource::collection(ClubMember::where('member_id', $member->id)->get()))
            ->with('memberSections', MemberSectionResource::collection(MemberSection::where('member_id', $member->id)->get()))
            ->with('memberSubscriptions', MemberSubscriptionResource::collection(MemberSubscription::where('member_id', $member->id)->get()))
            ->with('memberEvents', EventMemberResource::collection(EventMember::where('member_id', $member->id)->get()))
            ->with('memberRoles', MemberRoleResource::collection(MemberRole::where('member_id', $member->id)->get()));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($member->id));
        $attributes['iban'] = normalizeIban($attributes['iban']);

        $member->update($attributes);

        return redirect()->route('members')
            ->with('success', 'Mitglied geändert');
    }

    public function destroy(Request $request, Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('members')
            ->with('success', 'Mitglied gelöscht');
    }

}
