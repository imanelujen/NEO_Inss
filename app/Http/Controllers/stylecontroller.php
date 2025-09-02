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
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StyleController extends Controller
{





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

        return view('auto.formClaude', ['step' => $step, 'data' => $data, 'posts' => []]);
    }
    }



