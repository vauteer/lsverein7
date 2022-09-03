<?php

namespace App\Http\Controllers;

use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Response;

class SectionController extends Controller
{
    protected const URL_KEY = 'lastSectionsUrl';

    protected function validationRules($id): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                Rule::unique('sections')
                    ->where(fn ($query) => $query->where('club_id', auth()->user()->club_id))
                    ->ignore($id),
            ],
            'global' => 'boolean',
        ];

        return $rules;
    }


    public function index(Request $request):Response
    {
        $request->session()->put(self::URL_KEY, url()->full());
        return inertia('Sections/Index', [
            'sections' => SectionResource::collection(Section::query()
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(15)
                ->withQueryString()
            ),

            'filters' => $request->only(['search']),
            'canCreate' => auth()->user()->can('create', Section::class),
        ]);
    }

    public function create(Request $request): Response
    {
        return inertia('Sections/Edit', [
            'origin' => session(self::URL_KEY)
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));

        Section::create([
            'club_id' => $attributes['global'] ? null : currentClubId(),
            'name' => $attributes['name'],
        ]);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Abteilung hinzugefügt');
    }

    public function edit(Request $request, Section $section):Response
    {
        return inertia('Sections/Edit', [
            'section' => $section->getAttributes(),
            'deletable' => !$section->isInUse(),
            'origin' => session(self::URL_KEY),
        ]);
    }

    public function update(Request $request, Section $section): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($section->id));

        $section->update([
            'club_id' => $attributes['global'] ? null : currentClubId(),
            'name' => $attributes['name'],
        ]);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Abteilung geändert');
    }

    public function destroy(Request $request, Section $section): RedirectResponse
    {
        $section->delete();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Abteilung gelöscht');
    }

}
