<!DOCTYPE html>
  <html lang="fr">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Connexion - Neo Assurances</title>
      @vite(['resources/css/app.css'])
  </head>
  <body class="bg-gray-100 min-h-screen flex items-center justify-center">
      <div class="container mx-auto p-4 max-w-md">
          <h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">Connexion</h1>
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
          <form method="POST" action="{{ route('register.show') }}" class="bg-white p-6 rounded-lg shadow-md">
              @csrf
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
              <div class="flex justify-between">
                  <a href="{{ route('register.show') }}" class="text-blue-600 hover:underline">Créer un compte</a>
                  <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Se connecter</button>
              </div>
          </form>
      </div>
  </body>
  </html>
  