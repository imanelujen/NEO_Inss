<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedirectToRegister
{
    public function handle(Request $request, Closure $next, $guard = 'api_clients')
    {
        Log::info('RedirectToRegister middleware called', [
            'guard' => $guard,
            'session_id' => session()->getId(),
            'intended_devis_id' => session('intended_devis_id'),
            'type' => session('type'),
            'session_data' => session()->all(),
            'url' => $request->url(),
            'cookies' => $request->cookies->all(),
            'middleware_stack' => $request->route()->gatherMiddleware(),
        ]);

        if (Auth::guard($guard)->check()) {
            Log::info('User authenticated via session', [
                'guard' => $guard,
                'client_id' => Auth::guard($guard)->id(),
                'session_id' => session()->getId(),
            ]);
            return $next($request);
        }

        Log::info('Unauthenticated user, redirecting to login', [
            'guard' => $guard,
            'session_id' => session()->getId(),
            'intended_devis_id' => session('intended_devis_id'),
            'type' => session('type'),
            'url' => $request->url(),
            'middleware_stack' => $request->route()->gatherMiddleware(),
        ]);
        session(['intended_url' => $request->url()]);
        return redirect()->route('login')->withErrors(['error' => 'Veuillez vous connecter pour continuer.']);
    }
}