<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Club extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'blsv_member' => 'boolean',
        'sepa_date' => 'datetime',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class)
            ->withPivot(['from', 'to', 'memo'])
            ->withTimestamps()
            ->withoutGlobalScope('club')
            ->using(ClubMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['admin'])
            ->withTimestamps();
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function usedSections()
    {

        $sql = <<<EOD
SELECT DISTINCT(b.section_id) as `id`, c.name FROM club_member a
JOIN member_section b ON a.member_id = b.member_id
JOIN sections c ON c.id = b.section_id
WHERE a.club_id = $this->id ORDER BY(1)
EOD;

        return DB::select($sql);
    }

    public function usedSubscriptions()
    {

        $sql = <<<EOD
SELECT DISTINCT(a.id), a.name FROM subscriptions a
JOIN member_subscription b ON a.id = b.subscription_id
WHERE a.club_id = $this->id ORDER BY(1)
EOD;

        return DB::select($sql);
    }

    public static function logoPath($stub = ''):string
    {
        return storage_path('app/public/logo') .
            DIRECTORY_SEPARATOR .
            trim($stub, DIRECTORY_SEPARATOR);
    }

    public function logoURL(): string
    {
        return $this->logo ? asset('storage/logo/' . $this->logo) : asset('images/no_logo.png');
    }

    public static function removeOrphanLogos():int {
        $count = 0;

        foreach (glob(self::logoPath('*')) as $filename) {
            $customer = Club::where('logo', basename($filename))->first();
            if ($customer === null) {
                unlink($filename);
                $count++;
            }
        }

        return $count;
    }

    public static function displayStyles(): array {
        return [
            '1' => 'Logo + Name',
            '2' => 'Logo',
            '3' => 'Name',
        ];
    }

    public static function languages(): array {
        return [
            'de' => 'Deutsch',
            'en' => 'English',
        ];
    }

    public static function honorYears(): string {
        return '25,30,40,50,60,70,80,90';
    }
}
