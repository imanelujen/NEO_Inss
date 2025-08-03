<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\SimulationSession;
use App\Models\Conducteur;
use App\Models\Devis;
use App\Models\DevisAuto;
use App\Models\Vehicule;
use Illuminate\Support\Facades\Log;

class SimulationController extends Controller {
    public function show(Request $request) {
        $step = $request->query('step', 1);
        $data = session('simulation_data', []);

        try{
        $response = Http::get('http://neoassurances.local/wordpress/wp-json/wp/v2/posts');
        $posts = $response->successful() ? $response->json() : [];
        } catch(\Exception $e){
            // Handle the exception
            Log::error('WordPress API error: ' . $e->getMessage());
            $posts = [];
        }
        return view('simulation.form', compact('step', 'data','posts'));
    }
    public function store(Request $request) {
        Log::info('store method called with step: ' . $request->input('step'));
        $step = $request->input('step', 1);
        $data = session('simulation_data', []);
        Log::info('Current session data', ['simulation_data' => $data]);

        try{
        if ($step == 1) {
            $validated = $request->validate([
               'vehicle_type' => 'required|in:sedan,suv,truck,motorcycle',
                    'make' => 'required|string|max:255',
                    'model' => 'required|string|max:255',
                    'fuel_type' => 'required|in:ESSENCE,DIESEL,ELECTRIQUE,HYBRIDE',
                    'tax_horsepower' => 'required|integer|min:1',
                    'vehicle_value' => 'required|numeric|min:1000',
                    'registration_date' => 'required|date|before:today',
            ],[
               'vehicle_type.required' => 'Le type de véhicule est requis.',
                    'make.required' => 'La marque est requise.',
                    'model.required' => 'Le modèle est requis.',
                    'fuel_type.required' => 'Le type de carburant est requis.',
                    'tax_horsepower.required' => 'La puissance fiscale est requise.',
                    'tax_horsepower.min' => 'La puissance fiscale doit être positive.',
                    'vehicle_value.required' => 'La valeur du véhicule est requise.',
                    'vehicle_value.min' => 'La valeur doit être supérieure ou égale à 1000 €.',
                    'registration_date.required' => 'La date de mise en circulation est requise.',
                    'registration_date.before' => 'La date doit être antérieure à aujourd\'hui.',
                ]);
                Log::info('Step 1 validated', $validated);
                $data = array_merge($data, $validated);
                $request->session()->put('simulation_data', $data);
                Log::info('Session data stored', ['simulation_data' => $data]);
                return redirect()->route('simulation.show', ['step' => 2]);
        } elseif ($step == 2) {
            $validated = $request->validate([
                    'date_obtention_permis' => 'required|date|before:today',
                    'bonus_malus' => 'required|numeric|min:0.50|max:3.50',
                    'historique_accidents' => 'nullable|string',
                ], [
                    'date_obtention_permis.required' => 'La date d\'obtention du permis est requise.',
                    'date_obtention_permis.before' => 'La date doit être antérieure à aujourd\'hui.',
                    'bonus_malus.required' => 'Le bonus-malus est requis.',
                    'bonus_malus.min' => 'Le bonus-malus doit être au moins 0.50.',
                    'bonus_malus.max' => 'Le bonus-malus ne doit pas dépasser 3.50.',
                ]);
            Log::info('Step 2 validated', $validated);
                $data = array_merge($data, $validated);
                $years_driving = now()->diffInYears(\Carbon\Carbon::parse($data['date_obtention_permis']));
                $base_rate = 500;
                $vehicle_value_factor = $data['vehicle_value'] / 10000;
                $vehicle_type_factor = match ($data['vehicle_type']) {
                    'sedan' => 1.0,
                    'suv' => 1.2,
                    'truck' => 1.3,
                    'motorcycle' => 1.5,
                };
                $fuel_factor = match ($data['fuel_type']) {
                    'ESSENCE' => 1.1,
                    'DIESEL' => 1.2,
                    'ELECTRIQUE' => 0.9,
                    'HYBRIDE' => 1.0,
                };
                $age_factor = $years_driving < 2 ? 1.4 : ($years_driving < 5 ? 1.2 : 1.0);
                $registration_age = now()->diffInYears(\Carbon\Carbon::parse($data['registration_date']));
                $registration_factor = $registration_age > 10 ? 1.3 : ($registration_age > 5 ? 1.1 : 1.0);
                $bonus_malus = $data['bonus_malus'];
                $formules_choisis = [
                    'basic' => round($base_rate * $vehicle_value_factor * $vehicle_type_factor * $fuel_factor * $age_factor * $registration_factor * $bonus_malus, 2),
                    'standard' => round($base_rate * 1.5 * $vehicle_value_factor * $vehicle_type_factor * $fuel_factor * $age_factor * $registration_factor * $bonus_malus, 2),
                    'premium' => round($base_rate * 2.0 * $vehicle_value_factor * $vehicle_type_factor * $fuel_factor * $age_factor * $registration_factor * $bonus_malus, 2),
                ];
                $session = SimulationSession::create([
                    'date_debut' => now(),
                    'donnees_temporaires' => json_encode([$data]),
                ]);
                $devis = Devis::create([
                    'date_creation' => now(),
                    'date_expiration' => now()->addDays(30),
                    'montant_base' => $base_rate,
                    'garanties_incluses' => json_encode(['Responsabilité civile', 'collision']),
                    'status' => 'BROUILLON',
                    'typedevis' => 'SIMULATION',
                    'id_simulationsession' => $session->id,
                ]);
                $vehicule = Vehicule::create([
                    'vehicle_type' => $data['vehicle_type'],
                    'make' => $data['make'],
                    'model' => $data['model'],
                    'fuel_type' => $data['fuel_type'],
                    'tax_horsepower' => $data['tax_horsepower'],
                    'vehicle_value' => $data['vehicle_value'],
                    'registration_date' => $data['registration_date'],
                ]);
                $conducteur = Conducteur::create([
                    'bonus_malus' => $data['bonus_malus'],
                    'historique_accidents' => json_encode([$data['historique_accidents']]),
                    'date_obtention_permis' => $data['date_obtention_permis'],
                ]);
                $devis_auto = DevisAuto::create([
                    'id_devis' => $devis->id,
                    'id_vehicule' => $vehicule->id,
                    'id_conducteur' => $conducteur->id,
                    'formules_choisis' => json_encode([$formules_choisis]),
                ]);
                Log::info('Step 2 processed', [
                    'session_id' => $session->id,
                    'devis_id' => $devis->id,
                    'vehicule_id' => $vehicule->id,
                    'conducteur_id' => $conducteur->id,
                    'formules_choisis' => $formules_choisis
                ]);
                $data['formules_choisis'] = $formules_choisis;
                $request->session()->put('simulation_data', $data);
                Log::info('Redirecting to Step 3', ['simulation_data' => $data]);
                return redirect()->route('simulation.show', ['step' => 3]);
       }
            Log::warning('Invalid step', ['step' => $step]);
            return redirect()->route('simulation.show', ['step' => 1]);

        } catch (\Exception $e) {
            Log::error('Store method error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('simulation.show', ['step' => 1])->withErrors(['error' => 'Une erreur est survenue. Veuillez réessayer.']);
        }
    }

    public function reset(Request $request)
    {
        $request->session()->forget('simulation_data');
        Log::info('Session reset');
        return redirect()->route('simulation.show', ['step' => 1]);
    }
}
