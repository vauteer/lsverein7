<?php

namespace App\Models;

use App\Gender;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Member extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'birthday' => 'date',
        'death_day' => 'date',
        'gender' => Gender::class,
    ];

    protected $appends = ['age'];
    public static $key_date = null;

//    public function getAgeAttribute()
//    {
//        return Carbon::now()->diffInYears($this->birthday);
//    }
//
    protected function age(): Attribute
    {
        return new Attribute(
            get: function() { return $this->birthday->diffInYears(self::$key_date ?? Carbon::now()); },
        );
    }

    protected static function booted()
    {
        static::addGlobalScope('club', function (Builder $builder) {
            $builder->where('club_id', auth()->user()->club_id);
        });
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function memberships(): BelongsToMany
    {
        return $this->belongsToMany(Club::class)
            ->withPivot(['id', 'from', 'to', 'memo'])
            ->withTimestamps();
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot(['id', 'date', 'memo'])
            ->withTimestamps();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withPivot(['id', 'from', 'to', 'memo'])
            ->withTimestamps();
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class)
            ->withPivot(['id', 'from', 'to', 'memo'])
            ->withTimestamps();
    }

    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(Subscription::class)
            ->withPivot(['id', 'memo'])
            ->withTimestamps();
    }

    public static function genders()
    {
        return [
            'f' => 'Frau',
            'm' => 'Mann',
        ];
    }

    public static function paymentMethods()
    {
        return [
            'k' => 'Konto',
            'r' => 'Rechnung',
            'n' => 'Nichtzahler',
        ];
    }

    public static function getRange(string $from, ?string $to): string
    {
        $from = new Carbon($from);
        $range = $from->format('d.m.Y') . '-';
        if ($to !== null) {
            $to = new Carbon($to);
            $range .= $to->format('d.m.Y');
        }

        return $range;
    }
}
