<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogWpController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\habitSimulerController;
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
    return view('welcome');
});
Route::get('/blog', [BlogWpController::class,'index']);

Route::get('/about', function () {
    return view('about');
});
Route::get('/simulate', [SimulationController::class, 'show'])->name('simulation.show');
Route::post('/simulate', [SimulationController::class, 'store'])->name('simulation.store');
Route::get('/simulate/reset', [SimulationController::class, 'reset'])->name('simulation.reset');

Route::get('/habit/simulate', [habitSimulerController::class, 'show'])->name('habit.simulation.show');
Route::post('/habit/simulate', [habitSimulerController::class, 'store'])->name('habit.simulation.store');
Route::get('/habit/simulate/reset', [habitSimulerController::class, 'reset'])->name('habit.simulation.reset');
