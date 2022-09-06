<?php

namespace App\Http\Controllers;

use App\Http\Resources\DebitResource;
use App\Models\Debit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;

class DebitController extends Controller
{
    protected const URL_KEY = 'lastDebitsUrl';

    protected function validationRules($id): array
    {
        $rules = [
            'member_id' => 'required|exists:members',
            'amount' => 'numeric|min:0',
            'transfer_text' => 'required|string',
            'due_at' => 'required|date'
        ];

        return $rules;
    }


    public function index(Request $request):Response
    {
        $request->session()->put(self::URL_KEY, url()->full());
        return inertia('Debits/Index', [
            'debits' => DebitResource::collection(Debit::query()
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(15)
                ->withQueryString()
            ),

            'filters' => $request->only(['search']),
            'canCreate' => auth()->user()->can('create', Debit::class),
        ]);
    }

    public function create(Request $request): Response
    {
        return inertia('Debits/Edit', [
            'origin' => session(self::URL_KEY),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));

        Debit::create($attributes);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Lastschrift hinzugefügt');
    }

    public function edit(Request $request, Debit $debit):Response
    {
        return inertia('Debits/Edit', [
            'debit' => $debit->getAttributes(),
            'origin' => session(self::URL_KEY),
        ]);
    }

    public function update(Request $request, Debit $debit): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($debit->id));

        $debit->update($attributes);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Lastschrift geändert');
    }

    public function destroy(Request $request, Debit $debit): RedirectResponse
    {
        $debit->delete();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Lastschrift gelöscht');
    }

}
