<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return redirect()->route('login');
});



// Route::middleware('auth')->group(function () {

// });
Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
Route::post('logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');
    Route::resource('/user', UserController::class);
    Route::resource('/role', RoleController::class);
    Route::get('/log', [LogController::class, 'index'])->name('log.index');
    Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
    Route::get('/audit/{activityLog}', [AuditLogController::class, 'show'])->name('audit.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
