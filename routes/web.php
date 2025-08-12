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
Route::get('/simulate', [SimulationController::class, 'show'])->name('auto.show');
Route::post('/simulate', [SimulationController::class, 'store'])->name('auto.store');
Route::get('/simulate/reset', [SimulationController::class, 'reset'])->name('auto.reset');
Route::post('/simulate/auto/select-offer/{devis_id}', [QuoteController::class, 'selectOffer'])->name('auto.select_offer');
Route::get('/simulate/auto/download/{devis_id}', [QuoteController::class, 'downloadQuote'])->name('auto.download');
Route::post('/simulate/auto/email/{devis_id}', [QuoteController::class, 'emailQuote'])->name('auto.email');
Route::get('/simulate/auto/subscribe/{devis_id}', [QuoteController::class, 'subscribe'])->name('auto.subscribe');
Route::post('/simulate/auto/subscribe/{devis_id}', [QuoteController::class, 'storeSubscription'])->name('auto.store_subscription');
Route::get('/simulate/auto/result/{devis_id}', [QuoteController::class, 'showQuote'])->name('auto.result');



Route::get('/habit/simulate', [habitSimulerController::class, 'show'])->name('habit.simulation.show');
Route::post('/habit/simulate', [habitSimulerController::class, 'store'])->name('habit.simulation.store');
Route::get('/habit/simulate/reset', [habitSimulerController::class, 'reset'])->name('habit.simulation.reset');
Route::post('/simulate/habitation/select-offer/{devis_id}', [HabitSimulerController::class, 'selectOffer'])->name('habit.select_offer');
Route::get('/simulate/habitation/download/{devis_id}', [HabitSimulerController::class, 'downloadQuote'])->name('habit.download');
Route::post('/simulate/habitation/email/{devis_id}', [HabitSimulerController::class, 'emailQuote'])->name('habit.email');
Route::get('/simulate/habitation/subscribe/{devis_id}', [HabitSimulerController::class, 'subscribe'])->name('habit.subscribe');
Route::post('/simulate/habitation/subscribe/{devis_id}', [HabitSimulerController::class, 'storeSubscription'])->name('habit.store_subscription');
Route::get('/habitation/result/{devis_id}', [habitSimulerController::class, 'showQuote'])->name('habit.result');
