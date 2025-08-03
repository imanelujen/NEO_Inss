<nav class="bg-white shadow-md p-4">
  <div class="max-w-7xl mx-auto flex justify-between items-center">
    <!-- Logo -->
    <div class="text-2xl font-bold text-blue-600">MonSite</div>

    <!-- Menu (mobile hidden) -->
    <div class="hidden md:flex space-x-6">
      <a href="#" class="text-gray-700 hover:text-blue-600">Accueil</a>
      <a href="#" class="text-gray-700 hover:text-blue-600">À propos</a>
      <a href="#" class="text-gray-700 hover:text-blue-600">Services</a>
      <a href="#" class="text-gray-700 hover:text-blue-600">Contact</a>
    </div>

    <!-- Burger menu (mobile) -->
    <div class="md:hidden">
      <button id="menu-btn" class="text-gray-700 focus:outline-none">
        <!-- Icone burger -->
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>
  </div>

  <!-- Menu mobile -->
  <div id="mobile-menu" class="md:hidden hidden px-4 pt-2 pb-4 space-y-2">
    <a href="#" class="block text-gray-700 hover:text-blue-600">Accueil</a>
    <a href="#" class="block text-gray-700 hover:text-blue-600">À propos</a>
    <a href="#" class="block text-gray-700 hover:text-blue-600">Services</a>
    <a href="#" class="block text-gray-700 hover:text-blue-600">Contact</a>
  </div>
</nav>

<script>
  // Script pour ouvrir/fermer le menu mobile
  document.getElementById("menu-btn").addEventListener("click", () => {
    const menu = document.getElementById("mobile-menu");
    menu.classList.toggle("hidden");
  });
</script>
