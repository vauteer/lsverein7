<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

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

    public static function used()
    {
        return DB::table('club_member')
            ->join('member_section', 'club_member.member_id', 'member_section.member_id')
            ->join('sections', 'sections.id', 'member_section.section_id')
            ->distinct()
            ->select('section_id as id', 'name')
            ->where('club_member.club_id', currentClubId())
            ->orderBy('name')
            ->get();
    }
}
