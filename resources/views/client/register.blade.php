<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Neo Assurances</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-5xl bg-white shadow-lg rounded-xl overflow-hidden flex flex-col md:flex-row animate-fade-in">
        
        <!-- Left image (hidden on phone, visible on tablet/desktop) -->
        <div class="hidden md:block md:w-1/2 bg-cover bg-center" 
             style="background-image: url('/images/background1.jpeg');">
        </div>

        <!-- Right form -->
        <div class="w-full md:w-1/2 p-6 sm:p-8 flex flex-col justify-center">
            
            <!-- Header -->
            <div class="form-card-header rounded-lg mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold">Inscription</h1>
            </div>

            <!-- Errors -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Grid for name & prenom -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-blue-900 mb-2 text-sm sm:text-base">Nom</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full border rounded-lg p-2 sm:p-3 text-sm sm:text-base focus:ring-2 focus:ring-green-500">
                        @error('name') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="prenom" class="block text-blue-900 mb-2 text-sm sm:text-base">Prénom</label>
                        <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" required
                               class="w-full border rounded-lg p-2 sm:p-3 text-sm sm:text-base focus:ring-2 focus:ring-green-500">
                        @error('prenom') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Grid for email & phone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-blue-900 mb-2 text-sm sm:text-base">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full border rounded-lg p-2 sm:p-3 text-sm sm:text-base focus:ring-2 focus:ring-green-500">
                        @error('email') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-blue-900 mb-2 text-sm sm:text-base">Téléphone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                               class="w-full border rounded-lg p-2 sm:p-3 text-sm sm:text-base focus:ring-2 focus:ring-green-500">
                        @error('phone') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 sm:gap-0 pt-4">
                    <a href="{{ route('auto.show', ['step' => 1]) }}" 
                       class="bg-gray-500 text-white px-4 py-2 rounded text-center text-sm sm:text-base">
                        Retour
                    </a>
                    <button type="submit" 
                            class="bg-green-600 text-white px-4 py-2 rounded text-sm sm:text-base">
                        S'inscrire
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
