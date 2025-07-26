<?php

use App\Http\Controllers\KabupatenController;
use App\Http\Controllers\PendudukController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProvinsiController;
use App\Models\Provinsi;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $kabupatens = \App\Models\Kabupaten::with('provinsi')->get();
    $penduduks = \App\Models\Penduduk::with('kabupaten')->get(); // jika ada relasi
    $provinsis = \App\Models\Provinsi::all();

    return Inertia::render('Dashboard', [
        'kabupatens' => $kabupatens,
        'penduduks' => $penduduks,
        'provinsis' => Provinsi::orderBy('nama')->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');


//Province
Route::post('/provinsi', [ProvinsiController::class, 'store'])
    ->name('provinsi.store')
    ->middleware('auth');

Route::put('/provinsi/{id}', [ProvinsiController::class, 'update'])
    ->name('provinsi.update')
    ->middleware('auth');

Route::delete('/provinsi/{id}', [ProvinsiController::class, 'destroy'])
    ->name('provinsi.destroy')
    ->middleware('auth');


//Kabupaten
Route::get('/kabupaten', [KabupatenController::class, 'index'])
    ->name('kabupaten.index')
    ->middleware('auth');

Route::post('/kabupaten', [KabupatenController::class, 'store'])
    ->name('kabupaten.store')
    ->middleware('auth');

Route::put('/kabupaten/{id}', [KabupatenController::class, 'update'])
    ->name('kabupaten.update')
    ->middleware('auth');

Route::delete('/kabupaten/{id}', [KabupatenController::class, 'destroy'])
    ->name('kabupaten.destroy')
    ->middleware('auth');


//Penduduk
Route::get('/penduduk', [PendudukController::class, 'index'])
    ->name('penduduk.index')
    ->middleware('auth');

Route::post('/penduduk', [PendudukController::class, 'store'])
    ->name('penduduk.store')
    ->middleware('auth');

Route::put('/penduduk/{id}', [PendudukController::class, 'update'])
    ->name('penduduk.update')
    ->middleware('auth');

Route::delete('/penduduk/{id}', [PendudukController::class, 'destroy'])
    ->name('penduduk.destroy')
    ->middleware('auth');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::resource('kabupaten', KabupatenController::class);
// Route::resource('penduduk', PendudukController::class);
// Route::resource('provinsi', ProvinsiController::class);

require __DIR__ . '/auth.php';
