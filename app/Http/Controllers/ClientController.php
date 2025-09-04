<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Mail\ClientRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use App\Models\Devis;

class ClientController extends Controller
{
    public function showRegisterForm()
    {
        Log::info('Showing registration form', [
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'url' => request()->url(),
        ]);
        return view('client.register');
    }

    public function showLoginForm()
    {
        Log::info('Showing login form', [
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'url' => request()->url(),
        ]);
        return view('client.login');
    }

    public function login(Request $request)
    {
        Log::info('Client login attempt', [
            'email' => $request->input('email'),
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'url' => $request->url(),
        ]);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('api_clients')->attempt($credentials)) {
            $request->session()->regenerate();
            session()->save();

            Log::info('Client logged in', [
                'client_id' => Auth::guard('api_clients')->id(),
                'session_id' => session()->getId(),
                'session_data' => session()->all(),
            ]);

            $intended_devis_id = session('intended_devis_id');
            $type = session('type');

            if ($intended_devis_id) {
                $devis = Devis::find($intended_devis_id);
                $type = $devis?->typedevis ?? $type;

                if (strtoupper($type) === 'AUTO') {
                    return redirect()->route('auto.documents', ['devis_id' => $intended_devis_id]);
                } elseif (strtoupper($type) === 'HABITATION' || strtoupper($type) === 'HABIT') {
                    return redirect()->route('habit.documents', ['devis_id' => $intended_devis_id]);
                }
            }

            Log::info('No valid intended devis or type, redirecting to auto simulation', [
                'intended_devis_id' => $intended_devis_id,
                'type' => $type,
            ]);
            return redirect()->route('auto.simulation.show', ['step' => 1])->with('success', 'Connexion réussie.');
        }

        Log::warning('Client login failed', [
            'email' => $request->input('email'),
            'session_id' => session()->getId(),
        ]);
        return back()->withErrors(['email' => 'Identifiants incorrects.']);
    }

    public function register(Request $request)
    {
        Log::info('Client registration attempt', [
            'email' => $request->input('email'),
            'session_id' => session()->getId(),
            'intended_devis_id' => session('intended_devis_id'),
            'type' => session('type'),
            'session_data' => session()->all(),
            'url' => $request->url(),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/',
            'password' => 'required|string|min:8|confirmed', // Added password validation
        ]);

        try {
            $client = Client::create([
                'name' => $validated['name'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => bcrypt($validated['password']),
                'statut' => 'ACTIF',
                'date_inscription' => now()->toDateString(),
            ]);

            Mail::to($client->email)->send(new ClientRegistered($client, $validated['password']));
            Log::info('Email sent with password', ['email' => $client->email]);

            Auth::guard('api_clients')->login($client);
            session([
                'auto_data' => [
                    'intended_devis_id' => session('intended_devis_id'),
                    'type' => session('type', 'auto'),
                ],
            ]);
            session()->regenerate();
            session()->save();

            Log::info('Client registered and authenticated', [
                'client_id' => $client->id,
                'devis_id' => session('intended_devis_id'),
                'type' => session('type'),
                'session_id' => session()->getId(),
                'session_data' => session()->all(),
            ]);

            $intended_devis_id = session('intended_devis_id');
            $type = session('type');

            if ($intended_devis_id) {
                $devis = Devis::find($intended_devis_id);
                $type = $devis?->typedevis ?? $type;

                if (strtoupper($type) === 'AUTO') {
                    return redirect()->route('auto.documents', ['devis_id' => $intended_devis_id]);
                } elseif (strtoupper($type) === 'HABITATION' || strtoupper($type) === 'HABIT') {
                    return redirect()->route('habit.documents', ['devis_id' => $intended_devis_id]);
                }
            }

            Log::info('No valid intended devis or type, redirecting to auto simulation', [
                'intended_devis_id' => $intended_devis_id,
                'type' => $type,
            ]);
            return redirect()->route('auto.simulation.show', ['step' => 1])->with('success', 'Inscription réussie.');
        } catch (QueryException $e) {
            Log::error('Database error during registration: ' . $e->getMessage(), [
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'trace' => $e->getTraceAsString(),
            ]);
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return redirect()->route('client.register')->withErrors(['email' => 'Cet email est déjà utilisé.']);
            }
            return redirect()->route('client.register')->withErrors(['error' => 'Erreur de base de données. Veuillez réessayer.']);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('client.register')->withErrors(['error' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.']);
        }
    }

    public function logout(Request $request)
    {
        Log::info('Logout attempt', [
            'client_id' => Auth::guard('api_clients')->id(),
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
        ]);

        Auth::guard('api_clients')->logout();
        $request->session()->forget(['auto_data']);
        $request->session()->regenerate();

        Log::info('User logged out', ['session_id' => session()->getId()]);
        return redirect()->route('auto.simulation.show', ['step' => 1]);
    }
}