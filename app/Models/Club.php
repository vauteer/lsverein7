<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'blsv_member' => 'boolean',
        'sepa_date' => 'datetime',
    ];

    public function Members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class)
            ->withPivot(['from', 'to', 'memo'])
            ->withTimestamps();
    }

    public function Users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function Events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function Roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function Subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
