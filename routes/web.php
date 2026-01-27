<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')   // si está logueado
        : redirect()->route('login');      // si no, login
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('users', UserController::class);
    Route::resource('contacts', ContactController::class);
    Route::resource('opportunities', OpportunityController::class);
    Route::post('contacts/quick-store', [ContactController::class, 'quickStore'])->name('contacts.quick-store'); //\App\Http\Controllers\ContactController::class
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
    Route::post('/agenda/followups', [AgendaController::class, 'storeFollowup'])->name('agenda.followups.store');
    Route::get('/reports/commercial', [ReportController::class, 'commercial'])->name('reports.commercial');
});

require __DIR__.'/auth.php';
