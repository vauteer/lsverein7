<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;

class SubscriptionController extends Controller
{
    protected function validationRules($id): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                Rule::unique('subscriptions')
                    ->where(fn ($query) => $query->where('club_id', auth()->user()->club_id))
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
        return inertia('Subscriptions/Index', [
            'subscriptions' => SubscriptionResource::collection(Subscription::query()
                ->when($request->input('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->orderBy('amount')
                ->paginate(15)
                ->withQueryString()
            ),

            'filters' => $request->only(['search']),
            'canCreate' => auth()->user()->can('create', Subscription::class),
        ]);
    }

    public function create(Request $request): Response
    {
        return inertia('Subscriptions/Edit');
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));

        Subscription::create(array_merge($attributes, ['club_id' => auth()->user()->club_id]));

        return redirect()->route('subscriptions')
            ->with('success', 'Funktion hinzugefügt');
    }

    public function edit(Request $request, Subscription $subscription):Response
    {
        return inertia('Subscriptions/Edit', [
            'subscription' => [
                'id' => $subscription->id,
                'name' => $subscription->name,
                'amount' => $subscription->amount,
                'transfer_text' => $subscription->transfer_text,
                'memo' => $subscription->memo,
            ],
        ]);
    }

    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($subscription->id));

        $subscription->update($attributes);

        return redirect()->route('subscriptions')
            ->with('success', 'Beitrag geändert');
    }

    public function destroy(Request $request, Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return redirect()->route('subscriptions')
            ->with('success', 'Beitrag gelöscht');
    }

}
