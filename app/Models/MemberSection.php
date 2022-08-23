<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberSection extends Model
{
    use HasFactory;

    protected $table = "member_section";

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
        return Member::getRange($this->from, $this->to);
    }


}
