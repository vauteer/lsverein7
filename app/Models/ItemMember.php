<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ItemMember extends Pivot
{
    use HasFactory;

    protected $table = "item_member";

    protected $guarded = [];

    protected $casts = [
        'from' => 'date',
        'to' => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function range(): string
    {
        return getRange($this->from, $this->to);
    }

}
