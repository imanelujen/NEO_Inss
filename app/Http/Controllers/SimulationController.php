<?php

namespace App\Http\Controllers;

use App\Models\SimulationSession;
use App\Models\Conducteur;
use App\Models\Devis;
use App\Models\DevisAuto;
use App\Models\Vehicule;
use App\Models\Agence;
use App\Models\Contrat;
use App\Models\Contrat_auto;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\AutoQuoteMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class SimulationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api_clients')->only(['subscribe', 'showDocuments', 'storeDocuments', 'showPayment', 'storePayment']);
    }

    public function show(Request $request)
    {
        $step = $request->query('step', 1);
        Log::info('Auto simulation show called', ['step' => $step]);

        $data = session('auto_data', []);

        if ($step == 3 && !isset($data['devis_id'])) {
            Log::warning('Attempted to access Step 3 without devis_id', ['session_data' => $data]);
            return redirect()->route('auto.show', ['step' => 1])
                ->withErrors(['error' => 'Veuillez compléter les étapes précédentes.']);
        }

        if ($step == 3 && isset($data['devis_id'])) {
            $devis = Devis::find($data['devis_id']);
            if (!$devis) {
                Log::error('Devis not found for devis_id', ['devis_id' => $data['devis_id']]);
                return redirect()->route('auto.show', ['step' => 1])
                    ->withErrors(['error' => 'Devis non trouvé. Veuillez recommencer.']);
            }
            $offer_data = json_decode($devis->OFFRE_CHOISIE, true);
            $devis_auto = DevisAuto::where('id_devis', $data['devis_id'])->firstOrFail();
            $calculation_factors = json_decode($devis_auto->calculation_factors, true) ?? [];
            $data['devis_status'] = $devis->status;
            $data['montant_base'] = $devis->montant_base;
            $data['selected_offer'] = $offer_data['offer'] ?? 'none';
            $data['formules_choisis'] = json_decode($devis_auto->formules_choisis, true);
            $data['calculation_factors'] = $calculation_factors;
            session(['auto_data' => $data]);
        }

        return view('auto.form', ['step' => $step, 'data' => $data, 'posts' => []]);
    }

    public function store(Request $request)
    {
        Log::info('store method called with step: ' . $request->input('step'));
        $step = $request->input('step', 1);
        $data = session('auto_data', []);
        Log::info('Current session data', ['auto_data' => $data]);

        try {
            if ($step == 1) {
                $validated = $request->validate([
                    'vehicle_type' => 'required|in:sedan,suv,truck,motorcycle',
                    'make' => 'required|string|max:255',
                    'model' => 'required|string|max:255',
                    'fuel_type' => 'required|in:ESSENCE,DIESEL,ELECTRIQUE,HYBRIDE',
                    'tax_horsepower' => 'required|integer|min:1',
                    'vehicle_value' => 'required|numeric|min:1000',
                    'registration_date' => 'required|date|before:today',
                ], [
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
                session(['auto_data' => $data, 'type' => 'auto']);
                Log::info('Session data stored', ['auto_data' => $data]);
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
                $years_driving = now()->diffInYears(Carbon::parse($data['date_obtention_permis']));

                // Calculate the base rate and factors
                $base_rate = 1840;
                $vehicle_value_factor = $data['vehicle_value'] / 1000;
                $tax_horsepower = $data['tax_horsepower'];
                $tax_horsepower_factor = $tax_horsepower <= 5 ? $tax_horsepower * 1 :
                    ($tax_horsepower <= 7 ? $tax_horsepower * 1.3 :
                    ($tax_horsepower <= 10 ? $tax_horsepower * 1.7 :
                    ($tax_horsepower <= 14 ? $tax_horsepower * 2.2 : $tax_horsepower * 3)));
                Log::info('Tax horsepower factor calculated', ['tax_horsepower_factor' => $tax_horsepower_factor]);
                $vehicle_type_factor = match ($data['vehicle_type']) {
                    'sedan' => 1.0,
                    'suv' => 1.2,
                    'truck' => 1.3,
                    'motorcycle' => 1.5,
                };
                $fuel_factor = match ($data['fuel_type']) {
                    'ESSENCE' => 1.0,
                    'DIESEL' => 1.2,
                    'ELECTRIQUE' => 0.9,
                    'HYBRIDE' => 1.0,
                };
                $age_factor = $years_driving < 2 ? 1.4 : ($years_driving < 5 ? 1.2 : 1.0);
                $registration_age = now()->diffInYears(Carbon::parse($data['registration_date']));
                $registration_factor = $registration_age > 10 ? 1.3 : ($registration_age > 5 ? 1.1 : 1.0);
                $bonus_malus = $data['bonus_malus'];

                // Optional coverages
                $dommage_collision_factor = $vehicle_value_factor * 30;
                $incendie_factor = $vehicle_value_factor * 5;
                $vol_factor = $vehicle_value_factor * 4;
                $bris_de_glace_factor = 300;
                $assistance_factor = 400;
                $protection_juridique_factor = 250;

                // Calculate quotes
                $basic = round($base_rate + ($fuel_factor * $tax_horsepower_factor), 2);
                $standard = round(
                    $basic
                    + $bris_de_glace_factor
                    + (0.2 * $vehicle_value_factor)
                    + $incendie_factor
                    + $vol_factor,
                    2
                );
                $premium = round(
                    $standard
                    + $assistance_factor
                    + $protection_juridique_factor
                    + (0.5 * $dommage_collision_factor),
                    2
                );

                $formules_choisis = [
                    'basic' => $basic,
                    'standard' => $standard,
                    'premium' => $premium,
                ];

                $calculation_factors = [
                    'base_rate' => $base_rate,
                    'tax_horsepower_factor' => $tax_horsepower_factor,
                    'vehicle_value_factor' => $vehicle_value_factor,
                    'vehicle_type_factor' => $vehicle_type_factor,
                    'fuel_factor' => $fuel_factor,
                    'age_factor' => $age_factor,
                    'registration_factor' => $registration_factor,
                    'bonus_malus' => $bonus_malus,
                    'dommage_collision_factor' => $dommage_collision_factor,
                    'incendie_factor' => $incendie_factor,
                    'vol_factor' => $vol_factor,
                    'bris_de_glace_factor' => $bris_de_glace_factor,
                    'assistance_factor' => $assistance_factor,
                    'protection_juridique_factor' => $protection_juridique_factor,
                ];

                $session = SimulationSession::create([
                    'date_debut' => now(),
                    'donnees_temporaires' => json_encode($data),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $devis = Devis::create([
                    'date_creation' => now(),
                    'date_expiration' => now()->addDays(30),
                    'montant_base' => $formules_choisis['basic'],
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
                    'historique_accidents' => json_encode($data['historique_accidents'] ? [$data['historique_accidents']] : []),
                    'date_obtention_permis' => $data['date_obtention_permis'],
                ]);
                $devis_auto = DevisAuto::create([
                    'id_devis' => $devis->id,
                    'id_vehicule' => $vehicule->id,
                    'id_conducteur' => $conducteur->id,
                    'formules_choisis' => json_encode($formules_choisis),
                    'calculation_factors' => json_encode($calculation_factors),
                ]);
                $data['devis_id'] = $devis->id;
                session(['auto_data' => $data, 'intended_devis_id' => $devis->id, 'type' => 'auto']);
                Log::info('Step 2 processed', [
                    'session_id' => $session->id,
                    'devis_id' => $devis->id,
                    'vehicule_id' => $vehicule->id,
                    'conducteur_id' => $conducteur->id,
                    'formules_choisis' => $formules_choisis,
                    'calculation_factors' => $calculation_factors,
                ]);
                return redirect()->route('auto.show', ['step' => 3]);
            }
            Log::warning('Invalid step', ['step' => $step]);
            return redirect()->route('auto.show', ['step' => 1]);
        } catch (\Exception $e) {
            Log::error('Store method error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('auto.show', ['step' => 1])
                ->withErrors(['error' => 'Une erreur est survenue. Veuillez réessayer.']);
        }
    }

    public function reset(Request $request)
    {
        session()->forget(['auto_data', 'intended_devis_id', 'type', 'jwt_token']);
        Log::info('Session reset');
        return redirect()->route('auto.show', ['step' => 1]);
    }

    public function showQuote(Request $request, $devis_id)
    {
        Log::info('showQuote called', ['devis_id' => $devis_id]);
        $devis = Devis::findOrFail($devis_id);
        $devis_auto = DevisAuto::where('id_devis', $devis_id)->firstOrFail();
        $offer_data = json_decode($devis->OFFRE_CHOISIE, true);
        $data = session('auto_data', []);
        $calculation_factors = json_decode($devis_auto->calculation_factors, true) ?? [];

        $base_amount = $devis->status == 'BROUILLON' ? $devis->montant_base :
            $devis->montant_base / ($offer_data['offer'] == 'premium' ? 1.5 :
            ($offer_data['offer'] == 'standard' ? 1.2 : 1.0));
        $formules_choisis = [
            'basic' => $base_amount,
            'standard' => $base_amount * 1.2,
            'premium' => $base_amount * 1.5,
        ];

        return view('auto.form', [
            'step' => 3,
            'data' => array_merge($data, [
                'devis_id' => $devis->id,
                'devis_status' => $devis->status,
                'montant_base' => $devis->montant_base,
                'selected_offer' => $offer_data['offer'] ?? 'none',
                'formules_choisis' => $formules_choisis,
                'calculation_factors' => $calculation_factors,
            ]),
            'posts' => [],
        ]);
    }

    public function showResult($devis_id)
    {
        $devis = Devis::findOrFail($devis_id);
        $devis_auto = DevisAuto::where('id_devis', $devis_id)->firstOrFail();
        $vehicule = Vehicule::findOrFail($devis_auto->id_vehicule);
        $conducteur = Conducteur::findOrFail($devis_auto->id_conducteur);
        $data = session('auto_data', []);
        $data['devis_id'] = $devis_id;
        $data['vehicle_type'] = $vehicule->vehicle_type;
        $data['make'] = $vehicule->make;
        $data['model'] = $vehicule->model;
        $data['fuel_type'] = $vehicule->fuel_type;
        $data['tax_horsepower'] = $vehicule->tax_horsepower;
        $data['vehicle_value'] = $vehicule->vehicle_value;
        $data['registration_date'] = $vehicule->registration_date;
        $data['date_obtention_permis'] = $conducteur->date_obtention_permis;
        $data['bonus_malus'] = $conducteur->bonus_malus;
        $data['selected_offer'] = $devis->OFFRE_CHOISIE ? json_decode($devis->OFFRE_CHOISIE, true)['offer'] : null;
        $data['montant_base'] = $devis->montant_base;
        $data['devis_status'] = $devis->status;
        $data['calculation_factors'] = json_decode($devis_auto->calculation_factors, true);
        $data['formules_choisis'] = json_decode($devis_auto->formules_choisis, true);
        session(['auto_data' => $data, 'intended_devis_id' => $devis_id, 'type' => 'auto']);

        return view('auto.form', compact('data', 'devis_id'));
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
            'offer' => 'required|in:basic,standard,premium',
            'devis_id' => 'required|exists:devis,id',
        ]);

        try {
            $devis = Devis::findOrFail($devis_id);
            $devis_auto = DevisAuto::where('id_devis', $devis_id)->firstOrFail();
            $formules_choisis = json_decode($devis_auto->formules_choisis, true);
            $montant_base = $formules_choisis[$validated['offer']];

            $devis->update([
                'OFFRE_CHOISIE' => json_encode(['offer' => $validated['offer']]),
                'montant_base' => $montant_base,
                'status' => 'FINALISE',
            ]);

            Log::info('Offer selected', [
                'devis_id' => $devis_id,
                'offer' => $validated['offer'],
                'montant_base' => $montant_base,
                'status' => 'FINALISE'
            ]);

            return redirect()->route('auto.result', ['devis_id' => $devis_id])
                ->with('success', 'Formule auto sélectionnée avec succès.');
        } catch (\Exception $e) {
            Log::error('Offer selection error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('auto.show', ['step' => 3])
                ->withErrors(['error' => 'Échec de la sélection de la formule.']);
        }
    }

    public function downloadQuote(Request $request, $devis_id)
    {
        Log::info('downloadQuote called', ['devis_id' => $devis_id]);
        $devis = DevisAuto::where('id_devis', $devis_id)->firstOrFail();
        $main_devis = Devis::findOrFail($devis_id);
        if ($main_devis->status !== 'FINALISE') {
            return redirect()->route('auto.result', ['devis_id' => $devis_id])
                ->withErrors(['error' => 'Veuillez sélectionner une offre avant de télécharger.']);
        }
        $pdf = Pdf::loadView('auto.pdf', ['quote' => $devis, 'offer' => json_decode($main_devis->OFFRE_CHOISIE, true)]);
        return $pdf->download('devis_auto_' . $devis->id . '.pdf');
    }

    public function emailQuote(Request $request, $devis_id)
    {
        Log::info('emailQuote called', ['devis_id' => $devis_id]);
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

        public function subscribe($devis_id)
        {
        Log::info('subscribe called', [
        'devis_id' => $devis_id,
        'jwt_token' => session('jwt_token'),
        'auth_check' => Auth::guard('api_clients')->check(),
        'client_id' => Auth::guard('api_clients')->id(),
        'session_id' => session()->getId(),
        'intended_devis_id' => session('intended_devis_id'),
        'type' => session('type'),
        ]);

        if (!Auth::guard('api_clients')->check()) {
        session(['intended_devis_id' => $devis_id, 'type' => 'auto']);
        Log::info('User not authenticated, redirecting to register', ['devis_id' => $devis_id]);
        return redirect()->route('register.show');
        }

        Log::info('User authenticated, redirecting to documents', [
        'devis_id' => $devis_id,
        'client_id' => Auth::guard('api_clients')->id(),
         ]);
       return redirect()->route('auto.documents', ['devis_id' => $devis_id]);
          }

     public function showDocuments($devis_id)
    {
        Log::info('Showing documents page', [
            'devis_id' => $devis_id,
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'client_id' => Auth::guard('api_clients')->id(),
        ]);

        $devis = Devis::findOrFail($devis_id);
        $agences = Agence::all();
        $data = session('auto_data', []);
        $data['devis_id'] = $devis_id;
        session(['auto_data' => $data, 'intended_devis_id' => $devis_id, 'type' => 'auto']);

        Log::info('Showing documents page', [
            'devis_id' => $devis_id,
            'agences_count' => $agences->count(),
            'client_id' => Auth::guard('api_clients')->id(),
            'jwt_token' => session('jwt_token'),
        ]);
        return view('auto.documents', compact('devis_id', 'agences', 'data'));
    }

    public function storeDocuments(Request $request, $devis_id)
    {
        Log::info('Storing documents', [
            'devis_id' => $devis_id,
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'client_id' => Auth::guard('api_clients')->id(),
        ]);

        $validated = $request->validate([
            'carte_grise' => 'required|file|mimes:jpg,png|max:2048',
            'permis' => 'required|file|mimes:jpg,png|max:2048',
            'cin_recto' => 'required|file|mimes:jpg,png|max:2048',
            'cin_verso' => 'required|file|mimes:jpg,png|max:2048',
            'agence_id' => 'required|exists:agences,id',
        ]);

        $client = Auth::guard('api_clients')->user();
        $devis = Devis::findOrFail($devis_id);
        $devisAuto = DevisAuto::where('id_devis', $devis_id)->firstOrFail();

        $vehicule = Vehicule::findOrFail($devisAuto->id_vehicule);
        $conducteur = Conducteur::findOrFail($devisAuto->id_conducteur);

        // Store files in storage/app/public/documents/{client_id}
        $filePaths = [];
        foreach (['carte_grise', 'permis', 'cin_recto', 'cin_verso'] as $fileType) {
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
        

          $contratAuto = Contrat_auto::updateOrCreate(
            ['id_contrat' => $contrat->id],
            [
                'id_vehicule' => $vehicule->id,
                'id_conducteur' => $conducteur->id,
                'garanties' => json_decode($devis->OFFRE_CHOISIE, true)['offer'] == 'basic' ? json_encode(['RC']) : 
                              (json_decode($devis->OFFRE_CHOISIE, true)['offer'] == 'standard' ? json_encode(['RC', 'BRIS DE GLACE', 'INCENDIE', 'VOL']) : 
                              json_encode(['RC', 'BRIS DE GLACE', 'INCENDIE', 'VOL', 'ASSISTANCE', 'PROTECTION JURIDIQUE'])),
                'carte_grise_path' => $filePaths['carte_grise'],
                'permis_path' => $filePaths['permis'],
                'cin_recto_path' => $filePaths['cin_recto'],
                'cin_verso_path' => $filePaths['cin_verso'],
                'franchise' => 200.00,
            ]
        );

        Log::info('Contract created/updated', [
            'contrat_id' => $contrat->id,
            'contrat_auto_id' => $contratAuto->id,
            'vehicule_id' => $vehicule->id,
            'conducteur_id' => $conducteur->id,
            'devis_id' => $devis_id,
            'client_id' => $client->id,
            'agence_id' => $validated['agence_id'],
        ]);

        return redirect()->route('auto.payment', ['devis_id' => $devis_id])
            ->with('success', 'Documents envoyés avec succès.');
    }

    public function showPayment(Request $request, $devis_id)
    {
        Log::info('Showing payment form', [
            'devis_id' => $devis_id,
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'client_id' => Auth::guard('api_clients')->id(),
        ]);

        $devis = Devis::findOrFail($devis_id);
        $contrat = Contrat::where('id_devis', $devis_id)
            ->where('id_client', Auth::guard('api_clients')->id())
            ->firstOrFail();
        $contratAuto = $contrat->contratAuto;
//added for garanties display
        $garanties = $contratAuto && $contratAuto->garanties
    ? (is_array($contratAuto->garanties) ? $contratAuto->garanties : json_decode($contratAuto->garanties, true))
    : [];

        $paymentDetails = [
            'start_date' => $contrat->start_date,
            'amount' => $devis->montant_base - 100,
            'garanties' => $garanties,
        ];

        return view('auto.payment', [
            'paymentDetails' => $paymentDetails,
            'devis_id' => $devis_id,
        ]);
    }


public function storePayment(Request $request, $devis_id)
    {
        Log::info('Processing payment', [
            'devis_id' => $devis_id,
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'client_id' => Auth::guard('api_clients')->id(),
        ]);

        $validated = $request->validate([
            'payment_method' => 'required|string',
        ]);

        $devis = Devis::findOrFail($devis_id);
        $contrat = Contrat::where('id_devis', $devis_id)
            ->where('id_client', Auth::guard('api_clients')->id())
            ->firstOrFail();

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Create PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => ($devis->montant_base - 100) * 100, // Convert MAD to cents
                'currency' => 'mad',
                'payment_method' => $validated['payment_method'],
                'confirmation_method' => 'manual',
                'confirm' => true,
                'description' => "Paiement pour contrat auto #{$contrat->id}, devis #{$devis_id}",
                'metadata' => [
                    'client_id' => Auth::guard('api_clients')->id(),
                    'devis_id' => $devis_id,
                    'contrat_id' => $contrat->id,
                ],
            ]);

            // Create paiement record
            $paiement = Paiement::create([
                'amount' => $devis->montant_base - 100,
                'payment_frequency' => 'manual',
                'status' => $paymentIntent->status === 'succeeded' ? 'completed' : 'pending',
                'payment_method' => $paymentIntent->payment_method,
                'payment_date' => now(),
            ]);

            if ($paymentIntent->status === 'succeeded') {
                $contrat->update(['status' => 'ACTIVE']);
                Log::info('Payment processed successfully', [
                    'contrat_id' => $contrat->id,
                    'devis_id' => $devis_id,
                    'paiement_id' => $paiement->id,
                    'payment_intent_id' => $paymentIntent->id,
                ]);

                return redirect()->route('auto.result', ['devis_id' => $devis_id])
                    ->with('success', 'Paiement effectué avec succès.');
            } else {
                Log::warning('PaymentIntent not succeeded', [
                    'contrat_id' => $contrat->id,
                    'devis_id' => $devis_id,
                    'paiement_id' => $paiement->id,
                    'payment_intent_status' => $paymentIntent->status,
                ]);
                return redirect()->route('auto.payment', ['devis_id' => $devis_id])
                    ->with('error', 'Le paiement n\'a pas pu être finalisé. Veuillez réessayer.');
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe payment error: ' . $e->getMessage(), [
                'contrat_id' => $contrat->id,
                'devis_id' => $devis_id,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('auto.payment', ['devis_id' => $devis_id])
                ->with('error', 'Erreur lors du paiement : ' . $e->getMessage());
        }
    }
}
?>