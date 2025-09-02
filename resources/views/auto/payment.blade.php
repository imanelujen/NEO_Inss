<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - Neo Assurances</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-3xl bg-white shadow-lg rounded-xl p-6 sm:p-8 animate-fade-in">
        
        <!-- Header -->
        <div class="form-card-header rounded-lg mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold">Finaliser votre paiement</h1>
        </div>

        <!-- Alerts -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Contract details -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4">Détails du contrat</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-700 font-medium">Date de début du contrat :</p>
                    <p class="text-gray-600">{{ $paymentDetails['start_date'] }}</p>
                </div>
                <div>
                    <p class="text-gray-700 font-medium">Montant à payer :</p>
                    <p class="text-green-700 font-bold">{{ number_format($paymentDetails['amount'], 2) }} MAD</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-gray-700 font-medium">Garanties incluses :</p>
                    <ul class="list-disc list-inside text-gray-600 mt-1">
                        @foreach ($paymentDetails['garanties'] as $garantie)
                            <li>{{ $garantie }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Stripe form -->
        <form id="payment-form" method="POST" action="{{ route('auto.payment.store', ['devis_id' => $devis_id]) }}" class="space-y-4">
            @csrf

            <!-- Cardholder -->
            <input type="text" name="cardholder_name" 
                   placeholder="Nom du porteur de la carte" 
                   class="w-full border rounded-lg p-3 text-sm sm:text-base focus:ring-2 focus:ring-green-500" 
                   required>

            <!-- Stripe Elements -->
            <div id="card-number-element" class="p-3 border rounded-lg"></div>
            <div id="card-expiry-element" class="p-3 border rounded-lg"></div>
            <div id="card-cvc-element" class="p-3 border rounded-lg"></div>

            <!-- Errors -->
            <div id="card-errors" role="alert" class="text-red-600 text-sm"></div>

            <!-- Submit -->
            <button type="submit" id="submit-button" 
                    class="w-full bg-green-600 text-white px-4 py-3 rounded-lg text-sm sm:text-base">
                Confirmer le paiement
            </button>
        </form>
    </div>

    <!-- Stripe Script -->
    <script>
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');
        const elements = stripe.elements();

        const style = {
            base: {
                fontSize: '16px',
                color: '#32325d',
                fontFamily: 'Arial, sans-serif',
                '::placeholder': { color: '#a0aec0' },
            },
            invalid: { color: '#fa755a' }
        };

        const cardNumber = elements.create('cardNumber', { style });
        cardNumber.mount('#card-number-element');

        const cardExpiry = elements.create('cardExpiry', { style });
        cardExpiry.mount('#card-expiry-element');

        const cardCvc = elements.create('cardCvc', { style });
        cardCvc.mount('#card-cvc-element');

        const form = document.getElementById('payment-form');
        const cardErrors = document.getElementById('card-errors');
        const submitButton = document.getElementById('submit-button');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            submitButton.disabled = true;

        const response = await fetch("{{ route('auto.payment.intent', ['devis_id' => $devis_id]) }}");
        const { client_secret } = await response.json();


            const { error, paymentIntent } = await stripe.confirmCardPayment(client_secret, {
                payment_method: {
                    card: cardNumber,
                    billing_details: {
                        name: form.cardholder_name.value
                    },
                }
            });

            if (error) {
                cardErrors.textContent = error.message;
                submitButton.disabled = false;
            } else if (paymentIntent.status === "succeeded") {
                await fetch("{{ route('auto.payment.store', ['devis_id' => $devis_id]) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ payment_intent_id: paymentIntent.id })
                });

                alert("✅ Paiement réussi !");
               window.location.href = "{{ route('auto.result', ['devis_id' => $devis_id]) }}";
            }
        });
    </script>
</body>
</html>
