<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClubResource;
use App\Models\Club;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Response;

class ClubController extends Controller
{
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
            'iban' => 'required|string',
            'bic' => 'required|string',
            'sepa' => 'nullable|string',
            'sepa_date' => 'nullable|date',
            'logo' => 'nullable|string',
            'display' => 'required|int',
            'blsv_member' => 'boolean',
        ];

        return $rules;
    }

    public function index(Request $request): Response
    {
        return inertia('Clubs/Index', [
            'clubs' => ClubResource::collection(Auth::user()->clubs()->wherePivot('admin', true)
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
            ),

            'filters' => $request->only(['search']),
            'canCreate' => Auth::user()->can('create', Club::class),
        ]);
    }

    public function create(Request $request): Response
    {
        return inertia('Clubs/Edit')
            ->with('displayStyles', Club::displayStyles());
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));

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

        return redirect()->route('clubs')
            ->with('success', 'Verein hinzugefügt');
    }

    public function edit(Request $request, Club $club):Response
    {
        return inertia('Clubs/Edit', [
            'club' => [
                'id' => $club->id,
                'name' => $club->name,
                'street' => $club->street,
                'zipcode' => $club->zipcode,
                'city' => $club->city,
                'bank' => $club->bank,
                'account_owner' => $club->account_owner,
                'iban' => $club->iban,
                'bic' => $club->bic,
                'sepa' => $club->sepa,
                'sepa_date' => $club->sepa_date,
                'logo' => $club->logo,
                'display' => $club->display,
            ],
        ])->with('displayStyles', Club::displayStyles());
    }

    public function update(Request $request, Club $club): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($club->id));

        $club->update($attributes);

        Club::removeOrphanLogos();

        return redirect()->route('clubs')
            ->with('success', 'Verein geändert');
    }

    public function destroy(Request $request, Club $club): RedirectResponse
    {
        $club->delete();

        return redirect()->route('clubs')
            ->with('success', 'Verein gelöscht');
    }

    public function change(Request $request, Club $club): RedirectResponse
    {
        $user = Auth::user();

        if ($user->switchClub($club->id)) {
            return redirect()->route('clubs')
                ->with('success', 'Aktuellen Verein gewechselt');
        }

        abort(403);
    }

}
