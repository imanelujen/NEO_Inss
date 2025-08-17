<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SimulationSession;
use App\Models\Devis;
use App\Models\DevisHabitation;
use App\Models\logement;



class habitSimulerController extends Controller
{

  public function show(Request $request)
    {
        Log::info('show called', ['step' => $request->query('step', 1), 'session_data' => session('habit_data', [])]);
        $step = $request->query('step', 1);
        $data = session('habit_data', []);

        if ($step == 3 && !isset($data['devis_id'])) {
            Log::warning('Attempted to access Step 3 without devis_id', ['session_data' => $data]);
            return redirect()->route('habit.simulation.show', ['step' => 1])
                ->withErrors(['error' => 'Veuillez compléter les étapes précédentes.']);
        }

        if ($step == 3 && isset($data['devis_id'])) {
            $devis = Devis::find($data['devis_id']);
            if (!$devis) {
                Log::error('Devis not found for devis_id', ['devis_id' => $data['devis_id']]);
                return redirect()->route('habit.simulation.show', ['step' => 1])
                    ->withErrors(['error' => 'Devis non trouvé. Veuillez recommencer.']);
            }
            $offer_data = json_decode($devis->OFFRE_CHOISIE, true);
            $data['devis_status'] = $devis->status;
            $data['montant_base'] = $devis->montant_base;
            $data['selected_offer'] = $offer_data['offer'] ?? 'none';
            // Recalculate formules_choisis based on original montant_base (essentiel)
            $base_amount = $devis->status == 'BROUILLON' ? $devis->montant_base :
            $devis->montant_base / ($offer_data['offer'] == 'excellence' ? 1.5 :
            ($offer_data['offer'] == 'confort' ? 1.2 : 1.0));
            $data['formules_choisis'] = [
                'essentiel' => $base_amount,
                'confort' => $base_amount * 1.2,
                'excellence' => $base_amount * 1.5,
            ];
        }

        return view('habitation.habitform', ['step' => $step, 'data' => $data, 'posts' => []]);
    }

     public function store(Request $request) {
        Log::info('habitSimulerController store called', [
            'input' => $request->all(),
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);
        try {
            $step = $request->input('step', 1);
            $sessionData = session()->get('habit_data', []);

            if ($step == 3) {
                Log::warning('Invalid step 3 submission in store method', [
                    'input' => $request->all(),
                    'expected_route' => route('habit.select_offer', ['devis_id' => $request->input('devis_id', 'unknown')]),
                    'url' => $request->fullUrl()
                ]);
                return redirect()->route('habit.simulation.show', ['step' => 1])
                    ->withErrors(['error' => 'Action non autorisée. Veuillez sélectionner une formule via le formulaire approprié.']);
            }

        
        if ($step == 1) {
            $validated = $request->validate([
               'housing_type' => 'required|in:APPARTEMENT,MAISON,PAVILLON,STUDIO,LOFT,VILLA',
                    'surface_area' => 'required|numeric|min:10',
                    'housing_value' => 'required|numeric|min:10000',
                    'construction_year' => 'required|integer|min:1800|max:' . now()->year,
                    'occupancy_status' => 'required|in:Locataire,Propriétaire occupant,Propriétaire non-occupant',
            ],[
               'housing_type.required' => 'Le type de maison est requis.',
                    'surface_area.required' => 'La surface est requise.',
                    'housing_value.required' => 'La valeur de la maison est requise.',
                    'construction_year.integer' => 'L\'année de construction doit être un nombre entier.',
                    'construction_year.min' => 'L\'année de construction semble trop ancienne.',
                    'construction_year.max' => 'L\'année de construction ne peut pas être dans le futur.',
                    'occupancy_status.required' => 'Le statut d\'occupation est requis.',
                    'housing_value.required' => 'La valeur de la maison est requise.',
                    'housing_value.min' => 'La valeur doit être supérieure ou égale à 10000 DH.',
                ]);
                Log::info('Step 1 validated', $validated);
                $sessionData['step1'] = $validated;
                session(['habit_data' => $sessionData]);
                return redirect()->route('habit.simulation.show', ['step' => 2]);
        } 
        if ($step == 2) {
            if (empty($sessionData['step1'])) {
                    return redirect()->route('habit.simulation.show', ['step' => 1])
                        ->withErrors(['error' => 'Données de la propriété manquantes.']);
                }

            $validated = $request->validate([
                    'ville' => 'required|string|max:255',
                    'rue' => 'required|string|max:255',
                    'code_postal' => 'required|string|max:10',
                ], [
                    'ville.required' => 'La ville est requise.',
                    'rue.required' => 'La rue est requise.',
                    'code_postal.required' => 'Le code postal est requis.',
                ]);
             $sessionData['step2'] = $validated;
            $data = array_merge($sessionData['step1'], $validated);


                $constructionDate = \Carbon\Carbon::createFromDate((int) $data['construction_year'], 1, 1);
                $years_living = now()->diffInYears($constructionDate);
                Log::info('Years living calculated', ['years_living' => $years_living]);
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
                $montant_base = round($base_rate + $house_type_factor + $home_value_factor * $occupation_factor * $age_factor * $registration_factor, 2);

                $formules_choisis = [
                    'essentiel' => $montant_base,
                    'confort' => $montant_base * 1.2,
                    'excellence' => $montant_base * 1.5,
                ];
                $session = SimulationSession::create([
                    'date_debut' => now(),
                    'donnees_temporaires' => json_encode([$data]),
                ]);
                $devis = Devis::create([
                    'date_creation' => now(),
                    'date_expiration' => now()->addDays(30),
                    'montant_base' => $montant_base,
                    'OFFRE_CHOISIE' => json_encode(['offer' => 'none']),
                    'status' => 'BROUILLON',
                    'typedevis' => 'HABITATION',
                    'id_simulationsession' => $session->id,
                ]);
                Log::info('Valeur construction_year avant insert:', ['construction_year' => $data['construction_year']]);
                $logement = logement::create([
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
                    'formules_choisis' => json_encode($formules_choisis),
                ]);
                Log::info('Step 2 processed', [
                    'session_id' => $session->id,
                    'devis_id' => $devis->id,
                    'logement_id' => $logement->id,
                    'formules_choisis' => $formules_choisis
                ]);
                $data['formules_choisis'] = $formules_choisis;
                //$request->session()->put('habitsimulation_data', $data);
                Log::info('Home quote saved', ['devis_id' => $devis->id, 'montant_base' => $montant_base]);

                $sessionData['devis_id'] = $devis->id;
                session(['habit_data' => $sessionData]);

                return view('habitation.habitform', [
                    'step' => 3,
                    'data' => array_merge($data, [
                        'formules_choisis' => $formules_choisis,
                        'devis_id' => $devis->id,
                        'devis_status' => 'BROUILLON',
                        'montant_base' => $montant_base,
                        'selected_offer' => 'none'
                    ]),
                    'posts' => [],
                ]);
            }


        } catch (\Exception $e) {
            Log::error('habitSimulerController store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('habit.simulation.show', ['step' => $step])
                ->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()])
                ->withInput();
        }
    }

