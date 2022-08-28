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
use App\Pdf\MemberPdf;
use App\Rules\Iban;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Inertia\ResponseFactory;

class MemberController extends Controller
{
    protected const SORT_METHODS = [
        1 => "Name",
        2 => "Adresse",
        3 => "Geburtstag",
        4 => "Alter",
        5 => "Bank",
        6 => "Id"
    ];

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

    public function index(Request $request): Response|ResponseFactory
    {
        $currentSelection = $this->currentSelection($request);

        return inertia('Members/Index', [
            'members' => MemberResource::collection($currentSelection['query']
                ->paginate(15)
                ->withQueryString()
            ),

            'searchString' => $currentSelection['search'],
            'filters' => $this->getQuickFilters(),
            'years' => $this->getAvailableYears(),
            'sorts' => self::SORT_METHODS,
            'currentYear' => $currentSelection['keyDate']->year,
            'currentFilter' => strval($currentSelection['filter']),
            'currentSort' => $currentSelection['sort'],
            'canCreate' => auth()->user()->can('create', Member::class),
        ]);
    }

    public function pdf(Request $request)
    {
        $currentSelection = $this->currentSelection($request);
        $query = $currentSelection['query'];
        $desc = 'Mitglieder';
        $desc .= ' (' . $query->count() . ')';
        $pdf = new MemberPdf('P', 'mm', 'A4');

        $content = $pdf->getOutput($query->get(), $desc, currentClub()->name);

        return response($content)
            ->header('Content-Type', 'application/pdf; name="MyFile.pdf"');
    }

    public function create(Request $request): Response
    {
        return inertia('Members/Edit')
            ->with('genders', Member::availableGenders())
            ->with('paymentMethods', Member::availablePaymentMethods())
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
            ->with('genders', Member::availableGenders())
            ->with('paymentMethods', Member::availablePaymentMethods())
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

    private function getQuickFilters()
    {
        $filters = [
            0 => 'Mit Ehemaligen',
            1 => 'Mitglieder',
            2 => 'Ex-Mitglieder',
            3 => 'Runde Geburtstage',
            4 => 'Gestorben',
            5 => 'Eintritte',
            6 => 'Austritte',
            7 => 'Erwachsene',
            8 => 'Kinder & Jugendliche',
            9 => 'Fällige Ehrungen',
            10 => 'Hat Funktion',
            11 => 'Hatte Funktion',
            12 => 'Ohne Beitrag',
        ];

        $sections = Section::used();
        foreach ($sections as $section) {
            $filters["hasSection_" . $section->id] = "Abteilung: " . $section->name;
        }

        $subscriptions = Subscription::used();
        foreach ($subscriptions as $subscription) {
            $filters["hasSubscription_" . $subscription->id] = "Beitrag: " . $subscription->name;
        }

        $paymentMethods = Member::availablePaymentMethods();
        foreach ($paymentMethods as $key => $value) {
            $filters["hasPayment_" . $key] = "Zahlung: " . $value;
        }

        return $filters;
    }

    private function getAvailableYears()
    {
        $now = Carbon::now();
        $result[$now->year] = formatDate($now);

        $lastYear = $now->year - 1;
        $firstYear = $lastYear - 9;

        for ($i = $lastYear; $i > $firstYear; $i--) {
            $result[$i] = "31.12.{$i}";
        }

        return $result;
    }

    private function currentSelection(Request $request): array
    {
        $result['filter'] = $request->has('filter') ? $request->input('filter') : '1';
        $result['search'] = $request->has('search') ? $request->input('search') : '';
        $result['sort'] = $request->has('sort') ? intval($request->input('sort')) : 1;
        $result['year'] = $request->has('year') ? intval($request->input('year')) : Carbon::now()->year;
        $result['keyDate'] = Carbon::create($result['year'], 12, 31, 23, 59, 59)->min(Carbon::now());
        Member::$_keyDate = $result['keyDate'];

        $query = Member::query()->with(['memberships', 'events', 'sections', 'subscriptions', 'roles']);
        if ($result['search']) {
            $query->like($result['search']);
        }

        is_numeric($result['filter']) ? $this->applyQuickFilter($result['filter'], $query) :
            $this->applySpecialFilters($result['filter'], $query);

        $this->applySort($result['sort'], $query);
        $result['query'] = $query;

        return $result;
    }

    public function applyQuickFilter(string $filter, Builder $query)
    {
        return match(intval($filter)) {
            0 => $query,
            1 => $query->members(),
            2 => $query->noMembers(),
            3 => $query->members()->milestoneBirthdays(),
            4 => $query->deaths(),
            5 => $query->joined(),
            6 => $query->retired(),
            7 => $query->members()->ageRange(18, null),
            8 => $query->members()->ageRange(null, 18),
            9 => $query->members()->dueHonor(),
            10 => $query->members()->hasRole(),
            11 => $query->everRole(),
            12 => $query->members()->noSubscription(),
        };
    }

    public function applySpecialFilters(string &$filter, Builder $query)
    {
        if (preg_match('/^hasSection_(\d+)$/', $filter, $match)) {
            $query->members()->sectionMembers($match[1]);
        }
        else if (preg_match('/^hasSubscription_(\d+)$/', $filter, $match)) {
            // subscriptions don't have a range
            $keyDate = Carbon::now();
            Member::$_keyDate = $keyDate;
            $query->members()->hasSubscription($match[1]);
        }
        else if (preg_match('/^hasPayment_([a-z])$/', $filter, $match)) {
            $query->members()->paymentMethods($match[1]);
        }
        else if (preg_match('/^hasRole_(\d+)$/', $filter, $match)) {
            $query->hasRole($match[1]);
            $filter = '';
        }
        else if (preg_match('/^everRole_(\d+)$/', $filter, $match)) {
            $query->everRole($match[1]);
            $filter = '';
        }
        else if (preg_match('/^hadEvent_(\d+)$/', $filter, $match)) {
            $query->hadEvent($match[1]);
            $filter = '';
        }
    }

    /**
     * @param int $sort
     * @param Builder $query
     * @return void
     */
    public function applySort(int $sort, Builder $query): void
    {
        switch ($sort) {
            case 1 :
                $query->orderBy('surname')->orderBy('first_name');
                break;
            case 2 :
                $query->orderBy('city')->orderBy('street')->orderBy('surname')->orderBy('first_name');
                break;
            case 3 :
                $query->orderByRaw('MONTH(birthday)')->orderByRaw('DAY(birthday)');
                break;
            case 4 :
                $query->orderBy('birthday', 'desc');
                break;
            case 5:
                $query->orderBy('bank')->orderBy('surname')->orderBy('first_name');
                break;
            case 6 :
                $query->orderBy('id');
                break;
        }
    }
}
