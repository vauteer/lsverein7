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
        $request->session()->put(self::URL_KEY, url()->full());
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
            'sepaDate' => Carbon::now()->addDays(8)->format('Y-m-d'),
        ]);
    }

    public function create(Request $request): Response
    {
        return inertia('Subscriptions/Edit', [
            'origin' => session(self::URL_KEY),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules(-1));

        Subscription::create(array_merge($attributes, ['club_id' => auth()->user()->club_id]));

        return redirect(session(self::URL_KEY))
            ->with('success', 'Funktion hinzugefügt');
    }

    public function edit(Request $request, Subscription $subscription):Response
    {
        return inertia('Subscriptions/Edit', [
            'subscription' => $subscription->getAttributes(),
            'deletable' => !$subscription->isInUse(),
            'origin' => session(self::URL_KEY),
        ]);
    }

    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $attributes = $request->validate($this->validationRules($subscription->id));

        $subscription->update($attributes);

        return redirect(session(self::URL_KEY))
            ->with('success', 'Beitrag geändert');
    }

    public function destroy(Request $request, Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return redirect(session(self::URL_KEY))
            ->with('success', 'Beitrag gelöscht');
    }

    public function debit(Request $request): Response
    {
        $paymentMethods = Member::availablePaymentMethods();
        $subscriptions = $request->input('subscriptions');
        $executionDate = new Carbon($request->input('date'));
        $creationDate = Carbon::now();
        $year = $executionDate->year;
        $club = currentClub();
        $defaultDate = $club->sepa_date;
        $debits = [];
        $outStandings = [];
        $totalAmount = 0.0;
        $data['msgId'] = 'M' . $creationDate->format('YmdHis');
        $data['pmtInfId'] = 'P' . $creationDate->format('YmdHis');
        $data['creDtTm'] = substr($creationDate->toISO8601String(), 0, 19);
        $data['reqdColltnDt'] = $executionDate->format('Y-m-d');
        $data['nm'] = $club->name;
        $data['iban'] = str_replace(" ", "", $club->iban);
        $data['bic'] = $club->bic;
        $data['sepaId'] = str_replace(" ", "", $club->sepa);

        $members = Member::members()
            ->hasSubscription($subscriptions)
            ->orderBy('surname')->orderBy('first_name')
            ->get();

        foreach ($members as $member) {
            foreach ($member->subscriptions as $subscription) {
                if (!in_array($subscription->id, $subscriptions))
                    continue;

                if ($member->payment_method !== 'k') {
                    $outStandings[] = [
                        'name' => $member->first_name . ' ' . $member->surname,
                        'subscription' => $subscription->__toString(),
                        'paymentMethod' => $paymentMethods[$member->payment_method],
                    ];
                    continue;
                }

                $totalAmount += $subscription->amount;
                $transferText = str_replace(['<AJ>', '<VN>', '<NN>'], [$year, $member->first_name, $member->surname],
                    $subscription->transfer_text);
                $dateOfSignature = $defaultDate->max($member->entry());

                $debits[] = [
                    'nm' =>$member->account_owner,
                    'iban' => str_replace(" ", "", $member->iban),
                    'bic' => $member->bic,
                    'amount' => $subscription->amount,
                    'instdAmt' => sprintf('%01.2f', $subscription->amount),
                    'ustrd' => $transferText,
                    'mndtId' => $member->id,
                    'dtOfSgntr' => $dateOfSignature->format('Y-m-d'),
                ];
            }
        }

        $data['nbOfTxs'] = count($debits);
        $data['ctrlSum'] = sprintf('%01.2f', $totalAmount);
        $data['payments'] = $debits;
        $sepaSubPath = 'storage/downloads/beitraege_sepa.xml';
        $filename = public_path($sepaSubPath);
        $data['header'] = '<?xml version="1.0" encoding="utf-8"?>'; // <? Würde in view als PHP gewertet !
        $sepaData = view('sepaxml', $data)->render();

        file_put_contents($filename, $sepaData);

        $pdf = new SepaPdf();

        $pdfSubPath = 'storage/downloads/beitraege.pdf';
        file_put_contents(public_path($pdfSubPath), $pdf->getOutput($debits, 'Sepa-Bankeinzug', $club->name));

        return inertia('Subscriptions/Debit', [
            'title' => 'Downloads für SEPA-Bankeinzug',
            'downloads' => [
                0 => ['name' => 'Sepa-Datei', 'href' => asset($sepaSubPath)],
                1 => ['name' => 'Begleitzettel', 'href' => asset($pdfSubPath)],
            ],
            'outStandings' => $outStandings,
        ]);
    }
}
