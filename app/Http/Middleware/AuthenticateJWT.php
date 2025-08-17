<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class AuthenticateJWT
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = session('jwt_token');
            if ($token) {
                JWTAuth::setToken($token);
                if ($user = JWTAuth::authenticate()) {
                    auth('api')->setUser($user);
                    Log::info('JWT authentication successful', ['client_id' => $user->id]);
                    return $next($request);
                }
            }
            Log::warning('JWT authentication failed: No valid token');
        } catch (\Exception $e) {
            Log::warning('JWT authentication error', ['error' => $e->getMessage()]);
        }

        session(['intended_url' => $request->url()]);
        return redirect()->route('login.show')->withErrors(['error' => 'Veuillez vous connecter pour continuer.']);
    }
}