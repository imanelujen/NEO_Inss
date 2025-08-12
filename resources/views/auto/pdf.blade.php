<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis Auto - Neo Assurances</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { padding: 20px; }
        h1 { color: #2563eb; }
        .details { margin-top: 20px; }
        .details p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Devis Auto - Neo Assurances</h1>
        <div class="details">
            <p><strong>Type de véhicule :</strong> {{ $quote->vehicle_type }}</p>
            <p><strong>Marque :</strong> {{ $quote->brand }}</p>
            <p><strong>Modèle :</strong> {{ $quote->model }}</p>
            <p><strong>Année :</strong> {{ $quote->year }}</p>
            <p><strong>Date de permis :</strong> {{ $quote->license_date }}</p>
            <p><strong>Bonus-Malus :</strong> {{ $quote->bonus_malus }}</p>
            <p><strong>Historique d'accidents :</strong> {{ $quote->accident_history }}</p>
            <p><strong>Montant du devis :</strong> {{ number_format($quote->quote_amount, 2) }} €</p>
        </div>
    </div>
</body>
</html>
