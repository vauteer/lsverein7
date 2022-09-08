<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'due_at' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeDue($query, ?Carbon $date)
    {
        if ($date === null)
            $date = now()->endOfDay();

        $query->where('due_at', '<=', $date);
    }

    public static function debit(Carbon $executionDate): array
    {
        $debits = [];

        foreach (Debit::due($executionDate)->get() as $debit) {
            $debits[] = [
                'member_id' => $debit->member_id,
                'amount' => $debit->amount,
                'transfer_text' => $debit->transfer_text,
            ];
        }

        Debit::due($executionDate)->delete();

        return [
            'downloads' => Subscription::generateSepa($debits, $executionDate),
        ];
    }

}
