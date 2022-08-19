<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function Club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function Members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class)
            ->withPivot(['at', 'memo'])
            ->withTimestamps();
    }

}
