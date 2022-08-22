<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Section extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope('club', function (Builder $builder) {
            $builder->whereNull('club_id')
                ->orWhere('club_id', auth()->user()->club_id);
        });
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class)
            ->withPivot(['from', 'to', 'memo'])
            ->withTimeStamps();
    }

}
