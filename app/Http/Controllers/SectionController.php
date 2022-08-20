<?php

namespace App\Http\Controllers;

use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;

class SectionController extends Controller
{
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
        ];

        return $rules;
    }


    public function index(Request $request):Response
    {
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
        return inertia('Sections/Edit');
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));

        Section::create(array_merge($attributes, ['club_id' => auth()->user()->club_id]));

        return redirect()->route('sections')
            ->with('success', 'Abteilung hinzugefügt');
    }

    public function edit(Request $request, Section $section):Response
    {
        return inertia('Sections/Edit', [
            'section' => [
                'id' => $section->id,
                'name' => $section->name,
            ],
        ]);
    }

    public function update(Request $request, Section $section): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($section->id));

        $section->update($attributes);

        return redirect()->route('sections')
            ->with('success', 'Abteilung geändert');
    }

    public function destroy(Request $request, Section $section): RedirectResponse
    {
        $section->delete();

        return redirect()->route('sections')
            ->with('success', 'Abteilung gelöscht');
    }

}
