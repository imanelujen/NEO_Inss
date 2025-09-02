<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Neo Assurances</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md md:max-w-4xl bg-white shadow-lg rounded-xl overflow-hidden flex flex-col md:flex-row animate-fade-in">
        
        <!-- Left image (hidden on phone, visible on tablet/desktop) -->
        <div class="hidden md:block md:w-1/2 bg-cover bg-center" 
             style="background-image: url('/images/background1.jpeg');">
        </div>

        <!-- Form section -->
        <div class="w-full md:w-1/2 p-6 sm:p-8 flex flex-col justify-center">
            
            <!-- Header -->
            <div class="form-card-header rounded-lg mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold">Connexion</h1>
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

            <!-- Form -->
            <form method="POST" action="{{ route('register.show') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-blue-900 mb-2 text-sm sm:text-base">E-mail</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                           class="w-full border rounded-lg p-2 sm:p-3 text-sm sm:text-base focus:ring-2 focus:ring-green-500">
                    @error('email') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-blue-900 mb-2 text-sm sm:text-base">Mot de passe</label>
                    <input type="password" name="password" id="password" required 
                           class="w-full border rounded-lg p-2 sm:p-3 text-sm sm:text-base focus:ring-2 focus:ring-green-500">
                    @error('password') <span class="text-red-500 text-xs sm:text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 sm:gap-0">
                    <a href="{{ route('register.show') }}" class="text-green hover:underline text-sm sm:text-base text-center sm:text-left">
                        Créer un compte
                    </a>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded text-sm sm:text-base">
                        Se connecter
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
