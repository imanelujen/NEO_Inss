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
        return view('auto.form', compact('step', 'data','posts'));
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
                    'vehicle_value.min' => 'La valeur doit être supérieure ou égale à 1000 DH.',
                    'registration_date.required' => 'La date de mise en circulation est requise.',
                    'registration_date.before' => 'La date doit être antérieure à aujourd\'hui.',
                ]);
                Log::info('Step 1 validated', $validated);
                $data = array_merge($data, $validated);
                $request->session()->put('simulation_data', $data);
                Log::info('Session data stored', ['simulation_data' => $data]);
                return redirect()->route('auto.show', ['step' => 2]);
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
                    'OFFRE_CHOISIE' => json_encode(['offer' => 'none']),
                    'status' => 'BROUILLON',
                    'typedevis' => 'AUTO',
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
                return redirect()->route('auto.show', ['step' => 3]);
       }
            Log::warning('Invalid step', ['step' => $step]);
            return redirect()->route('auto.show', ['step' => 1]);

        } catch (\Exception $e) {
            Log::error('Store method error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('auto.show', ['step' => 1])->withErrors(['error' => 'Une erreur est survenue. Veuillez réessayer.']);
        }
    }

    public function reset(Request $request)
    {
        $request->session()->forget('simulation_data');
        Log::info('Session reset');
        return redirect()->route('auto.show', ['step' => 1]);
    }
    public function selectOffer(Request $request, $devis_id)
    {
        $validated = $request->validate([
            'offer' => 'required|in:tiers,tous_risques,assistance',
        ]);

        try {
            $devis = Devis::findOrFail($devis_id);
            $offer_factors = [
                'tiers' => 1.0,
                'tous_risques' => 1.3,
                'assistance' => 1.1,
            ];
            $montant_base = $devis->montant_base * $offer_factors[$validated['offer']];
            $devis->update([
                'OFFRE_CHOISIE' => json_encode(['offer' => $validated['offer']]),
                'montant_base' => $montant_base,
                'status' => 'FINALISE',
            ]);
            $devisAuto = DevisAuto::where('id_devis', $devis_id)->firstOrFail();
            $devisAuto->update(['quote_amount' => $montant_base]);

            return redirect()->route('auto.result', ['devis_id' => $devis_id])
                ->with('success', 'Offre sélectionnée avec succès.');
        } catch (\Exception $e) {
            Log::error('Offer selection error: ' . $e->getMessage());
            return redirect()->route('auto.result', ['devis_id' => $devis_id])
                ->withErrors(['error' => 'Échec de la sélection de l\'offre.']);
        }
    }

    public function downloadQuote(Request $request, $devis_id)
    {
        $devis = DevisAuto::where('id_devis', $devis_id)->firstOrFail();
        $main_devis = Devis::findOrFail($devis_id);
        if ($main_devis->status !== 'FINALISE') {
            return redirect()->route('auto.result', ['devis_id' => $devis_id])
                ->withErrors(['error' => 'Veuillez sélectionner une offre avant de télécharger.']);
        }
        $pdf = PDF::loadView('auto.pdf', ['quote' => $devis, 'offer' => json_decode($main_devis->OFFRE_CHOISIE, true)]);
        return $pdf->download('devis_auto_' . $devis->id . '.pdf');
    }

    public function emailQuote(Request $request, $devis_id)
    {
        $request->validate(['email' => 'required|email']);
        $devis = DevisAuto::where('id_devis', $devis_id)->firstOrFail();
        $main_devis = Devis::findOrFail($devis_id);
        if ($main_devis->status !== 'FINALISE') {
            return redirect()->route('auto.result', ['devis_id' => $devis_id])
                ->withErrors(['error' => 'Veuillez sélectionner une offre avant d\'envoyer.']);
        }

        try {
            Mail::to($request->email)->send(new AutoQuoteMail($devis, json_decode($main_devis->OFFRE_CHOISIE, true)));
            $main_devis->update(['status' => 'ENVOYE']);
            return redirect()->route('auto.result', ['devis_id' => $devis_id])
                ->with('success', 'Devis envoyé par e-mail avec succès.');
        } catch (\Exception $e) {
            Log::error('Email sending error: ' . $e->getMessage());
            return redirect()->route('auto.result', ['devis_id' => $devis_id])
                ->withErrors(['error' => 'Échec de l\'envoi de l\'e-mail.']);
        }
    }

    public function subscribe(Request $request, $devis_id)
    {
        $main_devis = Devis::findOrFail($devis_id);
        if ($main_devis->status !== 'FINALISE') {
            return redirect()->route('auto.result', ['devis_id' => $devis_id])
                ->withErrors(['error' => 'Veuillez sélectionner une offre avant de souscrire.']);
        }
        if (!auth('api')->check()) {
            return redirect()->route('login.show')->with(['devis_id' => $devis_id, 'type' => 'auto']);
        }
        $devis = DevisAuto::where('id_devis', $devis_id)->firstOrFail();
        return view('auto.subscribe', ['devis' => $devis, 'offer' => json_decode($main_devis->OFFRE_CHOISIE, true)]);
    }

    public function storeSubscription(Request $request, $devis_id)
    {
        $client = auth('api')->user();
        if (!$client) {
            return redirect()->route('login.show')->with(['devis_id' => $devis_id, 'type' => 'auto']);
        }

        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'garanties' => 'required|array',
            'franchise' => 'required|numeric|min:0',
        ]);

        try {
            $main_devis = Devis::findOrFail($devis_id);
            $devis = DevisAuto::where('id_devis', $devis_id)->firstOrFail();
            $vehicule = Vehicule::create([
                'type_vehicule' => $devis->vehicle_type,
                'marque' => $devis->brand,
                'modele' => $devis->model,
                'annee' => $devis->year,
            ]);
            $conducteur = Conducteur::create([
                'date_permis' => $devis->license_date,
                'bonus_malus' => $devis->bonus_malus,
                'historique_accidents' => $devis->accident_history,
            ]);

            $contrat = Contrat::create([
                'type_contrat' => 'AUTO',
                'id_client' => $client->id,
                'id_devis' => $devis->id_devis,
                'id_agent' => 1, // Default agent; adjust as needed
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'prime' => $devis->quote_amount,
                'statut' => 'ACTIF',
            ]);

            ContratAuto::create([
                'id_contrat' => $contrat->id,
                'id_vehicule' => $vehicule->id,
                'id_conducteur' => $conducteur->id,
                'franchise' => $validated['franchise'],
                'garanties' => $validated['garanties'],
            ]);

            $main_devis->update(['status' => 'ACCEPTE']);
            return redirect()->route('auto.result', ['devis_id' => $devis_id])
                ->with('success', 'Souscription effectuée avec succès.');
        } catch (\Exception $e) {
            Log::error('Subscription error: ' . $e->getMessage());
            return redirect()->route('auto.subscribe', ['devis_id' => $devis_id])
                ->withErrors(['error' => 'Échec de la souscription.']);
        }
    }



}
