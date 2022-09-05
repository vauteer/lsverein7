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
                ->orWhere('club_id', currentClubId());
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

    public function isInUse(): bool
    {
        return DB::table('member_section')->where('section_id', $this->id)->count() > 0;
    }

    public const BLSV_SECTIONS = [
        1 => 'Badminton',
        2 => 'Minigolf',
        3 => 'Basketball',
        4 => 'Billard',
        5 => 'Bob und Schlitten',
        6 => 'Boxen',
        7 => 'Eissport',
        8 => 'Fechten',
        9 => 'Fussball',
        10 => 'Gewichtheben und Kraftsport',
        11 => 'Golf',
        12 => 'Handball',
        13 => 'Hockey',
        14 => 'Judo',
        15 => 'Kanu',
        16 => 'Kegeln',
        17 => 'Leichtathletik',
        18 => 'Moderner Fünfkampf',
        19 => 'Motorsport',
        20 => 'Radsport',
        21 => 'Rasenkraftsport u. Tauziehen',
        22 => 'Reiten',
        23 => 'Ringen',
        24 => 'Rollsport',
        25 => 'Rudern',
        26 => 'Karate',
        27 => 'Schwimmen',
        28 => 'Segeln',
        29 => 'Skibob',
        30 => 'Ski',
        31 => 'Tanzsport',
        32 => 'Tennis',
        33 => 'Tischtennis',
        34 => 'Turnen',
        35 => 'Turnspiele',
        36 => 'Volleyball',
        37 => 'Behinderten- und Rehasport',
        39 => 'Schach',
        40 => 'Luftsport',
        41 => 'Tauchen',
        42 => 'Squash',
        43 => 'Taekwondo',
        44 => 'Gehörlose',
        45 => 'American Football',
        46 => 'Triathlon',
        47 => 'Base- und Softball',
        48 => 'Ju-Jutsu',
        49 => 'Motorwassersport',
        51 => 'Aikido',
        52 => 'Dart',
        53 => 'Bergsport',
        54 => 'Einrad',
        55 => 'Kickboxen',
        56 => 'Cheerleading',
        57 => 'Floorball',
        58 => 'Cricket',
        99 => 'Sonstige', // wohl keine offizielle BLSV-Sparte
    ];
}
