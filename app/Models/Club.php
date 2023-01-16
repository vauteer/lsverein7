<?php

namespace App\Models;

use App\Pdf\BlsvPdf;
use App\Models\Section;
use Carbon\Carbon;
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

    public function items(): HasMany
    {
        return $this->hasMan(Item::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function usedSections(): \Illuminate\Support\Collection
    {
        return DB::table('club_member')
            ->join('member_section', 'club_member.member_id', 'member_section.member_id')
            ->join('sections', 'sections.id', 'member_section.section_id')
            ->distinct()
            ->select('section_id as id', 'name')
            ->where('club_member.club_id', $this->id)
            ->orderBy('name')
            ->get();
    }

    public static function logoPath($stub = ''):string
    {
        return storage_path('app/public/logo') .
            DIRECTORY_SEPARATOR .
            trim($stub, DIRECTORY_SEPARATOR);
    }

    public function logoURL(): string
    {
        return ($this->logo && file_exists(self::logoPath($this->logo))) ?
            asset('storage/logo/' . $this->logo)
            : asset('images/no_logo.png');
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

    private static function getBlankStat()
    {
        $result = [];

        for ($i = 0; $i < 7; $i++) {
            $result[$i]['m'] = 0;
            $result[$i]['w'] = 0;
        }

        return $result;
    }

    private static function getStatIndex($age)
    {
        return match(true) {
            $age < 6 => 0,
            $age < 14 => 1,
            $age < 18 => 2,
            $age < 27 => 3,
            $age < 41 => 4,
            $age < 61 => 5,
            default => 6
        };
    }

    public function getBLSVStatistic(): array
    {
        // Statistik ist zum 1. Januar, Austritte zum 31.12. und Eintritte zum 1.1. werden realisiert
        $keyDate = now()->startOfYear();
        Member::$_keyDate = $keyDate;
        $year = $keyDate->year;

        $files = null;
        $stats[-1] = self::getBlankStat();

        $members = Member::members()
            ->orderBy('surname')->orderBy('first_name')
            ->get();

        foreach ($members as $member)
        {
            $gender = ($member->gender->value === 'm') ? 'm' : 'w';
            $index = self::getStatIndex($member->age);
            $stats[-1][$index][$gender] += 1;
        }

        //$totalCsv = null;
        $totalCsv = "Titel;Name;Vorname;Namenszusatz;Geschlecht;Geburtsdatum;Spartennummer\r\n";

        foreach (Section::orderBy('name')->get() as $section)
        {
            if ($section->blsv_id === null)
                continue;

            $csv = null;
            $stat = self::getBlankStat();
            $count = 0;

            $members = Member::members()->inBlsvSections($section->blsv_id)
                ->orderBy('surname')->orderBy('first_name')
                ->get();

            foreach ($members as $member)
            {
                $gender = $member->gender->value === 'm' ? 'm' : 'w';
                $line = ';' . mb_convert_encoding($member->surname, 'ISO-8859-1', 'UTF-8') . ';' .
                    mb_convert_encoding($member->first_name, 'ISO-8859-1', 'UTF-8') . ';;' .
                    $gender . ';' .
                    '"' . $member->birthday->format('d.m.y') . '";' .
                    $section->blsv_id . "\r\n";

                $csv .= $line;
                $totalCsv .= $line;

                $index = self::getStatIndex($member->age);
                $stat[$index][$gender] += 1;
                $count++;
            }

            if ($count > 0)
            {
                $stats[$section->id] = $stat;
                $stats[$section->id]['name'] = $section->name;
                $filename = "BE{$year}_{$section->name}.csv";
                $path = storage_path("downloads/{$this->id}_" . $filename);
                $handle = fopen($path, 'w');
                fputs($handle, $csv);
                fclose($handle);

                $files[] = ['name' => $section->name, 'href' => "/downloads/{$filename}"];
            }
        }

        if ($totalCsv)
        {
            $filename = "BE{$year}_Gesamt.csv";
            $path = storage_path("downloads/{$this->id}_" . $filename);
            $handle = fopen($path, 'w');
            fputs($handle, $totalCsv);
            fclose($handle);

            $files[] = ['name' => "Alle Sparten", 'href' => "/downloads/{$filename}"];
        }

        $clubName = $this->name;

        $pdf = new BlsvPdf();

        $pdfResult = $pdf->getOutput($stats, $keyDate, $clubName);

        $filename = "blsv_stat.pdf";
        $path = storage_path("downloads/{$this->id}_" . $filename);
        $handle = fopen($path, 'w');
        fputs($handle, $pdfResult);
        fclose($handle);
        $files[] = ['name' => 'Alters-Statistik', 'href' => "/downloads/{$filename}"];

        return array_reverse($files);
    }

    public function calcBlsvDebit(float $childrenDue, float $teenDue, float $adultDue): float
    {
        //Member::$_keyDate = now()->startOfYear();
        $children = $teens = $adults = 0;
        $children = Member::members()->ageRange(null, 13)->count();
        $teens = Member::members()->ageRange(14, 17)->count();
        $adults = Member::members()->ageRange(18, null)->count();

//        $members = Member::members()->get();
//
//        foreach ($members as $member) {
//            $age = $member->age;
//            if ($age < 14)
//                $children++;
//            else if ($age < 18)
//                $teens++;
//            else
//                $adults++;
//        }

        dd($children, $teens, $adults);
        return ($children * $childrenDue) + ($teens * $teenDue) + ($adults * $adultDue);
    }
}
