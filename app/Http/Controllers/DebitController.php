<?php

namespace App\Http\Controllers;

use App\Http\Resources\DebitResource;
use App\Models\Debit;
use App\Models\Member;
use App\Models\Subscription;
use Carbon\Carbon;
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
            'member_id' => 'required|exists:members,id',
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

            'options' => $request->only(['search']),
            'canCreate' => auth()->user()->can('create', Debit::class),
            'sepaDate' => now()->addDays(8)->format('Y-m-d'),
        ]);
    }

    private function editOptions(): array
    {
        return [
            'origin' => session(self::URL_KEY),
            'members' => Member::members()->hasAccount()->orderBy('surname')->orderBy('first_name')
                ->get(['id', 'surname', 'first_name', 'iban'])
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => "{$item->surname} {$item->first_name} ({$item->accountNumber()})",
                ]),
            'today' => now()->format('Y-m-d'),
            'varDescription' => Subscription::VAR_DESCRIPTION,
        ];
    }

    public function create(Request $request): Response
    {
        return inertia('Debits/Edit', $this->editOptions());
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
        return inertia('Debits/Edit', array_merge($this->editOptions(), [
            'debit' => $debit->getAttributes(),
        ]));
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

    public function debit(Request $request): Response
    {
        $executionDate = new Carbon($request->input('date'));

        return inertia('Subscriptions/Debit',
            array_merge(Debit::debit($executionDate), [
                'origin' => session(self::URL_KEY),
            ]));
    }

}
