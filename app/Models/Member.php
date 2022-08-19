<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'birthday' => 'datetime',
        'death_day' => 'datetime',
    ];

    public function Club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function Events(): HasMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot(['at', 'memo'])
            ->withTimestamps();
    }

    public function Roles(): HasMany
    {
        return $this->belongsToMany(Role::class)
            ->withPivot(['from', 'to', 'memo'])
            ->withTimestamps();
    }

    public function Sections(): HasMany
    {
        return $this->belongsToMany(Section::class)
            ->withPivot(['from', 'to', 'memo'])
            ->withTimestamps();
    }

    public function Subscriptions(): HasMany
    {
        return $this->belongsToMany(Subscription::class)
            ->withPivot(['memo'])
            ->withTimestamps();
    }

}
