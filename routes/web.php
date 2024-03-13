<?php

use App\Backup;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\ClubMemberController;
use App\Http\Controllers\DebitController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventMemberController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemMemberController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberRoleController;
use App\Http\Controllers\MemberSectionController;
use App\Http\Controllers\MemberSubscriptionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use App\Models\Club;
use App\Models\Debit;
use App\Models\Event;
use App\Models\Item;
use App\Models\Member;
use App\Models\Role;
use App\Models\Section;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/members');
});

Route::get('/test', function (\Illuminate\Http\Request $request) {

});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/account', [AccountController::class, 'edit'])
        ->name('account.edit');
    Route::put('/account', [AccountController::class, 'update'])
        ->name('account.update');

    Route::get('/users', [UserController::class, 'index'])
        ->can('viewAny', User::class)
        ->name('users');
    Route::get('/users/create', [UserController::class, 'create'])
        ->can('create', User::class);
    Route::post('/users', [UserController::class, 'store'])
        ->can('create', User::class);
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->can('update', 'user');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->can('update', 'user');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->can('delete', 'user');
    Route::post('/users/{user}/login', [UserController::class, 'loginAs'])
        ->can('root', User::class);
    Route::get('/show-log', [UserController::class, 'showLog'])
        ->can('root', User::class);

    Route::get('/backups', [BackupController::class, 'index'])
        ->can('view', Backup::class)
        ->name('backups');
    Route::get('/backups/create', [BackupController::class, 'create'])
        ->can('create', Backup::class);
    Route::post('/backups/restore', [BackupController::class, 'restore'])
        ->can('restore', Backup::class);
    Route::get('/backups/download/{filename}', [BackupController::class, 'download'])
        ->can('restore', Backup::class)
        ->name('backups.download');

    Route::get('/clubs', [ClubController::class, 'index'])
        ->can('viewAny', Club::class)
        ->name('clubs');
    Route::get('/clubs/create', [ClubController::class, 'create'])
        ->can('create', Club::class)
        ->name('clubs.create');
    Route::post('/clubs', [ClubController::class, 'store'])
        ->can('create', Club::class);
    Route::get('/clubs/{club}/edit', [ClubController::class, 'edit'])
        ->can('update', 'club');
    Route::put('/clubs/{club}', [ClubController::class, 'update'])
        ->can('update', 'club');
    Route::delete('/clubs/{club}', [ClubController::class, 'destroy'])
        ->can('delete', 'club');
    Route::post('/clubs/{club}/change', [ClubController::class, 'change'])
        ->can('change', 'club');
    Route::get('/clubs/{club}/blsv-statistic', [ClubController::class, 'blsvStatistic'])
        ->can('update', 'club');
    Route::get('/downloads/{filename}', [ClubController::class, 'downloads'])
        ->can('download', Club::class);

    Route::get('/sections', [SectionController::class, 'index'])
        ->can('viewAny', Section::class)
        ->name('sections');
    Route::get('/sections/create', [SectionController::class, 'create'])
        ->can('create', Section::class);
    Route::post('/sections', [SectionController::class, 'store'])
        ->can('create', Section::class);
    Route::get('/sections/{section}/edit', [SectionController::class, 'edit'])
        ->can('update', 'section');
    Route::put('/sections/{section}', [SectionController::class, 'update'])
        ->can('update', 'section');
    Route::delete('/sections/{section}', [SectionController::class, 'destroy'])
        ->can('delete', 'section');

    Route::get('/roles', [RoleController::class, 'index'])
        ->can('viewAny', Role::class)
        ->name('roles');
    Route::get('/roles/create', [RoleController::class, 'create'])
        ->can('create', Role::class);
    Route::post('/roles', [RoleController::class, 'store'])
        ->can('create', Role::class);
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->can('update', 'role');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->can('update', 'role');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->can('delete', 'role');

    Route::get('/events', [EventController::class, 'index'])->name('events')
        ->can('viewAny', Event::class);
    Route::get('/events/create', [EventController::class, 'create'])
        ->can('create', Event::class);
    Route::post('/events', [EventController::class, 'store'])
        ->can('create', Event::class);
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])
        ->can('update', 'event');
    Route::put('/events/{event}', [EventController::class, 'update'])
        ->can('update', 'event');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])
        ->can('delete', 'event');

    Route::get('/items', [ItemController::class, 'index'])->name('items')
        ->can('viewAny', Item::class);
    Route::get('/items/create', [ItemController::class, 'create'])
        ->can('create', Item::class);
    Route::post('/items', [ItemController::class, 'store'])
        ->can('create', Item::class);
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])
        ->can('update', 'item');
    Route::put('/items/{item}', [ItemController::class, 'update'])
        ->can('update', 'item');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])
        ->can('delete', 'item');

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])
        ->can('viewAny', Subscription::class)
        ->name('subscriptions');
    Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])
        ->can('create', Subscription::class);
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])
        ->can('create', Subscription::class);
    Route::get('/subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit'])
        ->can('update', 'subscription');
    Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])
        ->can('update', 'subscription');
    Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])
        ->can('delete', 'subscription');
    Route::post('/subscriptions/debit', [SubscriptionController::class, 'debit'])
        ->can('debit', Subscription::class)
        ->name('subscriptions.debit');

    Route::get('/debits', [DebitController::class, 'index'])
        ->can('viewAny', Debit::class)
        ->name('debits');
    Route::get('/debits/create', [DebitController::class, 'create'])
        ->can('create', Debit::class);
    Route::post('/debits', [DebitController::class, 'store'])
        ->can('create', Debit::class);
    Route::get('/debits/{debit}/edit', [DebitController::class, 'edit'])
        ->can('update', 'debit');
    Route::put('/debits/{debit}', [DebitController::class, 'update'])
        ->can('update', 'debit');
    Route::delete('/debits/{debit}', [DebitController::class, 'destroy'])
        ->can('delete', 'debit');
    Route::post('/debits/debit', [DebitController::class, 'debit'])
        ->can('debit', Debit::class)
        ->name('debits.debit');

    Route::get('/members', [MemberController::class, 'index'])
        ->can('viewAny', Member::class)
        ->name('members');
    Route::get('/members/{member}/show', [MemberController::class, 'show'])
        ->can('view', 'member')
        ->name('members.show');
    Route::get('/members/create', [MemberController::class, 'create'])
        ->can('create', Member::class);
    Route::post('/members', [MemberController::class, 'store'])
        ->can('create', Member::class);
    Route::get('/members/{member}/edit', [MemberController::class, 'edit'])
        ->name('members.edit')->can('update', 'member');
    Route::put('/members/{member}', [MemberController::class, 'update'])
        ->can('update', 'member');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])
        ->can('delete', 'member');
    Route::put('/members/{member}/resign', [MemberController::class, 'resign'])
        ->can('delete', 'member');
    Route::get('/members/pdf', [MemberController::class, 'exportPdf'])
        ->can('viewAny', Member::class)
        ->name('members.pdf');
    Route::get('/members/csv', [MemberController::class, 'exportCsv'])
        ->can('viewAny', Member::class)
        ->name('members.csv');
    Route::get('/members/vcf', [MemberController::class, 'exportVcard'])
        ->can('viewAny', Member::class)
        ->name('members.vcf');

    Route::get('/members/{member}/club/create', [ClubMemberController::class, 'create'])
        ->can('create', Member::class);
    Route::post('/members/{member}/club', [ClubMemberController::class, 'store'])
        ->can('create', Member::class);
    Route::get('/members/{member}/club/{clubMember}/edit', [ClubMemberController::class, 'edit'])
        ->can('update', 'member');
    Route::put('/members/{member}/club/{clubMember}', [ClubMemberController::class, 'update'])
        ->can('update', 'member');
    Route::delete('/members/{member}/club/{clubMember}', [ClubMemberController::class, 'destroy'])
        ->can('update', 'member');

    Route::get('/members/{member}/section/create', [MemberSectionController::class, 'create'])
        ->can('create', Member::class);
    Route::post('/members/{member}/section', [MemberSectionController::class, 'store'])
        ->can('create', Member::class);
    Route::get('/members/{member}/section/{memberSection}/edit', [MemberSectionController::class, 'edit'])
        ->can('update', 'member');
    Route::put('/members/{member}/section/{memberSection}', [MemberSectionController::class, 'update'])
        ->can('update', 'member');
    Route::delete('/members/{member}/section/{memberSection}', [MemberSectionController::class, 'destroy'])
        ->can('update', 'member');

    Route::get('/members/{member}/subscription/create', [MemberSubscriptionController::class, 'create'])
        ->can('create', Member::class);
    Route::post('/members/{member}/subscription', [MemberSubscriptionController::class, 'store'])
        ->can('create', Member::class);
    Route::get('/members/{member}/subscription/{memberSubscription}/edit', [MemberSubscriptionController::class, 'edit'])
        ->can('update', 'member');
    Route::put('/members/{member}/subscription/{memberSubscription}', [MemberSubscriptionController::class, 'update'])
        ->can('update', 'member');
    Route::delete('/members/{member}/subscription/{memberSubscription}', [MemberSubscriptionController::class, 'destroy'])
        ->can('update', 'member');

    Route::get('/members/{member}/event/create', [EventMemberController::class, 'create'])
        ->can('create', Member::class);
    Route::post('/members/{member}/event', [EventMemberController::class, 'store'])
        ->can('create', Member::class);
    Route::get('/members/{member}/event/{eventMember}/edit', [EventMemberController::class, 'edit'])
        ->can('update', 'member');
    Route::put('/members/{member}/event/{eventMember}', [EventMemberController::class, 'update'])
        ->can('update', 'member');
    Route::delete('/members/{member}/event/{eventMember}', [EventMemberController::class, 'destroy'])
        ->can('update', 'member');

    Route::get('/members/{member}/item/create', [ItemMemberController::class, 'create'])
        ->can('create', Member::class);
    Route::post('/members/{member}/item', [ItemMemberController::class, 'store'])
        ->can('create', Member::class);
    Route::get('/members/{member}/item/{itemMember}/edit', [ItemMemberController::class, 'edit'])
        ->can('update', 'member');
    Route::put('/members/{member}/item/{itemMember}', [ItemMemberController::class, 'update'])
        ->can('update', 'member');
    Route::delete('/members/{member}/item/{itemMember}', [ItemMemberController::class, 'destroy'])
        ->can('update', 'member');

    Route::get('/members/{member}/role/create', [MemberRoleController::class, 'create'])
        ->can('create', Member::class);
    Route::post('/members/{member}/role', [MemberRoleController::class, 'store'])
        ->can('create', Member::class);
    Route::get('/members/{member}/role/{memberRole}/edit', [MemberRoleController::class, 'edit'])
        ->can('update', 'member');
    Route::put('/members/{member}/role/{memberRole}', [MemberRoleController::class, 'update'])
        ->can('update', 'member');
    Route::delete('/members/{member}/role/{memberRole}', [MemberRoleController::class, 'destroy'])
        ->can('update', 'member');

    Route::get('/exportclub', [ExportController::class, 'exportClub'])
        ->can('download', Club::class)
        ->name('export.club');;
});

require __DIR__ . '/auth.php';
