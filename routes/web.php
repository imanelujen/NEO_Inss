<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogWpController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\habitSimulerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\StripeWebhookController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/blog', [BlogWpController::class, 'index']);
Route::get('/about', function () {
    return view('about');
});

Route::get('/login', [ClientController::class, 'showLoginForm'])->name('login');
Route::post('/login', [ClientController::class, 'login'])->name('login');
Route::get('/register', [ClientController::class, 'showRegisterForm'])->name('register.show');
Route::post('/register', [ClientController::class, 'register'])->name('register');
Route::post('/logout', [ClientController::class, 'logout'])->name('logout');

// Auto simulation routes
Route::get('/simulate', [SimulationController::class, 'show'])->name('auto.show');
Route::post('/simulate', [SimulationController::class, 'store'])->name('auto.store');
Route::get('/simulate/reset', [SimulationController::class, 'reset'])->name('auto.reset');
Route::post('/simulate/auto/select-offer/{devis_id}', [SimulationController::class, 'selectOffer'])->name('auto.select_offer');
Route::get('/simulate/auto/download/{devis_id}', [SimulationController::class, 'downloadQuote'])->name('auto.download');
Route::post('/simulate/auto/email/{devis_id}', [SimulationController::class, 'emailQuote'])->name('auto.email');
Route::get('/simulate/auto/result/{devis_id}', [SimulationController::class, 'showQuote'])->name('auto.result');

// Habitation simulation routes
Route::get('/habit/simulate', [habitSimulerController::class, 'show'])->name('habit.simulation.show');
Route::post('/habit/simulate', [habitSimulerController::class, 'store'])->name('habit.simulation.store');
Route::get('/habit/simulate/reset', [habitSimulerController::class, 'reset'])->name('habit.simulation.reset');
Route::post('/simulate/habitation/select-offer/{devis_id}', [habitSimulerController::class, 'selectOffer'])->name('habit.select_offer');
Route::get('/simulate/habitation/download/{devis_id}', [habitSimulerController::class, 'downloadQuote'])->name('habit.download');
Route::post('/simulate/habitation/email/{devis_id}', [habitSimulerController::class, 'emailQuote'])->name('habit.email');
Route::get('/habitation/result/{devis_id}', [habitSimulerController::class, 'showQuote'])->name('habit.result');

// Protected client routes
Route::middleware(['auth:api_clients'])->group(function () {
    Route::get('/simulate/auto/subscribe/{devis_id}', [SimulationController::class, 'subscribe'])->name('auto.subscribe');
    Route::post('/simulate/auto/subscribe/{devis_id}', [SimulationController::class, 'storeSubscription'])->name('auto.store_subscription');
    Route::get('/subscribe/documents/{devis_id}', [SimulationController::class, 'showDocuments'])->name('auto.documents');
    Route::post('/subscribe/documents/{devis_id}', [SimulationController::class, 'storeDocuments'])->name('auto.documents.store');
    Route::get('/subscribe/payment/{devis_id}', [SimulationController::class, 'showPayment'])->name('auto.payment');
    Route::post('/subscribe/payment/{devis_id}', [SimulationController::class, 'storePayment'])->name('auto.payment.store');
    Route::get('/habit/simulate/subscribe/{devis_id}', [habitSimulerController::class, 'subscribe'])->name('habit.subscribe');
    Route::post('/habit/simulate/subscribe/{devis_id}', [habitSimulerController::class, 'storeSubscription'])->name('habit.store_subscription');
    Route::get('/auto/{devis_id}/payment-intent', [SimulationController::class, 'createPaymentIntent'])->name('auto.payment.intent');
    Route::post('/auto/{devis_id}/payment-store', [SimulationController::class, 'storePayment'])->name('auto.payment.store');
    

});

// Debug route
Route::get('/debug', function () {
    \Illuminate\Support\Facades\Log::info('Debug route called', [
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'auth_check' => \Illuminate\Support\Facades\Auth::guard('api_clients')->check(),
        'user' => \Illuminate\Support\Facades\Auth::guard('api_clients')->user(),
        'url' => request()->url(),
        'middleware_stack' => request()->route() ? request()->route()->gatherMiddleware() : [],
    ]);
    return [
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'auth_check' => \Illuminate\Support\Facades\Auth::guard('api_clients')->check(),
        'user' => \Illuminate\Support\Facades\Auth::guard('api_clients')->user(),
    ];
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');
