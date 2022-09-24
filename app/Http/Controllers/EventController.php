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
    protected const URL_KEY = 'lastEventsUrl';

    protected function rules($id): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                Rule::unique('events')
                    ->where(fn ($query) => $query->where('club_id', currentClubId()))
                    ->ignore($id),
            ],
            'global' => 'boolean',
        ];

        return $rules;
    }

    public function index(Request $request):Response
    {
        session([self::URL_KEY => url()->full()]);

        return inertia('Events/Index', [
            'events' => EventResource::collection(Event::query()
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(15)
                ->withQueryString()
            ),

            'options' => $request->only(['search']),
            'canCreate' => auth()->user()->can('create', Event::class),
        ]);
    }

    private function editOptions(): array
    {
        return [
            'origin' => session(self::URL_KEY),
        ];
    }

    public function create(): Response
    {
        return inertia('Events/Edit', $this->editOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->rules(-1));

        Event::create(array_merge($attributes, [
            'club_id' => currentClubId(),
        ]));

        return redirect(session(self::URL_KEY))
            ->with('success', 'Ereignis hinzugefügt');
    }

    public function edit(Event $event):Response
    {
        return inertia('Events/Edit', array_merge($this->editOptions(), [
            'event' => $event->getAttributes(),
            'deletable' => !$event->isUsed(),
        ]));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $attributes = $request->validate($this->rules($event->id));

        $event->update($attributes);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Ereignis geändert');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Ereignis gelöscht');
    }

}
