<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SimulationSession;
use App\Models\Devis;
use App\Models\DevisHabitation;
use App\Models\Logement;

class habitSimulerController extends Controller
{
        private const SESSION_KEY = 'habitsimulation_data';
        public function show(Request $request) {
        $step = $request->query('step', 1);
        $data = session(self::SESSION_KEY, []);

        try{
        $response = Http::get('http://neoassurances.local/wordpress/wp-json/wp/v2/posts');
        $posts = $response->successful() ? $response->json() : [];
        } catch(\Exception $e){
            // Handle the exception
            Log::error('WordPress API error: ' . $e->getMessage());
            $posts = [];
        }
        return view('simulation.habitform', compact('step', 'data','posts'));
    }

     public function store(Request $request) {
        Log::info('store method called with step: ' . $request->input('step'));
        $step = $request->input('step', 1);
        $data = session(self::SESSION_KEY, []);

        try{
        if ($step == 1) {
            $validated = $request->validate([
               'housing_type' => 'required|in:APPARTEMENT,MAISON,PAVILLON,STUDIO,LOFT,VILLA',
                    'surface_area' => 'required|numeric|min:10',
                    'housing_value' => 'required|numeric|min:10000',
                    'construction_year' => 'required|date|before:today',
                    'occupancy_status' => 'required|in:Locataire,Propriétaire occupant,Propriétaire non-occupant',
            ],[
               'housing_type.required' => 'Le type de maison est requis.',
                    'surface_area.required' => 'La surface est requise.',
                    'housing_value.required' => 'La valeur de la maison est requise.',
                    'construction_year.required' => 'La date de construction est requise.',
                    'construction_year.before' => 'La date doit être antérieure à aujourd\'hui.',
                    'occupancy_status.required' => 'Le statut d\'occupation est requis.',
                    'housing_value.required' => 'La valeur de la maison est requise.',
                    'housing_value.min' => 'La valeur doit être supérieure ou égale à 10000 €.',
                ]);
                Log::info('Step 1 validated', $validated);
                $data = array_merge($data, $validated);
               session([self::SESSION_KEY => $data]);
                Log::info('Session data stored', ['habitsimulation_data' => $data]);
                return redirect()->route('habit.simulation.show', ['step' => 2]);
        } elseif ($step == 2) {
            $validated = $request->validate([
                    'ville' => 'required|string|max:255',
                    'rue' => 'required|string|max:255',
                    'code_postal' => 'required|string|max:10',
                ], [
                    'ville.required' => 'La ville est requise.',
                    'rue.required' => 'La rue est requise.',
                    'code_postal.required' => 'Le code postal est requis.',
                ]);
            Log::info('Step 2 validated', $validated);
                $data = array_merge($data, $validated);
                $years_living = now()->diffInYears(\Carbon\Carbon::parse($data['construction_year']));
                $base_rate = 500;
                $home_value_factor = $data['housing_value'] / 10000;
                $house_type_factor = match ($data['housing_type']) {
                    'APPARTEMENT' => 1.0,
                    'MAISON' => 1.2,
                    'PAVILLON' => 1.3,
                    'STUDIO' => 1.5,
                    'LOFT' => 1.4,
                    'VILLA' => 1.6,
                };
                $occupation_factor = match ($data['occupancy_status']) {
                    'Locataire' => 1.1,
                    'Propriétaire occupant' => 1.2,
                    'Propriétaire non-occupant' => 0.9,
                };
                $age_factor = $years_living < 2 ? 1.4 : ($years_living < 5 ? 1.2 : 1.0);

                $registration_factor = 1.0; // Assuming no additional registration factor for now
                $formules_choisis = [
                    'essentiel' => round($base_rate * $home_value_factor * $house_type_factor * $occupation_factor * $age_factor * $registration_factor , 2),
                    'confort' => round($base_rate * 1.5 * $home_value_factor * $house_type_factor * $occupation_factor * $age_factor * $registration_factor , 2),
                    'premium' => round($base_rate * 2.0 * $home_value_factor * $house_type_factor * $occupation_factor * $age_factor * $registration_factor , 2),
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
                $logement = Logement::create([
                    'housing_type' => $data['housing_type'],
                    'surface_area' => $data['surface_area'],
                    'housing_value' => $data['housing_value'],
                    'construction_year' => $data['construction_year'],
                    'occupancy_status' => $data['occupancy_status'],
                    'ville' => $data['ville'],
                    'rue' => $data['rue'],
                    'code_postal' => $data['code_postal'],
                ]);
                $devis_auto = DevisHabitation::create([
                    'id_devis' => $devis->id,
                    'id_logement' => $logement->id,
                    'formules_choisis' => json_encode([$formules_choisis]),
                ]);
                Log::info('Step 2 processed', [
                    'session_id' => $session->id,
                    'devis_id' => $devis->id,
                    'logement_id' => $logement->id,
                    'formules_choisis' => $formules_choisis
                ]);
                $data['formules_choisis'] = $formules_choisis;
                $request->session()->put('habitsimulation_data', $data);
                Log::info('Redirecting to Step 3', ['habitsimulation_data' => $data]);
                return redirect()->route('habit.simulation.show', ['step' => 3]);
       }
            Log::warning('Invalid step', ['step' => $step]);
            return redirect()->route('habit.simulation.show', ['step' => 1]);

        } catch (\Exception $e) {
            Log::error('Store method error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('habit.simulation.show', ['step' => 1])->withErrors(['error' => 'Une erreur est survenue. Veuillez réessayer.']);
        }
    }


        public function reset(Request $request)
    {
        $request->session()->forget('habitsimulation_data');
        Log::info('Session reset');
        return redirect()->route('habit.simulation.show', ['step' => 1]);
    }
}
