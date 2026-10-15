<?php
// À ajouter dans routes/web.php (en haut : use App\Http\Controllers\DashboardController;)

Route::get('/', fn () => redirect()->route('dashboard'));
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('/dashboard');
});
