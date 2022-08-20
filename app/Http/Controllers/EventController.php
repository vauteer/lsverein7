<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;

class EventController extends Controller
{
    protected function validationRules($id): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                Rule::unique('events')
                    ->where(fn ($query) => $query->where('club_id', auth()->user()->club_id))
                    ->ignore($id),
            ],
        ];

        return $rules;
    }


    public function index(Request $request):Response
    {
        return inertia('Events/Index', [
            'events' => EventResource::collection(Event::query()
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(15)
                ->withQueryString()
            ),

            'filters' => $request->only(['search']),
            'canCreate' => auth()->user()->can('create', Event::class),
        ]);
    }

    public function create(Request $request): Response
    {
        return inertia('Events/Edit');
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));

        Event::create(array_merge($attributes, ['club_id' => auth()->user()->club_id]));

        return redirect()->route('events')
            ->with('success', 'Ereignis hinzugefügt');
    }

    public function edit(Request $request, Event $event):Response
    {
        return inertia('Events/Edit', [
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
            ],
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($event->id));

        $event->update($attributes);

        return redirect()->route('events')
            ->with('success', 'Ereignis geändert');
    }

    public function destroy(Request $request, Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('events')
            ->with('success', 'Ereignis gelöscht');
    }

}
