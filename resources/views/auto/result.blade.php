<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat Devis Auto - Neo Assurances</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto p-4 max-w-2xl">
        <h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">Votre Devis Auto</h1>
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p><strong>Type de véhicule :</strong> {{ $quote->vehicle_type }}</p>
            <p><strong>Marque :</strong> {{ $quote->brand }}</p>
            <p><strong>Modèle :</strong> {{ $quote->model }}</p>
            <p><strong>Année :</strong> {{ $quote->year }}</p>
            <p><strong>Date de permis :</strong> {{ $quote->license_date }}</p>
            <p><strong>Bonus-Malus :</strong> {{ $quote->bonus_malus }}</p>
            <p><strong>Historique d'accidents :</strong> {{ $quote->accident_history }}</p>
            <p><strong>Montant du devis :</strong> {{ number_format($amount, 2) }} €</p>
            <div class="mt-6 flex justify-center space-x-4">
                <a href="{{ route('auto.subscribe', ['devis_id' => $quote->id]) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Souscrire</a>
                <a href="{{ route('auto.download', ['devis_id' => $quote->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Télécharger PDF</a>
                <form action="{{ route('auto.email', ['devis_id' => $quote->id]) }}" method="POST" class="inline">
                    @csrf
                    <input type="email" name="email" placeholder="Votre e-mail" required class="border rounded p-2 mr-2">
                    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Envoyer par e-mail</button>
                </form>
            </div>
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('quote.show', ['step' => 1]) }}" class="text-blue-600 hover:underline">Nouveau Devis</a>
        </div>
    </div>
</body>
</html>
