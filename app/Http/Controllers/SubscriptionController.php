<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubscriptionResource;
use App\Models\Member;
use App\Models\Subscription;
use App\Pdf\SepaPdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;

class SubscriptionController extends Controller
{
    protected const URL_KEY = 'lastSubscriptionsUrl';

    protected function rules($id): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                Rule::unique('subscriptions')
                    ->where(fn ($query) => $query->where('club_id', currentClubId()))
                    ->ignore($id),
            ],
            'amount' => 'numeric|min:0',
            'transfer_text' => 'required|string',
            'memo' => 'nullable|string',
        ];

        return $rules;
    }

    public function index(Request $request):Response
    {
        session([self::URL_KEY => url()->full()]);

        return inertia('Subscriptions/Index', [
            'subscriptions' => SubscriptionResource::collection(Subscription::query()
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->orderBy('amount')
                ->paginate(15)
                ->withQueryString()
            ),

            'options' => $request->only(['search']),
            'canCreate' => auth()->user()->can('create', Subscription::class),
            'sepaDate' => now()->addDays(8)->format('Y-m-d'),
        ]);
    }

    private function editOptions(): array
    {
        return [
            'origin' => session(self::URL_KEY),
            'varDescription' => Subscription::VAR_DESCRIPTION,
        ];
    }

    public function create(): Response
    {
        return inertia('Subscriptions/Edit', $this->editOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->rules(-1));

        Subscription::create(array_merge($attributes, ['club_id' => currentClubId()]));

        return redirect(session(self::URL_KEY))
            ->with('success', 'Funktion hinzugefügt');
    }

    public function edit(Subscription $subscription):Response
    {
        return inertia('Subscriptions/Edit', array_merge($this->editOptions(), [
            'subscription' => $subscription->getAttributes(),
            'deletable' => !$subscription->isUsed(),
        ]));
    }

    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $attributes = $request->validate($this->rules($subscription->id));

        $subscription->update($attributes);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Beitrag geändert');
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Beitrag gelöscht');
    }

    public function debit(Request $request): Response
    {
        $subscriptions = $request->input('subscriptions');
        $executionDate = new Carbon($request->input('date'));

        return inertia('Subscriptions/Debit',
            array_merge(Subscription::debit($subscriptions, $executionDate), [
            'origin' => session(self::URL_KEY),
        ]));
    }
}
