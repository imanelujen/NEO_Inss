<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook signature verification failed'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $contrat_id = $paymentIntent->metadata->contrat_id;
                $devis_id = $paymentIntent->metadata->devis_id;

                $contrat = Contrat::find($contrat_id);
                $paiement = Paiement::where('payment_method', $paymentIntent->payment_method)->first();

                if ($contrat && $paiement) {
                    $contrat->update(['status' => 'ACTIVE']);
                    $paiement->update(['status' => 'completed', 'payment_date' => now()]);
                    Log::info('Webhook: Payment succeeded', [
                        'contrat_id' => $contrat_id,
                        'devis_id' => $devis_id,
                        'paiement_id' => $paiement->id,
                        'payment_intent_id' => $paymentIntent->id,
                    ]);
                }
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $contrat_id = $paymentIntent->metadata->contrat_id;
                $devis_id = $paymentIntent->metadata->devis_id;

                $paiement = Paiement::where('payment_method', $paymentIntent->payment_method)->first();
                if ($paiement) {
                    $paiement->update(['status' => 'failed']);
                    Log::info('Webhook: Payment failed', [
                        'contrat_id' => $contrat_id,
                        'devis_id' => $devis_id,
                        'paiement_id' => $paiement->id,
                        'payment_intent_id' => $paymentIntent->id,
                    ]);
                }
                break;

            default:
                Log::info('Unhandled webhook event', ['event_type' => $event->type]);
        }

        return response()->json(['status' => 'success'], 200);
    }
}