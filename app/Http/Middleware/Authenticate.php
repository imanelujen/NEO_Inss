<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        Log::info('Authenticate middleware triggered', [
            'url' => $request->url(),
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'middleware_stack' => $request->route() ? $request->route()->gatherMiddleware() : [],
        ]);

        if (!$request->expectsJson()) {
            if (str_starts_with($request->path(), 'admin')) {
                return route('filament.admin.auth.login'); // Filament admin login
            }
            return route('login'); // Client login
        }
    }
}