<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Client;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return redirect()->route('login.show')
                    ->withErrors(['error' => 'Identifiants incorrects.']);
            }

            session(['jwt_token' => $token]);
            $devis_id = session('devis_id');
            if ($devis_id) {
                return redirect()->route('habitation.subscribe', ['devis_id' => $devis_id]);
            }
            return redirect()->route('habitation.simulate');
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return redirect()->route('login.show')
                ->withErrors(['error' => 'Échec de la connexion.']);
        }
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'id_conducteur' => 'required|exists:conducteurs,id',
        ]);

        try {
            $validated['statut'] = 'ACTIF';
            $validated['date_inscription'] = now()->toDateString();
            $client = Client::create($validated);
            $token = JWTAuth::fromUser($client);
            session(['jwt_token' => $token]);
            $devis_id = session('devis_id');
            if ($devis_id) {
                return redirect()->route('habitation.subscribe', ['devis_id' => $devis_id]);
            }
            return redirect()->route('habitation.simulate');
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return redirect()->route('register.show')
                ->withErrors(['error' => 'Échec de l\'inscription.']);
        }
    }
}
