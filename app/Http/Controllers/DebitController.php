<?php

namespace App\Http\Controllers;

use App\Http\Requests\DebitRequest;
use App\Http\Resources\DebitResource;
use App\Models\Debit;
use App\Models\Member;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class DebitController extends Controller
{
    protected const URL_KEY = 'lastDebitsUrl';

    public function index(Request $request):Response
    {
        session([self::URL_KEY => url()->full()]);
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

    public function create(): Response
    {
        return inertia('Debits/Edit', $this->editOptions());
    }

    public function store(DebitRequest $request): RedirectResponse
    {
        $attributes = $request->validated();

        Debit::create($attributes);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Lastschrift hinzugefügt');
    }

    public function edit(Debit $debit):Response
    {
        return inertia('Debits/Edit', array_merge($this->editOptions(), [
            'debit' => $debit->getAttributes(),
        ]));
    }

    public function update(DebitRequest $request, Debit $debit): RedirectResponse
    {
        $attributes = $request->validated();

        $debit->update($attributes);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Lastschrift geändert');
    }

    public function destroy(Debit $debit): RedirectResponse
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
