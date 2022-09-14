<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;

class ItemController extends Controller
{
    protected const URL_KEY = 'lastItemsUrl';

    protected function validationRules($id): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                Rule::unique('items')
                    ->where(fn ($query) => $query->where('club_id', currentClubId()))
                    ->ignore($id),
            ],
        ];

        return $rules;
    }


    public function index(Request $request):Response
    {
        $request->session()->put(self::URL_KEY, url()->full());
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
            'origin' => session(self::URL_KEY),
        ];
    }

    public function create(Request $request): Response
    {
        return inertia('Items/Edit', $this->editOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));

        Item::create(array_merge($attributes, ['club_id' => currentClubId()]));

        return redirect(session(self::URL_KEY))
            ->with('success', 'Ereignis hinzugefügt');
    }

    public function edit(Request $request, Item $item):Response
    {
        return inertia('Items/Edit', array_merge($this->editOptions(), [
            'item' => $item->getAttributes(),
            'deletable' => !$item->isUsed(),
        ]));
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($item->id));

        $item->update($attributes);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Ereignis geändert');
    }

    public function destroy(Request $request, Item $item): RedirectResponse
    {
        $item->delete();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Ereignis gelöscht');
    }

}
