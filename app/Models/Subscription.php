<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Subscription extends Model
{
    use HasFactory;

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

    public static function used()
    {
        return DB::table('subscriptions')
            ->join('member_subscription', 'subscriptions.id', 'member_subscription.subscription_id')
            ->select('subscription_id as id', 'name', 'amount') // amount needed for orderBy
            ->where('club_id', currentClubId())
            ->orderBy('amount')
            ->distinct()
            ->get();
    }

    public function __toString()
    {
        $amount = number_format($this->amount, 2, ',');
        return "{$this->name} ($amount €)";
    }
}
