<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MemberSection extends Pivot
{
    use HasFactory;

    protected $table = "member_section";
    public $incrementing = true;

    protected $guarded = [];

    protected $casts = [
        'from' => 'date',
        'to' => 'date',
    ];

    public function member():BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function range(): string
    {
        return getRange($this->from, $this->to, 'm.Y');
    }


}
