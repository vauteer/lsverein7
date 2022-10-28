<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ItemController extends Controller
{
    public function index(Request $request):Response
    {
        $this->setLastUrl();

        return inertia('Items/Index', [
            'items' => ItemResource::collection(Item::query()
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(15)
                ->withQueryString()
            ),

            'options' => $request->only(['search']),
            'canCreate' => auth()->user()->can('create', Item::class),
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
        return inertia('Items/Edit', $this->editOptions());
    }

    public function store(ItemRequest $request): RedirectResponse
    {
        $attributes = $request->validated();

        Item::create(array_merge($attributes, ['club_id' => currentClubId()]));

        return redirect($this->getLastUrl())
            ->with('success', 'Ereignis hinzugefügt');
    }

    public function edit(Item $item):Response
    {
        return inertia('Items/Edit', array_merge($this->editOptions(), [
            'item' => $item->getAttributes(),
            'deletable' => !$item->isUsed(),
        ]));
    }

    public function update(ItemRequest $request, Item $item): RedirectResponse
    {
        $attributes = $request->validated();

        $item->update($attributes);

        return redirect($this->getLastUrl())
            ->with('success', 'Ereignis geändert');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        return redirect($this->getLastUrl())
            ->with('success', 'Ereignis gelöscht');
    }

}