  public function reset(Request $request)
    {
        $request->session()->forget('habit_data');
        Log::info('Session reset');
        return redirect()->route('habit.simulation.show', ['step' => 1]);
    }
    //select offer

   public function showQuote(Request $request, $devis_id)
    {
        Log::info('showQuote called', ['devis_id' => $devis_id]);
        $devis = Devis::findOrFail($devis_id);
        $devisHabitation = DevisHabitation::where('id_devis', $devis_id)->firstOrFail();
        $offer_data = json_decode($devis->OFFRE_CHOISIE, true);
        $sessionData = session()->get('habit_data', []);
        $data = array_merge($sessionData['step1'] ?? [], $sessionData['step2'] ?? []);

        // Recalculate formules_choisis
        $base_amount = $devis->status == 'BROUILLON' ? $devis->montant_base : $devis->montant_base / ($offer_data['offer'] == 'excellence' ? 1.5 : ($offer_data['offer'] == 'confort' ? 1.2 : 1.0));
        $formules_choisis = [
            'essentiel' => $base_amount,
            'confort' => $base_amount * 1.2,
            'excellence' => $base_amount * 1.5,
        ];

        return view('habitation.habitform', [
            'step' => 3,
            'data' => array_merge($data, [
                'devis_id' => $devis->id,
                'devis_status' => $devis->status,
                'montant_base' => $devis->montant_base,
                'selected_offer' => $offer_data['offer'] ?? 'none',
                'formules_choisis' => $formules_choisis,
            ]),
            'posts' => [],
        ]);
    }
    public function selectOffer(Request $request, $devis_id)
    {
        Log::info('selectOffer called', [
            'devis_id' => $devis_id,
            'input' => $request->all(),
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);

        $validated = $request->validate([
            'offer' => 'required|in:essentiel,confort,excellence',
            'devis_id' => 'required|exists:devis,id',
        ]);

        try {
            $devis = Devis::findOrFail($devis_id);
            $offer_factors = [
                'essentiel' => 1.0,
                'confort' => 1.2,
                'excellence' => 1.5,
            ];
            // Base amount is the original montant_base (essentiel)
            $base_amount = $devis->montant_base;
            if ($devis->status != 'BROUILLON') {
                $offer_data = json_decode($devis->OFFRE_CHOISIE, true);
                $base_amount = $devis->montant_base / ($offer_data['offer'] == 'excellence' ? 1.5 : ($offer_data['offer'] == 'confort' ? 1.2 : 1.0));
            }
            $montant_base = $base_amount * $offer_factors[$validated['offer']];
            $devis->update([
                'OFFRE_CHOISIE' => json_encode(['offer' => $validated['offer']]),
                'montant_base' => $montant_base,
                'status' => 'FINALISE',
            ]);
            $devisHabitation = DevisHabitation::where('id_devis', $devis_id)->firstOrFail();
            $devisHabitation->update(['quote_amount' => $montant_base]);

            Log::info('Offer selected', [
                'devis_id' => $devis_id,
                'offer' => $validated['offer'],
                'montant_base' => $montant_base,
                'status' => 'FINALISE'
            ]);

            return redirect()->route('habit.result', ['devis_id' => $devis_id])
                ->with('success', 'Formule sélectionnée avec succès.');
        } catch (\Exception $e) {
            Log::error('Offer selection error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('habit.simulation.show', ['step' => 3])
                ->withErrors(['error' => 'Échec de la sélection de la formule.']);
        }
    }

    public function downloadQuote(Request $request, $devis_id)
    {
        Log::info('downloadQuote called', ['devis_id' => $devis_id]);
        $devis = DevisHabitation::where('id_devis', $devis_id)->firstOrFail();
        $main_devis = Devis::findOrFail($devis_id);
        if ($main_devis->status !== 'FINALISE') {
            return redirect()->route('habitation.simulate', ['step' => 3])
                ->withErrors(['error' => 'Veuillez sélectionner une formule avant de télécharger.']);
        }
        $pdf = PDF::loadView('habitation.pdf', ['quote' => $devis, 'offer' => json_decode($main_devis->OFFRE_CHOISIE, true)]);
        return $pdf->download('devis_habitation_' . $devis->id . '.pdf');
    }

    public function emailQuote(Request $request, $devis_id)
    {
        Log::info('emailQuote called', ['devis_id' => $devis_id, 'input' => $request->all()]);
        $request->validate(['email' => 'required|email']);
        $devis = DevisHabitation::where('id_devis', $devis_id)->firstOrFail();
        $main_devis = Devis::findOrFail($devis_id);
        if ($main_devis->status !== 'FINALISE') {
            return redirect()->route('habitation.simulate', ['step' => 3])
                ->withErrors(['error' => 'Veuillez sélectionner une formule avant d\'envoyer.']);
        }

        try {
            Mail::to($request->email)->send(new QuoteMail($devis, json_decode($main_devis->OFFRE_CHOISIE, true)));
            $main_devis->update(['status' => 'ENVOYE']);
            return redirect()->route('habitation.simulate', ['step' => 3])
                ->with('success', 'Devis envoyé par e-mail avec succès.');
        } catch (\Exception $e) {
            Log::error('Email sending error: ' . $e->getMessage());
            return redirect()->route('habitation.simulate', ['step' => 3])
                ->withErrors(['error' => 'Échec de l\'envoi de l\'e-mail.']);
        }
    }

    public function subscribe(Request $request, $devis_id)
    {
        Log::info('subscribe called', ['devis_id' => $devis_id]);
        $main_devis = Devis::findOrFail($devis_id);
        if ($main_devis->status !== 'FINALISE') {
            return redirect()->route('habit.simulation.show', ['step' => 3])
                ->withErrors(['error' => 'Veuillez sélectionner une formule avant de souscrire.']);
        }
        if (!auth('api')->check()) {
            session(['devis_id' => $devis_id, 'type' => 'habitation']);
            return redirect()->route('login.show');
        }
        $devis = DevisHabitation::where('id_devis', $devis_id)->firstOrFail();
        return view('habitation.subscribe', ['devis' => $devis, 'offer' => json_decode($main_devis->OFFRE_CHOISIE, true)]);
    }

    public function storeSubscription(Request $request, $devis_id)
    {
        Log::info('storeSubscription called', ['devis_id' => $devis_id, 'input' => $request->all()]);
        $client = auth('api')->user();
        if (!$client) {
            return redirect()->route('login.show')->with(['devis_id' => $devis_id, 'type' => 'habitation']);
        }

        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'garanties' => 'required|array',
            'franchise' => 'required|numeric|min:0',
        ]);

        try {
            $main_devis = Devis::findOrFail($devis_id);
            $devis = DevisHabitation::where('id_devis', $devis_id)->firstOrFail();

             //why creating the logemen recors (haven't been created before)
            $logement = logement::create([
                'type_logement' => $devis->property_type,
                'surface' => $devis->surface_area,
                'adresse' => $devis->location,
                'nombre_pieces' => $devis->number_of_rooms,
                'valeur_estimee' => $devis->estimated_value,
            ]);

            $contrat = Contrat::create([
                'type_contrat' => 'HABITATION',
                'id_client' => $client->id,
                'id_devis' => $devis->id_devis,
                'id_agent' => 1,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'prime' => $devis->quote_amount,
                'statut' => 'ACTIF',
            ]);

            ContratHabitation::create([
                'id_contrat' => $contrat->id,
                'id_logement' => $logement->id,
                'franchise' => $validated['franchise'],
                'garanties' => $validated['garanties'],
            ]);

            $main_devis->update(['status' => 'ACCEPTE']);
            Log::info('Subscription completed', ['devis_id' => $devis_id, 'client_id' => $client->id]);
            return redirect()->route('habit.result', ['devis_id' => $devis_id])
                ->with('success', 'Souscription effectuée avec succès.');
        } catch (\Exception $e) {
            Log::error('Subscription error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('habit.subscribe', ['devis_id' => $devis_id])
                ->withErrors(['error' => 'Échec de la souscription.']);
        }
    }

}
