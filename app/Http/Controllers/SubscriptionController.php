<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function index(Request $request):Response
    {
        $this->setLastUrl();

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
            'origin' => $this->getLastUrl(),
            'varDescription' => Subscription::VAR_DESCRIPTION,
        ];
    }

    public function create(): Response
    {
        return inertia('Subscriptions/Edit', $this->editOptions());
    }

    public function store(SubscriptionRequest $request): RedirectResponse
    {
        $attributes = $request->validated();

        Subscription::create(array_merge($attributes, ['club_id' => currentClubId()]));

        return redirect($this->getLastUrl())
            ->with('success', 'Funktion hinzugefügt');
    }

    public function edit(Subscription $subscription):Response
    {
        return inertia('Subscriptions/Edit', array_merge($this->editOptions(), [
            'subscription' => $subscription->getAttributes(),
            'deletable' => !$subscription->isUsed(),
        ]));
    }

    public function update(SubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        $attributes = $request->validated();

        $subscription->update($attributes);

        return redirect($this->getLastUrl())
            ->with('success', 'Beitrag geändert');
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return redirect($this->getLastUrl())
            ->with('success', 'Beitrag gelöscht');
    }

    public function debit(Request $request): Response
    {
        $subscriptions = $request->input('subscriptions');
        $executionDate = new Carbon($request->input('date'));

        return inertia('Subscriptions/Debit',
            array_merge(Subscription::debit($subscriptions, $executionDate), [
            'origin' => $this->getLastUrl(),
        ]));
    }
}
