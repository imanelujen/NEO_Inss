<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>checkout habitation</title>
</head>
<body>
  <h1 class="text-2xl font-bold text-blue-600 mb-6 text-center">
    Finaliser votre contrat - Rendez-vous
</h1>

<form method="POST" action="{{ route('habit.appointment.store', ['devis_id' => $devis_id]) }}" 
      class="bg-white p-6 rounded-lg shadow-md">
    @csrf

    <div class="mb-4">
        <label for="appointment_date" class="block text-gray-700 font-bold mb-2">Date du rendez-vous :</label>
        <input type="date" name="appointment_date" id="appointment_date" 
               class="border rounded w-full p-2" required>
    </div>

    <div class="mb-4">
        <label for="appointment_time" class="block text-gray-700 font-bold mb-2">Heure du rendez-vous :</label>
        <input type="time" name="appointment_time" id="appointment_time" 
               class="border rounded w-full p-2" required>
    </div>

    <button type="submit" 
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Confirmer le rendez-vous
    </button>
</form>
  
</body>
</html>