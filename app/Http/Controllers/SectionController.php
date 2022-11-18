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
    public function index(Request $request):Response
    {
        $this->setLastUrl();

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
            'origin' => $this->getLastUrl(),
            'blsvSections' => currentClub()->blsv_member ? optionsFromArray(Section::BLSV_SECTIONS, true) : null,
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

        return redirect($this->getLastUrl())
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

        return redirect($this->getLastUrl())
            ->with('success', 'Abteilung geändert');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $section->delete();

        return redirect($this->getLastUrl())
            ->with('success', 'Abteilung gelöscht');
    }

}
