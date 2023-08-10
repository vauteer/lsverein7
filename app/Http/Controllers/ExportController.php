<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\ClubUser;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Item;
use App\Models\ItemMember;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberSection;
use App\Models\MemberSubscription;
use App\Models\Role;
use App\Models\Section;
use App\Models\Subscription;
use App\Models\User;
use App\SqlConverter;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function exportClub()
    {
        $club = currentClub();
        $path = storage_path('exports/' . 'export_club_' . $club->id . '.sql');

        $handle = fopen($path, 'w');
        set_time_limit(120);

        $this->writeHead($handle);
        $this->writeTables($handle, $club);

        fclose($handle);

        return response()->download($path, 'lsverein_' . Carbon::now()->format('Ymd') . '.sql');
    }

    private function writeHead($handle)
    {
        $head = <<<HEAD
            SET NAMES utf8;
            SET time_zone = '+00:00';
            SET foreign_key_checks = 0;
            SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
        HEAD;

        fwrite($handle, $head . PHP_EOL . PHP_EOL);
    }

    public static function writeTables($handle, $club): void
    {
        $memberIds = $club->members()->pluck('members.id');

        SqlConverter::fromBuilder(User::whereIn('id', ClubUser::where('club_id', $club->id)->pluck('user_id'))
            ->orderBy('id'))
            ->write($handle);
        SqlConverter::fromBuilder(Club::where('id', $club->id))
            ->write($handle);
        SqlConverter::fromBuilder(ClubUser::where('club_id', $club->id)
        ->orderBy('id'))
            ->write($handle);
        SqlConverter::fromBuilder(Member::where('club_id', $club->id)
        ->orderBy('id'))
            ->cast('gender', 'gender')
            ->cast('birthday', 'date')
            ->cast('death_day', 'date')
            ->write($handle);
        SqlConverter::fromBuilder(ClubMember::where('club_id', $club->id)->orderBy('id'))
            ->cast('from', 'date')
            ->cast('to', 'date')
            ->write($handle);
        SqlConverter::fromBuilder(Section::where('club_id', $club->id)->orderBy('id'))
            ->write($handle);
        SqlConverter::fromBuilder(MemberSection::whereIn('member_id', $memberIds))
            ->cast('from', 'date')
            ->cast('to', 'date')
            ->write($handle);
        SqlConverter::fromBuilder(Event::where('club_id', $club->id)->orderBy('id'))
            ->write($handle);
        SqlConverter::fromBuilder(EventMember::whereIn('member_id', $memberIds))
            ->cast('date', 'date')
        ->write($handle);
        SqlConverter::fromBuilder(Item::where('club_id', $club->id)->orderBy('id'))
            ->write($handle);
        SqlConverter::fromBuilder(ItemMember::whereIn('member_id', $memberIds))
        ->write($handle);
        SqlConverter::fromBuilder(Role::where('club_id', $club->id)->orderBy('id'))
            ->write($handle);
        SqlConverter::fromBuilder(MemberRole::whereIn('member_id', $memberIds))
            ->cast('from', 'date')
            ->cast('to', 'date')
        ->write($handle);
        SqlConverter::fromBuilder(Subscription::where('club_id', $club->id)->orderBy('id'))
            ->write($handle);
        SqlConverter::fromBuilder(MemberSubscription::whereIn('member_id', $memberIds))
        ->write($handle);
    }

}
