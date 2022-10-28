<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class EventController extends Controller
{
    public function index(Request $request):Response
    {
        $this->setLastUrl();

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
            'origin' => $this->getLastUrl(),
        ];
    }

    public function create(): Response
    {
        return inertia('Events/Edit', $this->editOptions());
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $attributes = $request->validated();

        Event::create(array_merge($attributes, [
            'club_id' => currentClubId(),
        ]));

        return redirect($this->getLastUrl())
            ->with('success', 'Ereignis hinzugefügt');
    }

    public function edit(Event $event):Response
    {
        return inertia('Events/Edit', array_merge($this->editOptions(), [
            'event' => $event->getAttributes(),
            'deletable' => !$event->isUsed(),
        ]));
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $attributes = $request->validated();

        $event->update($attributes);

        return redirect($this->getLastUrl())
            ->with('success', 'Ereignis geändert');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect($this->getLastUrl())
            ->with('success', 'Ereignis gelöscht');
    }

}
