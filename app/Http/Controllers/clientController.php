<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class clientController extends Controller
{
    public function showLoginForm(){
     LOg::info('Showing login form');
        return view('client.login');
    }
    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        Log::info('Client login attempt', ['email' => $request->email]);
        if(!$token =JWTAuth::attempt($credentials)){
            Log::error('Client login failed', ['email' => $request->email]);
            return redirect()->route('client.login')
                ->withErrors(['email' => 'The provided credentials do not match our records.']);
        }

        session(['jwt_token' => $token]);
        $client = auth('api')->user();
        Log::info('Client logged in successfully', ['client_id' => $client->id]);

        // Redirect to subscription if devis_id exists
                 if (session('devis_id')) {
              return redirect()->route('habit.subscribe', ['devis_id' => session('devis_id')]);
          }

          return redirect()->route('habit.simulation.show', ['step' => 1])
              ->with('success', 'Connexion réussie');
    }
    public function logout(){

    }
    public function register(){

    }



}
