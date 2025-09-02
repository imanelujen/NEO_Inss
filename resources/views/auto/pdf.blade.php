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
            <p><strong>Type de véhicule :</strong> {{ $data['vehicle_type'] }}</p>
            <p><strong>Marque :</strong> {{ $data['make'] }}</p>
            <p><strong>Modèle :</strong> {{ $data['model'] }}</p>
            <p><strong>Année :</strong> {{ $data['registration_date'] }}</p>
            <p><strong>Date de permis :</strong> {{ $data['date_obtention_permis'] }}</p>
            <p><strong>Bonus-Malus :</strong> {{ $data['bonus_malus'] }}</p>
            <p><strong>Historique d'accidents :</strong> {{ $data['historique_accidents'] }}</p>
            <p><strong>Montant du devis :</strong> {{ number_format($data['quote_amount'], 2) }} €</p>
            <p><strong>Formule choisie :</strong> {{ ucfirst($data['selected_offer']) }}</p>
        </div>
    </div>
</body>
</html>
