<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClubMember extends Pivot
{
    use HasFactory;

    protected $table = "club_member";
    public $incrementing = true;

    protected $guarded = [];

    protected $casts = [
        'from' => 'date',
        'to' => 'date',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function range(): string
    {
        return getRange($this->from, $this->to, 'm.Y');
    }
}
