<?php

namespace App\Models;

use App\Gender;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
    use HasFactory;

    const EXPORT_FORMATS = [
        ['id' => 'pdf', 'name' => 'PDF'],
        ['id' => 'vcf', 'name' => 'vCard'],
        ['id' => 'csv', 'name' => 'CSV'],
    ];

    protected $guarded = [];

    protected $casts = [
        'birthday' => 'date',
        'death_day' => 'date',
        'gender' => Gender::class,
    ];

    protected static function booted()
    {
        static::addGlobalScope('club', function (Builder $builder) {
            $builder->where('club_id', currentClubId());
        });
    }

    protected $appends = ['age'];
    public static ?Carbon $_keyDate = null;

    public static function getKeyDate(): Carbon
    {
        if (static::$_keyDate === null)
            static::$_keyDate = now()->endOfDay();

        return static::$_keyDate->copy();
    }

//    public function getAgeAttribute()
//    {
//        return now()->diffInYears($this->birthday);
//    }
//
    public function age(): Attribute
    {
        return new Attribute(
            get: function () {
                $keyDate = $this->gone() ? $this->death_day : self::getKeyDate() ?? now();
                return $this->birthday->diffInYears($keyDate);
            },
        );
    }

    public function entry(): Carbon
    {
        $entry = null;

        foreach ($this->memberships as $membership) {
            $entry = $membership->pivot->from->min($entry);
        }

        return $entry;
    }

    public function dueHonor(): int
    {
        $years = $this->membershipYears();
        return in_array($years, explode(',', currentClub()->honor_years)) ? $years : 0;
    }

        public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function memberships(): BelongsToMany
    {
        return $this->belongsToMany(Club::class)
            ->withPivot(['id', 'from', 'to', 'memo'])
            ->withTimestamps()
            ->using(ClubMember::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot(['id', 'date', 'memo'])
            ->withTimestamps()
            ->orderBy('pivot_date', 'desc')
            ->using(EventMember::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)
            ->withPivot(['id', 'memo', 'from', 'to'])
            ->withTimestamps()
            ->orderBy('pivot_from', 'desc')
            ->using(ItemMember::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withPivot(['id', 'from', 'to', 'memo'])
            ->withTimestamps()
            ->using(MemberRole::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class)
            ->withPivot(['id', 'from', 'to', 'memo'])
            ->withTimestamps()
            ->using(MemberSection::class);
    }

    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(Subscription::class)
            ->withPivot(['id', 'memo'])
            ->withTimestamps()
            ->using(MemberSubscription::class);
    }

    public function born()
    {
        return inRange($this->birthday, null, self::getKeyDate());
    }

    public function gone()
    {
        return inRange($this->death_day, null, self::getKeyDate());
    }

    public function alive()
    {
        return $this->born() && !$this->gone();
    }

    public function fullName(): string
    {
        return $this->first_name . ' ' . $this->surname;
    }

    public function accountNumber(): string
    {
        return ltrim(str_replace(' ', '', substr($this->iban, -12)), "0");
    }

    public function isMember()
    {
        if (!$this->alive())
            return false;

        $keyDate = self::getKeyDate();

        foreach ($this->memberships as $membership) {
            if (inRange($keyDate, $membership->pivot->from, $membership->pivot->to))
                return true;
        }

        return false;
    }

    public function membershipYears()
    {
        $keyDate = self::getKeyDate()->min($this->death_day);
        $years = 0;

        foreach ($this->memberships as $membership) {
            $pivot = $membership->pivot;
            if ($pivot->from >= $keyDate)
                return 0;

            $to = $keyDate->min($pivot->to);

            // roughly calculation
            $years += $to->year - $pivot->from->year;
        }

        return $years;
    }

    public function lastEvent()
    {
        $eventName = $this->events->first()?->name;

        return $eventName;
    }

    public function currentSections()
    {
        $keyDate = self::getKeyDate();
        $sections = [];

        foreach ($this->sections as $section) {
            if (inRange($keyDate, $section->pivot->from, $section->pivot->to)) {
                $sections[] = $section->name;
            }
        }

        return join('|', $sections);
    }

    public function currentRoles()
    {
        $keyDate = self::getKeyDate();
        $roles = [];

        foreach ($this->roles as $role) {
            if (inRange($keyDate, $role->pivot->from, $role->pivot->to)) {
                $roles[] = $role->name;
            }
        }

        return join('|', $roles);
    }

    public function currentSubscriptions()
    {
        $subscriptions = [];

        foreach ($this->subscriptions as $subscription) {
            $subscriptions[] = $subscription->name;
        }

        return join('|', $subscriptions);
    }

    public static function memberIds(?Carbon $keyDate = null,)
    {
        if ($keyDate === null)
            $keyDate = self::getKeyDate();

        return DB::table('members')->join('club_member', 'members.id', '=', 'club_member.member_id')
            ->where('members.club_id', currentClubId())
            ->where(function ($query) use ($keyDate) {
                $query->whereNull('members.death_day')->orWhere('members.death_day', '>', $keyDate);
            })
            ->where('club_member.from', '<=', $keyDate)
            ->where(function ($query) use ($keyDate) {
                $query->whereNull('club_member.to')->orWhere('club_member.to', '>=', $keyDate);
            })
            ->pluck('members.id');
    }

    public function scopeMembers($query, ?Carbon $keyDate = null, bool $isMember = true)
    {
        $query->whereIn('id', self::memberIds($keyDate));
    }

    public function scopeNoMembers($query, ?Carbon $keyDate = null)
    {
        $query->whereNotIn('id', self::memberIds($keyDate));
    }

    public function scopeInSections($query, array|int $sections, ?Carbon $keyDate = null)
    {
        if ($keyDate === null)
            $keyDate = self::getKeyDate();

        $sections = Arr::wrap($sections);

        $query->whereIn('id', DB::table('members')
            ->join('member_section', 'members.id', '=', 'member_section.member_id')
            ->where('members.club_id', currentClubId())
            ->where(function ($query) use ($keyDate) {
                $query->whereNull('members.death_day')->orWhere('members.death_day', '>', $keyDate);
            })
            ->whereIn('member_section.section_id', $sections)
            ->where('member_section.from', '<=', $keyDate)
            ->where(function ($query) use ($keyDate) {
                $query->whereNull('member_section.to')->orWhere('member_section.to', '>=', $keyDate);
            })
            ->pluck('members.id')
        );
    }

    public function scopeInBlsvSections($query, array|int $sections, ?Carbon $keyDate = null)
    {
        if ($keyDate === null)
            $keyDate = self::getKeyDate();

        $sections = Arr::wrap($sections);

        $query->whereIn('id', DB::table('members')
            ->join('member_section', 'members.id', '=', 'member_section.member_id')
            ->join('sections', 'sections.id', '=', 'member_section.section_id')
            ->where('members.club_id', currentClubId())
            ->where(function ($query) use ($keyDate) {
                $query->whereNull('members.death_day')->orWhere('members.death_day', '>', $keyDate);
            })
            ->whereIn('sections.blsv_id', $sections)
            ->where('member_section.from', '<=', $keyDate)
            ->where(function ($query) use ($keyDate) {
                $query->whereNull('member_section.to')->orWhere('member_section.to', '>=', $keyDate);
            })
            ->pluck('members.id')
        );
    }

    public function scopeAgeRange($query, ?int $from, ?int $to)
    {
        if ($to !== null) {
            // geboren morgen vor ($to + 1) Jahren ab 00:00
            $query->where('birthday', '>=', self::getKeyDate()->addDay()->subYears($to + 1)->startOfDay());
        }
        if ($from !== null) {
            // geboren vor $from Jahren bis 23:59
            $query->where('birthday', '<=', self::getKeyDate()->subYears($from)->endOfDay());
        }
    }

    public function scopeMilestoneBirthdays($query, ?int $year = null)
    {
        if ($year === null)
            $year = self::getKeyDate()->year;

        $query->whereRaw('? - YEAR(birthday) in (50,60,70,80,90,100)', [$year]);
    }

    public function scopeJoined($query, ?int $year = null)
    {
        if ($year === null)
            $year = self::getKeyDate()->year;

        $query->whereIn('id', ClubMember::whereRaw('YEAR(`from`) = ?', [$year])
            ->pluck('member_id'));
    }

    public function scopeRetired($query, ?int $year = null)
    {
        if ($year === null)
            $year = self::getKeyDate()->year;

        $query->whereIn('id', ClubMember::whereRaw('YEAR(`to`) = ?', $year)
            ->pluck('member_id'));
    }

    public function scopeDead($query, ?int $year = null)
    {
        if ($year === null)
            $year = self::getKeyDate()->year;

        $query->whereRaw('(YEAR(`death_day`) = ?)', [$year]);
    }

    public function scopePaymentMethods($query, array|string $methods)
    {
        $methods = Arr::wrap($methods);

        $query->whereIn('payment_method', $methods);
    }

    public function scopeHasAccount($query)
    {
        $query->where('iban', '<>', '');  // schließt wohl auch NULL aus
    }

    public function scopeHadEvent($query, int|null $id = null, ?Carbon $keyDate = null)
    {
        if ($keyDate === null)
            $keyDate = self::getKeyDate();

        $query->whereIn('id', EventMember::where('date', '<', $keyDate)
            ->when($id, function ($query, $id) {
                $query->where('event_id', $id);
            })->pluck('member_id')
        );
    }

    public function scopeHasRole($query, int|null $id = null, ?Carbon $keyDate = null)
    {
        if ($keyDate === null)
            $keyDate = self::getKeyDate();

        $query->whereIn('id', MemberRole::where('from', '<', $keyDate)
            ->where(function ($query) use ($keyDate) {
                $query->whereNull('to')->orWhere('to', '>', $keyDate);
            })
            ->when($id, function ($query, $id) {
                $query->where('role_id', $id);
            })
            ->pluck('member_id'));
    }

    public function scopeEverRole($query, int|null $id = null, ?Carbon $keyDate = null)
    {
        if ($keyDate === null)
            $keyDate = self::getKeyDate();

        $query->whereIn('id', MemberRole::where('from', '<', $keyDate)
            ->when($id, function ($query, $id) {
                $query->where('role_id', $id);
            })->pluck('member_id'));
    }

    public function scopeHasItem($query, int|null $id = null, ?Carbon $keyDate = null)
    {
        if ($keyDate === null)
            $keyDate = self::getKeyDate();

        $query->whereIn('id', ItemMember::where('from', '<', $keyDate)
            ->where(function ($query) use ($keyDate) {
                $query->whereNull('to')->orWhere('to', '>', $keyDate);
            })
            ->when($id, function ($query, $id) {
                $query->where('item_id', $id);
            })
            ->pluck('member_id'));
    }

    public function scopeEverItem($query, int|null $id = null, ?Carbon $keyDate = null)
    {
        if ($keyDate === null)
            $keyDate = self::getKeyDate();

        $query->whereIn('id', ItemMember::where('from', '<', $keyDate)
            ->when($id, function ($query, $id) {
                $query->where('item_id', $id);
            })->pluck('member_id'));
    }

    public function scopeHasSubscription($query, null|string|array $subscriptionTypes = null)
    {
        $query->whereIn('id', MemberSubscription::when($subscriptionTypes,
            function ($query, $subscriptionTypes) {
                $subscriptionTypes = Arr::wrap($subscriptionTypes);
                $query->whereIn('subscription_id', $subscriptionTypes);
            })->pluck('member_id'));
    }

    public function scopeNoSubscription($query, null|string|array $subscriptionTypes = null)
    {
        $query->whereNotIn('id', MemberSubscription::when($subscriptionTypes,
            function ($query, $subscriptionTypes) {
                $subscriptionTypes = Arr::wrap($subscriptionTypes);
                $query->whereIn('subscription_id', $subscriptionTypes);
            })->pluck('member_id'));
    }

    public function scopeDueHonor($query, ?Carbon $keyDate = null)
    {
        if ($keyDate === null)
            $keyDate = self::getKeyDate();

        $honorYears = currentClub()->honor_years;
        if ($honorYears === null)
            return;

        $query->whereIn('id',
            ClubMember::groupBy('member_id') // add if member has several memberships
            ->havingRaw("SUM(YEAR(LEAST(IFNULL(`to`, ?), ?)) - YEAR(`from`)) IN ($honorYears)",
                [$keyDate, $keyDate])->pluck('member_id'));
    }

    public function scopeLike($query, string $like)
    {
        $like = '%' . $like . '%';
        $query->where(function ($query) use ($like) {
            $query->where('first_name', 'like', $like)
                ->orWhere('surname', 'like', $like)
                ->orWhere('street', 'like', $like)
                ->orWhere('zipcode', 'like', $like)
                ->orWhere('city', 'like', $like)
                ->orWhere('memo', 'like', $like);
        });
    }

    public static function availableGenders(): array
    {
        return [
            'f' => 'Frau',
            'm' => 'Mann',
        ];
    }

    public static function availablePaymentMethods(): array
    {
        return [
            'k' => 'Konto',
            'r' => 'Rechnung',
            'n' => 'Nichtzahler',
        ];
    }
}
