<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'blsv_member' => 'boolean',
        'sepa_date' => 'datetime',
    ];

    public function Members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class)
            ->withPivot(['from', 'to', 'memo'])
            ->withTimestamps();
    }

    public function Users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['admin'])
            ->withTimestamps();
    }

    public function Events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function Roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function Subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
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
}
