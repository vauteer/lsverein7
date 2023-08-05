<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\ActionType;
use App\ClubRole;
use App\Mail\MarkDownMail;
use App\Models\Scopes\WithRoleScope;
use App\Notifications\InvoicePaid;
use App\Notifications\UserNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'admin' => 'boolean',
    ];

    protected array $clubRoles = [];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function tracings(): HasMany
    {
        return $this->hasMany(Tracing::class);
    }

    public function scopeHasClub($query, ?int $clubId = null)
    {
        if ($clubId === null)
            $clubId = currentClubId();

        $where = 'users.id in (select user_id from club_user where club_id = ?)';

        $query->whereRaw($where, [$clubId]);
    }

    public function scopeWithLastLoginAt($query)
    {
        $query->addSelect(
            [
                'last_login_at' => Tracing::select('at')
                    ->whereColumn('tracings.user_id', 'users.id')
                    ->where('action_type', ActionType::Login)
                    ->orderByDesc('at')
                    ->take(1)
            ]
        )
        ->withCasts(['last_login_at' => 'datetime']);
    }

    public function scopeWithRole($query, ?int $clubId = null)
    {
        if ($clubId === null)
            $clubId = currentClubId();

        $query->addSelect(
            [
                'role' => ClubUser::select('role')
                ->where('club_id', $clubId)
                ->whereColumn('user_id', 'users.id')
                ->take(1)
            ]
        );
    }

    public function scopeOrderByLastLogin($query)
    {
        $query->orderByDesc(Tracing::select('at')
            ->whereColumn('user_id', 'users.id')
            ->where('action_type', ActionType::Login)
            ->latest('at')
            ->take(1)
        );
    }

    public function hasAdminRights(?int $clubId = null)
    {
        return $this->clubRole($clubId) >= ClubRole::Admin->value;
    }

    public function hasAdvancedRights(?int $clubId = null)
    {
        return $this->clubRole($clubId) >= ClubRole::Advanced->value;
    }

    public function hasClubRole(ClubRole $clubRole, ?int $clubId = null)
    {
        return $this->clubRole($clubId) === $clubRole->value;
    }

    public function clubRole(?int $clubId = null)
    {
        $clubId = $clubId ?? currentClubId();

        if (!isset($this->clubRoles[$clubId])) {
            $club = $this->Clubs()->where('club_id', $clubId)->first();
            $this->clubRoles[$clubId] = $club ? $club->pivot->role : -1;
        }

        return $this->clubRoles[$clubId];
    }

    public function lastLogin(): ?Carbon
    {
        $lastLogin = $this->tracings()->actionType(ActionType::Login)->orderByDesc('at')->first();

        return $lastLogin?->at;
    }

    public function profileURL(): string
    {
        if ($this->profile_image) {
            $path = public_path('storage/profile/' . $this->profile_image);
            if (file_exists($path)) {
                return asset('storage/profile/' . $this->profile_image);
            } else {
                $this->update(['profile_image' => null]);
            }
        }

        return "https://www.gravatar.com/avatar/" .
            md5(strtolower(trim($this->email))) .
            "?d=mp&s=40";
    }

    public static function profilePath(string $stub = ''): string
    {
        return storage_path('app/public/profile') .
            DIRECTORY_SEPARATOR .
            trim($stub, DIRECTORY_SEPARATOR);
    }


    public static function removeOrphanProfileImages(): int
    {
        $count = 0;
        foreach (glob(self::profilePath('*')) as $filename) {
            $user = User::where('profile_image', basename($filename))->first();
            if ($user === null) {
                unlink($filename);
                $count++;
            }
        }

        return $count;
    }

    public function switchClub($clubId): bool
    {
        $club = $this->clubs()
            ->where('club_id', $clubId)
            ->first();

        if ($club) {
            $this->update(['club_id' => $club->id]);

            return true;
        }

        return false;
    }

    public function test()
    {
//        $this->notify(new InvoicePaid());
        $this->notify(new UserNotification('head', 'text'));
        //Mail::to($this->email)->send(new MarkDownMail());
    }

    public static function availableRoles(): array
    {
        return [
            1 => 'Basic',
            128 => 'Advanced',
            256 => 'Admin',
        ];
    }


}
