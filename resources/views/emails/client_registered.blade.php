<!DOCTYPE html>
<html>
<head>
    <title>Bienvenue chez Neo Assurances</title>
</head>
<body>
    <h1>Bienvenue, {{ $name }} {{ $prenom }} !</h1>
    <p>Votre compte a été créé avec succès. Voici vos identifiants :</p>
    <p><strong>Email :</strong> {{ $email }}</p>
    <p><strong>Mot de passe :</strong> {{ $password }}</p>
    <p>Veuillez vous connecter à <a href="{{ url('/login') }}">Neo Assurances</a> pour continuer.</p>
    <p>Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe après votre première connexion.</p>
    <p>Merci de choisir Neo Assurances !</p>
</body>
</html>