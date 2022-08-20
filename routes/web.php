<?php

use App\Backup;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Models\Club;
use App\Models\Event;
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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('/users', [UserController::class, 'index'])
        ->can('viewAny', User::class)
        ->name('users');
    Route::get('/users/account', [UserController::class, 'editAccount']);
    Route::put('/users/account', [UserController::class, 'updateAccount']);
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
        ->can('create', 'user');

    Route::get('/backups', [BackupController::class, 'index'])
        ->can('view', Backup::class)
        ->name('backups');
    Route::get('/backups/create', [BackupController::class, 'create'])
        ->can('create', Backup::class);
    Route::post('/backups/restore', [BackupController::class, 'restore'])
        ->can('restore', Backup::class);

    Route::get('/clubs', [ClubController::class, 'index'])->name('clubs')->can('viewAny', Club::class);
    Route::get('/clubs/create', [ClubController::class, 'create'])->can('create', Club::class);
    Route::post('/clubs', [ClubController::class, 'store'])->can('create', Club::class);
    Route::get('/clubs/{club}/edit', [ClubController::class, 'edit'])->can('update', 'club');
    Route::put('/clubs/{club}', [ClubController::class, 'update'])->can('update', 'club');
    Route::delete('/clubs/{club}', [ClubController::class, 'destroy'])->can('delete', 'club');
    Route::post('/clubs/{club}/change', [ClubController::class, 'change'])->can('change', 'club');

    Route::get('/sections', [SectionController::class, 'index'])->name('sections')->can('viewAny', Section::class);
    Route::get('/sections/create', [SectionController::class, 'create'])->can('create', Section::class);
    Route::post('/sections', [SectionController::class, 'store'])->can('create', Section::class);
    Route::get('/sections/{section}/edit', [SectionController::class, 'edit'])->can('update', 'section');
    Route::put('/sections/{section}', [SectionController::class, 'update'])->can('update', 'section');
    Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->can('delete', 'section');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles')->can('viewAny', Role::class);
    Route::get('/roles/create', [RoleController::class, 'create'])->can('create', Role::class);
    Route::post('/roles', [RoleController::class, 'store'])->can('create', Role::class);
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->can('update', 'role');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->can('update', 'role');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->can('delete', 'role');

    Route::get('/events', [EventController::class, 'index'])->name('events')->can('viewAny', Event::class);
    Route::get('/events/create', [EventController::class, 'create'])->can('create', Event::class);
    Route::post('/events', [EventController::class, 'store'])->can('create', Event::class);
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->can('update', 'event');
    Route::put('/events/{event}', [EventController::class, 'update'])->can('update', 'event');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->can('delete', 'event');

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions')->can('viewAny', Subscription::class);
    Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])->can('create', Subscription::class);
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->can('create', Subscription::class);
    Route::get('/subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit'])->can('update', 'subscription');
    Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->can('update', 'subscription');
    Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->can('delete', 'subscription');

});

require __DIR__.'/auth.php';
