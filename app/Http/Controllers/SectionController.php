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
                    ->where(fn ($query) => $query->where('club_id', currentClubId()))
                    ->ignore($id),
            ],
            'blsv_id' => 'nullable|integer',
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

    private function editOptions(): array
    {
        return [
            'origin' => session(self::URL_KEY),
            'blsvSections' => optionsFromArray(Section::BLSV_SECTIONS),
        ];
    }

    public function create(Request $request): Response
    {
        return inertia('Sections/Edit', $this->editOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));

        Section::create(array_merge($attributes, [
            'club_id' => currentClubId(),
        ]));

        return redirect(session(self::URL_KEY))
            ->with('success', 'Abteilung hinzugefügt');
    }

    public function edit(Request $request, Section $section):Response
    {
        return inertia('Sections/Edit', array_merge($this->editOptions(), [
            'section' => $section->getAttributes(),
            'deletable' => !$section->isInUse(),
        ]));
    }

    public function update(Request $request, Section $section): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($section->id));

        $section->update($attributes);

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
