<?php

namespace App\Models;

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

    public function scopeDue($query)
    {
        $query->where('due_at', '<', now()->endOfDay());
    }
}
