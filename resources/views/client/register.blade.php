<!DOCTYPE html>
  <html lang="fr">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Inscription - Neo Assurances</title>
      @vite(['resources/css/app.css'])
  </head>
  <body class="bg-gray-100 min-h-screen flex items-center justify-center">
      <div class="container mx-auto p-4 max-w-md">
          <h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">Inscription</h1>
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
          <form method="POST" action="{{ route('register') }}" class="bg-white p-6 rounded-lg shadow-md">
              @csrf
              <div class="mb-4">
                  <label class="block text-gray-700 font-medium mb-2" for="name">Nom</label>
                  <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full border rounded p-2">
                  @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
              </div>
              <div class="mb-4">
                  <label class="block text-gray-700 font-medium mb-2" for="prenom">prenom</label>
                  <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" required class="w-full border rounded p-2">
                  @error('prenom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
              </div>
              <div class="mb-4">
                  <label class="block text-gray-700 font-medium mb-2" for="email">E-mail</label>
                  <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full border rounded p-2">
                  @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
              </div>
              <div class="mb-4">
                  <label class="block text-gray-700 font-medium mb-2" for="password">Mot de passe</label>
                  <input type="password" name="password" id="password" required class="w-full border rounded p-2">
                  @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
              </div>
              <div class="mb-4">
                  <label class="block text-gray-700 font-medium mb-2" for="password_confirmation">Confirmer le mot de passe</label>
                  <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full border rounded p-2">
              </div>
              <div class="mb-4">
                  <label class="block text-gray-700 font-medium mb-2" for="phone">Téléphone (optionnel)</label>
                  <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full border rounded p-2">
                  @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
              </div>
              <div class="mb-4">
                  <label class="block text-gray-700 font-medium mb-2" for="address">Adresse (optionnel)</label>
                  <input type="text" name="address" id="address" value="{{ old('address') }}" class="w-full border rounded p-2">
                  @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
              </div>
              <div class="flex justify-between">
                  <a href="{{ route('login.show') }}" class="text-blue-600 hover:underline">Déjà un compte ?</a>
                  <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">S'inscrire</button>
              </div>
          </form>
      </div>
  </body>
  </html>