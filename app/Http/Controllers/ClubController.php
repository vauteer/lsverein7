<?php

namespace App\Http\Controllers;

use App\ClubRole;
use App\Http\Resources\ClubResource;
use App\Models\Club;
use App\Rules\Iban;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Response;

class ClubController extends Controller
{
    protected const URL_KEY = 'lastClubsUrl';

    protected function validationRules($id): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                Rule::unique('clubs')->ignore($id),
            ],
            'street' => 'required|string',
            'zipcode' => 'required|string',
            'city' => 'required|string',
            'bank' => 'required|string',
            'account_owner' => 'required|string',
            'iban' => ['required', new Iban()],
            'bic' => 'required|string',
            'sepa' => 'nullable|string',
            'sepa_date' => 'nullable|date',
            'logo' => 'nullable|string',
            'display' => 'required|int',
            'blsv_member' => 'boolean',
            'use_items' => 'boolean',
            'honor_years' => 'nullable|regex:/^\d{1,2}(,\d{1,2})*$/'
        ];

        return $rules;
    }

    public function index(Request $request): Response
    {
        $request->session()->put(self::URL_KEY, url()->full());
        return inertia('Clubs/Index', [
            'clubs' => ClubResource::collection(auth()->user()->clubs()->wherePivot('role', ClubRole::Admin)
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
            ),

            'filters' => $request->only(['search']),
            'canCreate' => auth()->user()->admin,
        ]);
    }

    private function editOptions(): array
    {
        return [
            'origin' => session(self::URL_KEY),
            'displayStyles' => optionsFromArray(Club::displayStyles(), false),
        ];
    }

    public function create(Request $request): Response
    {
        return inertia('Clubs/Edit', $this->editOptions());
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));
        $attributes['iban'] = normalizeIban($attributes['iban']);

        $club = Club::create($attributes);
        $creator = auth()->user();
        $club->users()
            ->attach($creator->id, [
                'admin' => true,
            ]);

        // For the case there was no club before
        if ($creator->club_id === null) {
            $creator->update(['club_id' => $club->id]);
        }

        return redirect(session(self::URL_KEY))
            ->with('success', 'Verein hinzugefügt');
    }

    public function edit(Request $request, Club $club):Response
    {
        return inertia('Clubs/Edit', array_merge($this->editOptions(), [
            'club' => $club->getAttributes(),
            'deletable' => auth()->user()->admin,
        ]));
    }

    public function update(Request $request, Club $club): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($club->id));
        $attributes['iban'] = normalizeIban($attributes['iban']);
        $club->update($attributes);

        Club::removeOrphanLogos();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Verein geändert');
    }

    public function destroy(Request $request, Club $club): RedirectResponse
    {
        $club->delete();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Verein gelöscht');
    }

    public function change(Request $request, Club $club): RedirectResponse
    {
        $user = Auth::user();

        if ($user->switchClub($club->id)) {
            return redirect(session(self::URL_KEY))
                ->with('success', 'Aktuellen Verein gewechselt');
        }

        abort(403);
    }

    public function blsvStatistic(Request $request, Club $club): Response
    {
        return inertia('Clubs/BLSVStat', [
            'origin' => session(self::URL_KEY),
            'downloads' => $club->getBLSVStatistic(),
        ]);
    }

    public function downloads(Request $request, string $filename)
    {
        $path = storage_path('downloads/' . currentClubId() . '_' . $filename);
        if (!file_exists($path))
            abort(404);

        return response()->download($path, $filename);
    }

}
