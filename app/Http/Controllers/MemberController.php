<?php

namespace App\Http\Controllers;

use App\ClubRole;
use App\Http\Resources\EventMemberResource;
use App\Http\Resources\ItemMemberResource;
use App\Http\Resources\MemberResource;
use App\Http\Resources\ClubMemberResource;
use App\Http\Resources\MemberRoleResource;
use App\Http\Resources\MemberSectionResource;
use App\Http\Resources\MemberSubscriptionResource;
use App\Models\ClubMember;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Item;
use App\Models\ItemMember;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberSection;
use App\Models\MemberSubscription;
use App\Models\Role;
use App\Models\Section;
use App\Models\Subscription;
use App\Pdf\MemberPdf;
use App\Rules\Iban;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    protected const URL_KEY = 'lastMembersUrl';

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
            'bank' => 'nullable|string|required_if:payment_method,k',
            'account_owner' => 'nullable|string|required_if:payment_method,k',
            'iban' => ['nullable', 'required_if:payment_method,k', new Iban()],
            'bic' => 'nullable|string|required_if:payment_method,k',
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

    public function index(Request $request): \Inertia\Response
    {
        $request->session()->put(self::URL_KEY, url()->full());
        $currentSelection = $this->currentSelection($request);

        return inertia('Members/Index', [
            'members' => MemberResource::collection($currentSelection['query']
                ->paginate(15)
                ->withQueryString()
            ),

            'options' => ['search' => $currentSelection['search']],
            'filters' => optionsFromArray($currentSelection['quickFilters'], false),
            'years' => optionsFromArray($this->getAvailableYears(), false),
            'sorts' => optionsFromArray(self::SORT_METHODS, false),
            'currentYear' => $currentSelection['keyDate']->year,
            'currentFilter' => strval($currentSelection['filter']),
            'currentSort' => $currentSelection['sort'],
            'clubAdmin' => auth()->user()->hasClubRole(ClubRole::Admin),
        ]);
    }

    public function outputPdf(Request $request): \Illuminate\Http\Response
    {
        $currentSelection = $this->currentSelection($request);
        $query = $currentSelection['query'];
        $leftHeadline = $currentSelection['quickFilters'][$currentSelection['filter']] . ' ' . $currentSelection['year'];
        $leftHeadline .= ' (' . $query->count() . ' Personen)';
        $rightHeadline = 'Stand: ' . formatDate($currentSelection['keyDate']);
        $pdf = new MemberPdf('P', 'mm', 'A4');

        $content = $pdf->getOutput($query->get(), currentClub()->name, $leftHeadline, $rightHeadline);

        return response($content)
            ->header('Content-Type', 'application/pdf; name="MyFile.pdf"');
    }

    public function outputCsv(Request $request): \Illuminate\Http\Response
    {
        $currentSelection = $this->currentSelection($request);
        $fileName = str_replace(': ', '_', $currentSelection['quickFilters'][$currentSelection['filter']]);
        $path = storage_path("downloads/{$fileName}.csv");

        $handle = fopen($path, 'w');

        $header = ['ID', 'Vorname', 'Nachname', 'Strasse', 'Plz', 'Ort', 'Alter', 'Geschlecht', 'MGJahre', 'Ehrung'];

        // commas in data will be handled from fputcsv !
        fputcsv($handle, $header);
        $members = $currentSelection['query']->get();

        foreach ($members as $member)
        {
            $fields = array(
                $member->id,
                mb_convert_encoding($member->first_name, 'ISO-8859-1', 'UTF-8'),
                mb_convert_encoding($member->surname, 'ISO-8859-1', 'UTF-8'),
                mb_convert_encoding($member->street, 'ISO-8859-1', 'UTF-8'),
                $member->zipcode,
                mb_convert_encoding($member->city, 'ISO-8859-1', 'UTF-8'),
                $member->age, $member->gender->value,
                $member->membershipYears(), $member->dueHonor(),
            );

            fputcsv($handle, $fields);
        }

        fclose($handle);

        $content = file_get_contents($path);

        return response($content)
            ->header('content-type', 'text/comma-separated-values')
            ->header('content-length', strlen($content))
            ->header('content-disposition', 'attachment; filename="' . $fileName . '.csv"');

    }

    private function editOptions(): array
    {
        return [
            'origin' => session(self::URL_KEY),
            'genders' => optionsFromArray(Member::availableGenders(), false),
            'paymentMethods' => optionsFromArray(Member::availablePaymentMethods(), false),
        ];
    }

    public function create(Request $request): \Inertia\Response
    {
        return inertia('Members/Edit', array_merge($this->editOptions(), [
            'sections' => Section::orderBy('name')->get(['id', 'name']),
            'subscriptions' => Subscription::orderBy('name')->get(['id', 'name']),
        ]));
    }

    public function show(Request $request, Member $member):\Inertia\Response
    {
        return inertia('Members/Show', [
            'member' => $member->getAttributes(),
            'origin' => session(self::URL_KEY),
            'advanced' => auth()->user()->hasAdvancedRights(),
            'birthday' => formatDate($member->birthday),
            'death_day' => formatDate($member->death_day),
            'age' => $member->age,
            'entry' => formatDate($member->entry()),
            'membershipYears' => $member->membershipYears(),
            'memberClubs' => ClubMemberResource::collection(ClubMember::where('member_id', $member->id)->get()),
            'memberSections' => MemberSectionResource::collection(MemberSection::where('member_id', $member->id)->get()),
            'memberSubscriptions' => MemberSubscriptionResource::collection(MemberSubscription::where('member_id', $member->id)->get()),
            'memberEvents' => EventMemberResource::collection(EventMember::where('member_id', $member->id)->get()),
            'memberRoles' => MemberRoleResource::collection(MemberRole::where('member_id', $member->id)->get()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));
        $entryData = $request->validate($this->entryValidationRules());
        $attributes['iban'] = normalizeIban($attributes['iban']);

        $member = Member::create(array_merge($attributes, ['club_id' => currentClubId()]));

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

        return redirect(session(self::URL_KEY))
            ->with('success', 'Mitglied hinzugefügt');
    }

    public function edit(Request $request, Member $member):\Inertia\Response
    {
        return inertia('Members/Edit', array_merge($this->editOptions(), [
            'member' => $member->getAttributes(),
            'isMember' => $member->isMember(),
            'date' => Carbon::today()->format('Y-m-d'),
            'memberClubs' => ClubMemberResource::collection(ClubMember::where('member_id', $member->id)->get()),
            'memberSections' => MemberSectionResource::collection(MemberSection::where('member_id', $member->id)->get()),
            'memberSubscriptions' => MemberSubscriptionResource::collection(MemberSubscription::where('member_id', $member->id)->get()),
            'memberEvents' => EventMemberResource::collection(EventMember::where('member_id', $member->id)->get()),
            'memberRoles' => MemberRoleResource::collection(MemberRole::where('member_id', $member->id)->get()),
            'memberItems' => ItemMemberResource::collection(ItemMember::where('member_id', $member->id)->get()),
        ]));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($member->id));
        $attributes['iban'] = normalizeIban($attributes['iban']);

        $member->update($attributes);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Mitglied geändert');
    }

    public function destroy(Request $request, Member $member): RedirectResponse
    {
        $member->delete();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Mitglied gelöscht');
    }

    public function resign(Request $request, Member $member): RedirectResponse
    {
        $end = $request->input('date');
        $member->memberships()->whereNull('to')->update(['to' => $end]);
        $member->sections()->whereNull('to')->update(['to' => $end]);

        return redirect(route('members.edit', $member))
            ->with('success', 'Mitgliedschaft beendet');
    }

    private function getQuickFilters()
    {
        $filters = [
            0 => 'Mit Ehemaligen',
            1 => 'Mitglieder',
            2 => 'Ex-Mitglieder',
            3 => 'Runde Geburtstage',
            4 => 'Todesfälle',
            5 => 'Eintritte',
            6 => 'Austritte',
            7 => 'Kinder (-13 Jahre)',
            8 => 'Jugendliche (14-17 Jahre)',
            9 => 'Erwachsene',
            10 => 'Fällige Ehrungen',
            11 => 'Hat Funktion',
            12 => 'Hatte Funktion',
        ];

        if (currentUser()->hasAdminRights()) {
            $filters[13] = 'Ohne Beitrag';
        }

//        $sections = currentClub()->usedSections();
//        foreach ($sections as $section) {
//            $filters["hasSection_" . $section->id] = "Abteilung: " . $section->name;
//        }

        return $filters;
    }

    private function getAvailableYears()
    {
        $now = now();
//        $result[$now->year] = formatDate($now);
        $result[$now->year] = $now->format('Y');

        $lastYear = $now->year - 1;
        $firstYear = $lastYear - 9;

        for ($i = $lastYear; $i > $firstYear; $i--) {
//            $result[$i] = "31.12.{$i}";
            $result[$i] = "{$i}";
        }

        return $result;
    }

    private function currentSelection(Request $request): array
    {
        $result['filter'] = $request->has('filter') ? $request->input('filter') : '1';
        $result['search'] = $request->has('search') ? $request->input('search') : '';
        $result['sort'] = $request->has('sort') ? intval($request->input('sort')) : 1;
        $result['year'] = $request->has('year') ? intval($request->input('year')) : now()->year;
        $result['keyDate'] = Carbon::create($result['year'], 12, 31, 23, 59, 59)->min(now());
        Member::$_keyDate = $result['keyDate'];

        $query = Member::query()->with(['memberships', 'events', 'sections', 'subscriptions', 'roles']);
        if ($result['search']) {
            $query->like($result['search']);
        }

        $quickFilters = $this->getQuickFilters();

        if (is_numeric($result['filter'])) {
            $this->applyQuickFilter($result['filter'], $query);
        }
        else {
            $this->applySpecialFilters($result['filter'], $query, $quickFilters);
        }

        $result['quickFilters'] = $quickFilters;

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
            4 => $query->dead(),
            5 => $query->joined(),
            6 => $query->retired(),
            7 => $query->members()->ageRange(null, 13),
            8 => $query->members()->ageRange(14, 17),
            9 => $query->members()->ageRange(18, null),
            10 => $query->members()->dueHonor(),
            11 => $query->members()->hasRole(),
            12 => $query->everRole(),
            13 => $query->members()->noSubscription(),
        };
    }

    public function applySpecialFilters(string $filter, Builder $query, array &$filters)
    {
        if (preg_match('/^hasSection_(\d+)$/', $filter, $match)) {
            $query->members()->inSections($match[1]);
            $filters[$filter] = "Sparte: " . Section::find($match[1])->name;
        }
        else if (preg_match('/^hasSubscription_(\d+)$/', $filter, $match)) {
            // subscriptions don't have a range
            $keyDate = now();
            Member::$_keyDate = $keyDate;
            $query->members()->hasSubscription($match[1]);
            $filters[$filter] = "Beitrag: " . Subscription::find($match[1])->name;
        }
        else if (preg_match('/^hasPayment_([a-z])$/', $filter, $match)) {
            $query->members()->paymentMethods($match[1]);
        }
        else if (preg_match('/^hasRole_(\d+)$/', $filter, $match)) {
            $query->hasRole($match[1]);
            $filters[$filter] = Role::find($match[1])->name . '(Aktuell)';
        }
        else if (preg_match('/^everRole_(\d+)$/', $filter, $match)) {
            $query->everRole($match[1]);
            $filters[$filter] = Role::find($match[1])->name . '(Jemals)';
        }
        else if (preg_match('/^hadEvent_(\d+)$/', $filter, $match)) {
            $query->hadEvent($match[1]);
            $filters[$filter] = Event::find($match[1])->name;
        }
        else if (preg_match('/^hasItem_(\d+)$/', $filter, $match)) {
            $query->hasItem($match[1]);
            $filters[$filter] = Item::find($match[1])->name . '(Aktuell)';
        }
        else if (preg_match('/^everItem_(\d+)$/', $filter, $match)) {
            $query->everItem($match[1]);
            $filters[$filter] = Item::find($match[1])->name . '(Jemals)';
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
