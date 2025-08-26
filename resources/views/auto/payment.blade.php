<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - Neo Assurances</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto p-4 max-w-4xl">
        <h1 class="text-2xl font-bold text-blue-600 mb-6 text-center">Finaliser votre paiement</h1>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Détails du contrat</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-gray-700 font-medium">Date de début du contrat :</p>
                    <p class="text-gray-600">{{ $paymentDetails['start_date'] }}</p>
                </div>
                <div>
                    <p class="text-gray-700 font-medium">Montant à payer (après réduction CRM de 100) :</p>
                    <p class="text-gray-600">{{ number_format($paymentDetails['amount'], 2) }} MAD</p>
                </div>
                <div>
                    <p class="text-gray-700 font-medium">Garanties incluses :</p>
                    <ul class="list-disc list-inside text-gray-600">
                        @foreach ($paymentDetails['garanties'] as $garantie)
                            <li>{{ $garantie }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
     <form id="payment-form" method="POST" action="{{ route('auto.payment.store', ['devis_id' => $devis_id]) }}">
            @csrf
            <div id="card-element" class="mb-4 p-2 border border-gray-300 rounded-md"></div>
            <div id="card-errors" role="alert" class="text-red-600 text-sm mb-4"></div>
            <button type="submit" id="submit-button" class="w-full bg-indigo-600 text-white p-3 rounded-md hover:bg-indigo-700 transition">
                Payer en ligne
            </button>
        </form>
        <script>
            const stripe = Stripe('{{ env('STRIPE_PUBLISHABLE_KEY') }}');
            const elements = stripe.elements();
            const cardElement = elements.create('card');
            cardElement.mount('#card-element');

            const form = document.getElementById('payment-form');
            const cardErrors = document.getElementById('card-errors');
            const submitButton = document.getElementById('submit-button');

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                submitButton.disabled = true;

                const { paymentMethod, error } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                });

                if (error) {
                    cardErrors.textContent = error.message;
                    submitButton.disabled = false;
                } else {
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = 'payment_method';
                    tokenInput.value = paymentMethod.id;
                    form.appendChild(tokenInput);
                    form.submit();
                }
            });
        </script>
    </div>
</body>
</html>