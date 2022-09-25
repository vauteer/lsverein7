<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class SectionController extends Controller
{
    protected const URL_KEY = 'lastSectionsUrl';

    public function index(Request $request):Response
    {
        session([self::URL_KEY => url()->full()]);

        return inertia('Sections/Index', [
            'sections' => SectionResource::collection(Section::query()
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(15)
                ->withQueryString()
            ),

            'options' => $request->only(['search']),
            'canCreate' => auth()->user()->can('create', Section::class),
        ]);
    }

    private function editOptions(): array
    {
        return [
            'origin' => session(self::URL_KEY),
            'blsvSections' => currentClub()->blsvMember ? optionsFromArray(Section::BLSV_SECTIONS, true) : null,
        ];
    }

    public function create(): Response
    {
        return inertia('Sections/Edit', $this->editOptions());
    }

    public function store(SectionRequest $request): RedirectResponse
    {
        $attributes = $request->validated();

        Section::create(array_merge($attributes, [
            'club_id' => currentClubId(),
        ]));

        return redirect(session(self::URL_KEY))
            ->with('success', 'Abteilung hinzugefügt');
    }

    public function edit(Section $section):Response
    {
        return inertia('Sections/Edit', array_merge($this->editOptions(), [
            'section' => $section->getAttributes(),
            'deletable' => !$section->isUsed(),
        ]));
    }

    public function update(SectionRequest $request, Section $section): RedirectResponse
    {
        $attributes = $request->validated();

        $section->update($attributes);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Abteilung geändert');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $section->delete();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Abteilung gelöscht');
    }

}
