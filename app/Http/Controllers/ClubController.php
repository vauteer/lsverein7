<?php

namespace App\Http\Controllers;

use App\ClubRole;
use App\Http\Requests\ClubRequest;
use App\Http\Resources\ClubResource;
use App\Models\Club;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class ClubController extends Controller
{
    protected const URL_KEY = 'lastClubsUrl';

    public function index(Request $request): Response
    {
        session()->put(self::URL_KEY, url()->full());

        return inertia('Clubs/Index', [
            'clubs' => ClubResource::collection(auth()->user()->clubs()->wherePivot('role', ClubRole::Admin)
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
            ),

            'options' => $request->only(['search']),
            'canCreate' => auth()->user()->admin,
        ]);
    }

    private function editOptions(): array
    {
        return [
            'origin' => session(self::URL_KEY, '/backups'), // for the case it's a new installation and we have no clubs
            'displayStyles' => optionsFromArray(Club::displayStyles(), false),
        ];
    }

    public function create(): Response
    {
        return inertia('Clubs/Edit', $this->editOptions());
    }

    public function store(ClubRequest $request): RedirectResponse
    {
        $attributes = $request->validated();
        $attributes['iban'] = normalizeIban($attributes['iban']);

        $club = Club::create($attributes);
        $creator = auth()->user();
        $club->users()
            ->attach($creator->id, [
                'role' => 256,
            ]);

        // For the case there was no club before
        if ($creator->club_id === null) {
            $creator->update(['club_id' => $club->id]);
        }

        return redirect(session(self::URL_KEY, 'clubs')) // URL_KEY is not set if creating the first club
            ->with('success', 'Verein hinzugefügt');
    }

    public function edit(Club $club):Response
    {
        return inertia('Clubs/Edit', array_merge($this->editOptions(), [
            'club' => $club->getAttributes(),
            'deletable' => auth()->user()->admin,
        ]));
    }

    public function update(ClubRequest $request, Club $club): RedirectResponse
    {
        $attributes = $request->validated();
        $attributes['iban'] = normalizeIban($attributes['iban']);
        $club->update($attributes);

        Club::removeOrphanLogos();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Verein geändert');
    }

    public function destroy(Club $club): RedirectResponse
    {
        $club->delete();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Verein gelöscht');
    }

    public function change(Club $club): RedirectResponse
    {
        $user = Auth::user();

        if ($user->switchClub($club->id)) {
            return redirect(session(self::URL_KEY))
                ->with('success', 'Aktuellen Verein gewechselt');
        }

        abort(403);
    }

    public function blsvStatistic(Club $club): Response
    {
        return inertia('Clubs/BLSVStat', [
            'origin' => session(self::URL_KEY),
            'downloads' => $club->getBLSVStatistic(),
        ]);
    }

    public function downloads(string $filename)
    {
        $path = storage_path('downloads/' . currentClubId() . '_' . $filename);
        if (!file_exists($path))
            abort(404);

        return response()->download($path, $filename);
    }

}
