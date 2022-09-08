<?php

namespace App\Models;

use App\Pdf\SepaPdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class Subscription extends Model
{
    use HasFactory;

    public const VAR_DESCRIPTION = "Variablen: <AJ> Jahr, <VN> Vorname, <NN> Nachname";

    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope('club', function (Builder $builder) {
            $builder->where('club_id', currentClubId());
        });
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class)
            ->withPivot(['memo'])
            ->withTimestamps();
    }

    public function isInUse(): bool
    {
        return DB::table('member_subscription')->where('subscription_id', $this->id)->count() > 0;
    }

    public function __toString()
    {
        $amount = number_format($this->amount, 2, ',');
        return "{$this->name} ($amount €)";
    }

    public static function debit(array $subscriptions, Carbon $executionDate): array
    {
        $paymentMethods = Member::availablePaymentMethods();

        $debits = [];
        $outStandings = [];

        $members = Member::members()
            ->hasSubscription($subscriptions)
            ->orderBy('surname')->orderBy('first_name')
            ->get();

        foreach ($members as $member) {
            foreach ($member->subscriptions as $subscription) {
                if (!in_array($subscription->id, $subscriptions))
                    continue;

                if ($member->payment_method === 'k') {
                    $debits[] = [
                        'member_id' => $member->id,
                        'amount' => $subscription->amount,
                        'transfer_text' => $subscription->transfer_text,
                    ];
                }
                else {
                    $outStandings[] = [
                        'name' => $member->first_name . ' ' . $member->surname,
                        'subscription' => $subscription->__toString(),
                        'paymentMethod' => $paymentMethods[$member->payment_method],
                    ];
                }
            }
        }

        return [
            'outStandings' => $outStandings,
            'downloads' => self::generateSepa($debits, $executionDate),
        ];
    }

    public static function generateSepa(array $debits, Carbon $executionDate): array
    {
        $creationDate = now();
        $year = $executionDate->year;
        $club = currentClub();
        $defaultDate = $club->sepa_date;
        $payments = [];
        $totalAmount = 0.0;
        $data['msgId'] = 'M' . $creationDate->format('YmdHis');
        $data['pmtInfId'] = 'P' . $creationDate->format('YmdHis');
        $data['creDtTm'] = substr($creationDate->toISO8601String(), 0, 19);
        $data['reqdColltnDt'] = $executionDate->format('Y-m-d');
        $data['nm'] = $club->name;
        $data['iban'] = str_replace(" ", "", $club->iban);
        $data['bic'] = $club->bic;
        $data['sepaId'] = str_replace(" ", "", $club->sepa);

        foreach ($debits as $debit) {
            $member = Member::find($debit['member_id']);
            $totalAmount += $debit['amount'];
            $transferText = str_replace(['<AJ>', '<VN>', '<NN>'], [$year, $member->first_name, $member->surname],
                $debit['transfer_text']);
            $dateOfSignature = $defaultDate->max($member->entry());

            $payments[] = [
                'nm' =>$member->account_owner,
                'iban' => str_replace(" ", "", $member->iban),
                'bic' => $member->bic,
                'amount' => $debit['amount'],
                'instdAmt' => sprintf('%01.2f', $debit['amount']),
                'ustrd' => $transferText,
                'mndtId' => $member->id,
                'dtOfSgntr' => $dateOfSignature->format('Y-m-d'),
            ];
        }

        $data['nbOfTxs'] = count($payments);
        $data['ctrlSum'] = sprintf('%01.2f', $totalAmount);
        $data['payments'] = $payments;

        $sepaName = "sepa.xml";
        $sepaPath = storage_path("downloads/{$club->id}_" . $sepaName);
        $data['header'] = '<?xml version="1.0" encoding="utf-8"?>'; // <? Würde in view als PHP gewertet !
        $sepaData = view('sepaxml', $data)->render();

        file_put_contents($sepaPath, $sepaData);

        $pdf = new SepaPdf();

        $pdfName = "Abbuchungen.pdf";
        $pdfPath = storage_path("downloads/{$club->id}_" . $pdfName);
        file_put_contents($pdfPath, $pdf->getOutput($payments, 'Sepa-Bankeinzug', $club->name));

        return [
            0 => ['name' => 'Sepa-Datei', 'href' => "/downloads/{$sepaName}"],
            1 => ['name' => 'Begleitzettel', 'href' => "/downloads/{$pdfName}"],
        ];
    }

}
