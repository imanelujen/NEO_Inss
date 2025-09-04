<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SimulationSession;
use App\Models\Devis;
use App\Models\DevisHabitation;
use App\Models\logement;
use App\Mail\QuoteMail;
use App\Models\Contrat;
use App\Models\Contrat_habitation;
use App\Models\Vehicule;
use App\Models\Agence;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;



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
            $devis_habitation = DevisHabitation::where('id_devis', $data['devis_id'])->firstOrFail();

            $offer_data = json_decode($devis->OFFRE_CHOISIE, true);
            $data['devis_status'] = $devis->status;
            $data['montant_base'] = $devis->montant_base;
            $data['selected_offer'] = $offer_data['offer'] ?? 'none';
            $calculation_factors = json_decode($devis_habitation->calculation_factors, true) ?? [];
            // Recalculate formules_choisis based on original montant_base (essentiel)
            $base_amount = $devis->status == 'BROUILLON' ? $devis->montant_base :
            $devis->montant_base / ($offer_data['offer'] == 'excellence' ? 1.5 :
            ($offer_data['offer'] == 'confort' ? 1.2 : 1.0));
            $data['formules_choisis'] = json_decode($devis_habitation->formules_choisis, true);
            $data['calculation_factors'] = [];
            session(['habit_data' => $data]);
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
                    'STUDIO' => 0.9,
                    'LOFT' => 0.9,
                    'VILLA' => 1.5,
                };
                $occupation_factor = match ($data['occupancy_status']) {
                    'Locataire' => 1.0,
                    'Propriétaire occupant' => 1.2,
                    'Propriétaire non-occupant' => 1.1,
                };
                $age_factor = $years_living < 5 ? 0.9 : ($years_living < 15 ? 1 : 1.3);
                $housing_value = $data['housing_value']/10000;

                $registration_factor = 1.0; // Assuming no additional registration factor for now
                $degat_eau_factor=50;
                $incendie_factor=70.0;
                $protection_juridique_factor=30.0;
                $assistance_factor=50;

            $essentiel = round($base_rate + ($house_type_factor * $occupation_factor*$age_factor), 2);
                $confort = round(
                    $essentiel
                    + $housing_value
                    + $degat_eau_factor
                    + $incendie_factor
                    + $protection_juridique_factor,
                    2
                );
                $excellence = round(
                    $confort
                    + $assistance_factor
                    + $protection_juridique_factor
                    + (0.5 ),
                    2
                );
                $formules_choisis = [
                    'essentiel' => $essentiel,
                    'confort' => $confort,
                    'excellence' => $excellence,
                ];
                $calculation_factors = [
                    'base_rate' => $base_rate,
                    'home_value_factor' => $home_value_factor,
                    'house_type_factor' => $house_type_factor,
                    'occupation_factor' => $occupation_factor,
                    'age_factor' => $age_factor,
                    'registration_factor' => $registration_factor,
                    'degat_eau_factor' => $degat_eau_factor,
                    'incendie_factor' => $incendie_factor,
                    'protection_juridique_factor' => $protection_juridique_factor,
                    'assistance_factor' => $assistance_factor,
                ];

                $session = SimulationSession::create([
                    'date_debut' => now(),
                    'donnees_temporaires' => json_encode([$data]),
                ]);
                $devis = Devis::create([
                    'date_creation' => now(),
                    'date_expiration' => now()->addDays(30),
                    'montant_base' => $formules_choisis['essentiel'],
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
                $devis_habitation = DevisHabitation::create([
                    'id_devis' => $devis->id,
                    'id_logement' => $logement->id,
                    'formules_choisis' => json_encode($formules_choisis),
                ]);
                Log::info('Step 2 processed', [
                    'session_id' => $session->id,
                    'devis_id' => $devis->id,
                    'logement_id' => $logement->id,
                    'formules_choisis' => json_encode($formules_choisis),
                    'calculation_factors' => json_encode($calculation_factors),
                ]);
                $data['formules_choisis'] = $formules_choisis;
                //$request->session()->put('habitsimulation_data', $data);
                Log::info('Home quote saved', ['devis_id' => $devis->id]);

                $sessionData['devis_id'] = $devis->id;
                session(['habit_data' => $sessionData]);

                return view('habitation.habitform', [
                    'step' => 3,
                    'data' => array_merge($data, [
                        'formules_choisis' => $formules_choisis,
                        'devis_id' => $devis->id,
                        'devis_status' => 'BROUILLON',
                        'montant_base' => $formules_choisis['essentiel'],
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
        $logement = Logement::findOrFail($devisHabitation->id_logement);
        $offer_data = json_decode($devis->OFFRE_CHOISIE, true);
        $sessionData = session()->get('habit_data', []);


        // Recalculate formules_choisis
        $data['devis_id'] = $devis_id;
        $data['housing_type'] = $logement->housing_type;
        $data['surface_area'] = $logement->surface_area;
        $data['housing_value'] = $logement->housing_value;
        $data['construction_year'] = $logement->construction_year;
        $data['rue'] = $logement->rue;
        $data['registration_date'] = $logement->registration_date;
        $data['occupancy_status'] = $logement->occupancy_status;
        $data['selected_offer'] = $devis->OFFRE_CHOISIE ? json_decode($devis->OFFRE_CHOISIE, true)['offer'] : null;
        $data['montant_base'] = $devis->montant_base;
        $data['devis_status'] = $devis->status;
        $data['formules_choisis'] = json_decode($devisHabitation->formules_choisis, true);
        session(['habit_data' => $data, 'intended_devis_id' => $devis_id, 'type' => 'habitation']);

      return view('habitation.result', compact('data', 'devis_id'));
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
            $devisHabitation = DevisHabitation::where('id_devis', $devis_id)->firstOrFail();
            $formules_choisis = json_decode($devisHabitation->formules_choisis, true);
            $montant_base = $formules_choisis[$validated['offer']];
            // Base amount is the original montant_base (essentiel)


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

    $main_devis = Devis::findOrFail($devis_id);
    $devis_habitation = DevisHabitation::where('id_devis', $devis_id)->firstOrFail();
    $logement = Logement::findOrFail($devis_habitation->id_logement);

    if ($main_devis->status !== 'FINALISE') {
        return redirect()->route('habitation.result', ['devis_id' => $devis_id])
            ->withErrors(['error' => 'Veuillez sélectionner une formule avant de télécharger.']);
    }

    // OFFRE_CHOISIE is stored like ['offer' => 'essentiel'|'confort'|'excellence']
    $offerData = json_decode($main_devis->OFFRE_CHOISIE, true) ?: [];
    $selectedOffer = $offerData['offer'] ?? null; // fallback if somehow missing

    $data = [
        'housing_type'   => $logement->housing_type,
        'surface_area'   => $logement->surface_area,
        'housing_value'  => $logement->housing_value,
        'ville'          => $logement->ville,
        'rue'            => $logement->rue,
        'selected_offer' => $selectedOffer,          // <- fixed
        'montant_base'   => $main_devis->montant_base,
    ];

    $pdf = PDF::loadView('habitation.pdf', compact('data'));

    return $pdf->download('devis_habitation_' . $devis_id . '.pdf');
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
        if (!Auth::guard('api_clients')->check()) {
        session(['intended_devis_id' => $devis_id, 'type' => 'habitation']);
        Log::info('User not authenticated, redirecting to register', ['devis_id' => $devis_id]);
        return redirect()->route('register.show');
        }

        Log::info('User authenticated, redirecting to documents', [
        'devis_id' => $devis_id,
        'client_id' => Auth::guard('api_clients')->id(),
         ]);
       return redirect()->route('habit.documents', ['devis_id' => $devis_id]);
    }

         public function showDocuments($devis_id)
    {
        Log::info('Showing documents page', [
            'devis_id' => $devis_id,
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
           // 'client_id' => Auth::guard('api_clients')->id(),
        ]);

        $devis = Devis::findOrFail($devis_id);
        $agences = Agence::all();
        $data = session('habit_data', []);
        $data['devis_id'] = $devis_id;
        session(['habit_data' => $data, 'intended_devis_id' => $devis_id, 'type' => 'habit']);

        Log::info('Showing documents page', [
            'devis_id' => $devis_id,
            'agences_count' => $agences->count(),
            'client_id' => Auth::guard('api_clients')->id(),
            'jwt_token' => session('jwt_token'),
        ]);
        return view('habitation.documents', compact('devis_id', 'agences', 'data'));
    }

       public function storeDocuments(Request $request, $devis_id)
    {
         Log::info('📥 Incoming request', $request->all());

         try {
           $validated = $request->validate([
            'titre_foncier' => 'required|file|mimes:jpg,png|max:2048',
            'cin_recto' => 'required|file|mimes:jpg,png|max:2048',
            'cin_verso' => 'required|file|mimes:jpg,png|max:2048',
            'agence_id' => 'required|exists:agences,id',
           ]);
           Log::info('✅ Validation passed', $validated);
         } catch (\Illuminate\Validation\ValidationException $e) {
         Log::error('❌ Validation failed', $e->errors());
           throw $e; // will redirect back with errors
           }

    $client = Auth::guard('api_clients')->user();
    Log::info('👤 Client found?', ['client' => $client]);

    $devis = Devis::findOrFail($devis_id);

if ($devis->typedevis !== 'HABIT') {
    Log::error('❌ Wrong flow: tried to use habitation controller with AUTO devis', [
        'devis_id' => $devis_id,
        'typedevis' => $devis->typedevis,
    ]);
    abort(400, 'Mauvais type de devis pour ce flux.');
}

    Log::info('📑 Devis found?', ['devis' => $devis]);

    $devisHabitation = DevisHabitation::where('id_devis', $devis_id)->first();
    Log::info('🏠 DevisHabitation found?', ['devisHabitation' => $devisHabitation]);

    $logement = logement::find($devisHabitation->id_logement ?? null);
    Log::info('🏡 Logement found?', ['logement' => $logement]);

        // Store files in storage/app/public/documents/{client_id}
        $filePaths = [];
        foreach (['titre_foncier', 'cin_recto', 'cin_verso'] as $fileType) {
            $file = $request->file($fileType);
            $path = $file->storeAs(
                "documents/{$client->id}",
                "{$fileType}_{$devis_id}." . $file->getClientOriginalExtension(),
                'public'
            );
            $filePaths[$fileType] = $path;
            Log::info('File stored', ['type' => $fileType, 'path' => $path]);
        }

        // Create or update contract
         $contrat = Contrat::updateOrCreate(
            ['id_devis' => $devis_id, 'id_client' => $client->id],
            [
                'id_agent' => $validated['agence_id'],
                'start_date' => now()->addDay()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'prime' => $devis->montant_base,
                'status' => 'PENDING',
            ]
        );

        Log::info('Contract created/updated', [
            'contrat_id' => $contrat->id,
            'devis_id' => $devis_id,
            'client_id' => $client->id,
            'agence_id' => $validated['agence_id'],
        ]);


          $contratHabitation = contrat_habitation::updateOrCreate(
            ['id_contrat' => $contrat->id],
            [
                'id_logement' => $logement->id,
                'garanties' => json_decode($devis->OFFRE_CHOISIE, true)['offer'] == 'essentiel' ? json_encode(['RC']) :
                              (json_decode($devis->OFFRE_CHOISIE, true)['offer'] == 'confort' ? json_encode(['RC', 'Degâts des eaux', 'INCENDIE']) :
                              json_encode(['RC', 'Degâts des eaux', 'INCENDIE','BRIS DE GLACE', 'VOL', 'CATASTROPHE NATURELLE','ASSISTANCE HABITATION'])),
                'titre_foncier_path' => $filePaths['titre_foncier'],
                'cin_recto_path' => $filePaths['cin_recto'],
                'cin_verso_path' => $filePaths['cin_verso'],
                'franchise' => 200.00,
            ]
        );

        Log::info('Contract created/updated', [
            'contrat_id' => $contrat->id,
            'contrat_habit_id' => $contratHabitation->id,
            'logement' => $logement->id,
            'devis_id' => $devis_id,
            'client_id' => $client->id,
            'agence_id' => $validated['agence_id'],
        ]);


        Log::info('✅ Ready to redirect to appointment', ['devis_id' => $devis_id]);
        return redirect()->route('habit.appointment.create', ['devis_id' => $devis_id])
        ->with('success', 'Documents envoyés avec succès.');

    }

        public function showcheckout(Request $request, $devis_id)
    {
        Log::info('Showing checkout habitation form', [
            'devis_id' => $devis_id,
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'client_id' => Auth::guard('api_clients')->id(),
        ]);

        $devis = Devis::findOrFail($devis_id);
        $contrat = Contrat::where('id_devis', $devis_id)
            ->where('id_client', Auth::guard('api_clients')->id())
            ->firstOrFail();
        $contratHabitation = $contrat->contratHabitation;
//added for garanties display
        $garanties = $contratHabitation && $contratHabitation->garanties
    ? (is_array($contratHabitation->garanties) ? $contratHabitation->garanties : json_decode($contratHabitation->garanties, true))
    : [];

        $paymentDetails = [
            'start_date' => $contrat->start_date,
            'amount' => $devis->montant_base - 100,
            'garanties' => $garanties,
        ];

        return view('habitation.appointement', [
            'paymentDetails' => $paymentDetails,
            'devis_id' => $devis_id,
        ]);
    }

    public function storeSubscription(Request $request, $devis_id)
    {
        Log::info('storeSubscription called', ['devis_id' => $devis_id, 'input' => $request->all()]);
        $client = auth('api_clients')->user();
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
                    'housing_type' => $data['housing_type'],
                    'surface_area' => $data['surface_area'],
                    'housing_value' => $data['housing_value'],
                    'construction_year' => $data['construction_year'],
                    'occupancy_status' => $data['occupancy_status'],
                    'ville' => $data['ville'],
                    'rue' => $data['rue'],
                    'code_postal' => $data['code_postal'],
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

    public function createAppointment($devis_id)
{
    return view('habitation.appointement', compact('devis_id'));
}
     public function storeAppointment(Request $request, $devis_id)
    {
        $request->validate([
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
        ]);

        Appointment::create([
            'client_id' => auth()->id(), // client logged in
            'devis_habitation_id' => $devis_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending'
        ]);

        return redirect()->route('habit.result', $devis_id)
            ->with('success', 'Votre rendez-vous a été enregistré. Nous allons vous contacter pour confirmation.');
    }

}
