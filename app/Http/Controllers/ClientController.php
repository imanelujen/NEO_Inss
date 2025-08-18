<?php
  namespace App\Http\Controllers;
  use Illuminate\Http\Request;
  use App\Models\Client;
  use Illuminate\Support\Facades\Log;
  use Tymon\JWTAuth\Facades\JWTAuth;
  use Illuminate\Support\Facades\Hash;
  use Illuminate\Contracts\Auth\Authenticatable;
  use App\Models\Devis;


  class ClientController extends Controller
  {
      public function showLoginForm()
      {
          Log::info('Client login form displayed');
          return view('client.login');
      }

 public function login(Request $request)
    {
        // 1️⃣ Validate request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        Log::info('Client login attempt', ['email' => $request->email]);

        // 2️⃣ Try to authenticate with JWT
        $credentials = $request->only('email', 'password');
        if (!$token = auth('api')->attempt($credentials)) {
            Log::warning('Login failed: wrong credentials', ['email' => $request->email]);
            return back()->withErrors(['email' => 'Identifiants incorrects'])->withInput($request->except('password'));
        }

        // 3️⃣ Get authenticated user
        $client = auth('api')->user();
        if (!$client) {
            Log::error('JWT token created but user is null after attempt', ['email' => $request->email]);
            return back()->withErrors(['error' => 'Impossible de récupérer le client.']);
        }

        // 4️⃣ Store JWT token in session
        session(['jwt_token' => $token]);

        Log::info('Client logged in successfully', ['client_id' => $client->id]);

        // 5️⃣ Redirect to subscription if valid devis_id exists in session
        $devis_id = session('devis_id');
        $type = session('type');
        if ($devis_id && Devis::find($devis_id)) {
            $route = $type === 'auto' ? 'auto.subscribe' : 'habit.subscribe';
            Log::info('Redirecting to subscription', ['devis_id' => $devis_id, 'type' => $type]);
            return redirect()->route($route, ['devis_id' => $devis_id]);
        }

        // 6️⃣ Fallback redirect to simulation
        Log::info('No valid devis_id in session, redirecting to simulation');
        return redirect()->route('habit.simulation.show', ['step' => 1])
            ->with('success', 'Connexion réussie');
    }

      public function showRegisterForm()
      {
          Log::info('Client registration form displayed');
          return view('client.register');
      }

      public function register(Request $request)
      {
          $validated = $request->validate([
              'name' => 'required|string|max:255',
              'prenom' => 'required|string|max:255',
              'email' => 'required|email|unique:clients,email',
              'password' => 'required|string|min:8|confirmed',
              'phone' => 'nullable|string|max:20',
          ]);

          Log::info('Client registration attempt', ['email' => $request->email]);

          $client = Client::create([
              'name' => $validated['name'],
              'prenom' => $validated['prenom'],
              'email' => $validated['email'],
              'password' => $validated['password'],
              'phone' => $validated['phone'],
              'date_inscription' => now(),

          ]);

          $token = JWTAuth::fromUser($client);
          session(['jwt_token' => $token]);
          Log::info('Client registered successfully', ['client_id' => $client->id]);

           $devis_id = session('devis_id');
        $type = session('type');
        if ($devis_id && Devis::find($devis_id)) {
            $route = $type === 'auto' ? 'auto.subscribe' : 'habit.subscribe';
            Log::info('Redirecting to subscription', ['devis_id' => $devis_id, 'type' => $type]);
            return redirect()->route($route, ['devis_id' => $devis_id]);
        }

        //change this
        return redirect()->route('habit.simulation.show', ['step' => 1])
            ->with('success', 'Inscription réussie');
    }

      public function logout(Request $request)
      {
          Log::info('Client logout', ['client_id' => auth('api')->id() ?? 'unknown']);
          JWTAuth::invalidate(JWTAuth::getToken());
          session()->forget('jwt_token');
          return redirect()->route('habit.simulation.show', ['step' => 1])
              ->with('success', 'Déconnexion réussie');
      }
  }


