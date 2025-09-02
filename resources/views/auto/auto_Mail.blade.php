<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
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
        <h1>Votre Devis Auto - Neo Assurances</h1>
        <p>Merci d'avoir utilisé notre service de devis. Voici les détails de votre devis :</p>
        <div class="details">
<p><strong>Type de véhicule :</strong> {{ $devis->vehicle_type }}</p>
<p><strong>Marque :</strong> {{ $devis->make }}</p>
<p><strong>Modèle :</strong> {{ $devis->model }}</p>
<p><strong>Année :</strong> {{ $devis->registration_date }}</p>
<p><strong>Date de permis :</strong> {{ $devis->date_obtention_permis }}</p>
<p><strong>Bonus-Malus :</strong> {{ $devis->bonus_malus }}</p>
<p><strong>Montant du devis :</strong> {{ number_format($devis->quote_amount, 2) }} DH</p>

        </div>
        <p>Vous trouverez le devis en pièce jointe au format PDF.</p>
        <p>Pour souscrire, veuillez visiter <a href="{{ url('/simulate/auto/subscribe/' . $devis->id) }}">notre site</a>.</p>
    </div>
</body>
</html>
