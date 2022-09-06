<?php

namespace App\Models;

use App\ActionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tracing extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'at' => 'datetime',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActionType($query, ActionType $actionType)
    {
        $query->where('action_type', $actionType);
    }
}
