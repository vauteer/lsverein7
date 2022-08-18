<?php

use App\Backup;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\UserController;
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
});

require __DIR__.'/auth.php';
