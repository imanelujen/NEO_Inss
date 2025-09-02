<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis HABITATION - Neo Assurances</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f9fafb;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .header {
            border-bottom: 3px solid #471b37ff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2563eb;
            font-size: 24px;
        }
        .header p {
            font-size: 14px;
            color: #555;
        }
        .details {
            margin-top: 20px;
        }
        .details h2 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #471b37ff;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details th, .details td {
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .details th {
            background: #f3f4f6;
            color: #111827;
            width: 40%;
        }
        .amount {
            margin-top: 30px;
            padding: 15px;
            background: #f0f9ff;
            border-left: 4px solid #2563eb;
            font-size: 18px;
            font-weight: bold;
            color: #471b37ff;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Devis HABITATION - UMBRELLA</h1>
            <p>Date : {{ now()->format('d/m/Y') }}</p>
        </div>

        <!-- Vehicle & Driver Details -->
        <div class="details">
            <h2>Détails du propriété</h2>
            <table>
                <tr>
                    <th>Type de propriété</th>
                    <td>{{ $data['housing_type'] }}</td>
                </tr>
                <tr>
                    <th>surface m²</th>
                    <td>{{ $data['surface_area'] }}</td>
                </tr>
                <tr>
                    <th>valeur</th>
                    <td>{{ $data['housing_value'] }}</td>
                </tr>
                <tr>
                    <th>Ville</th>
                    <td>{{ $data['ville'] }}</td>
                </tr>
                <tr>
                    <th>Rue</th>
                    <td>{{ $data['rue'] }}</td>
                </tr>
                <tr>
                    <th>Offre choisie</th>
                    <td>{{ ucfirst($data['selected_offer']) }}</td>
                </tr>
            </table>
        </div>

        <!-- Amount -->
        <div class="amount">
            Montant du devis : {{ number_format($data['montant_base'], 2) }} DH/AN
        </div>

        <!-- Footer -->
        <div class="footer">
            Ce devis est généré automatiquement par UMBRELLA.<br>
            Pour toute question, contactez-nous : support@UMBRELLA.ma
        </div>
    </div>
</body>
</html>
