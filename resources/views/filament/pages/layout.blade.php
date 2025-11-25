<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>

    @filamentStyles
    @livewireStyles
    @vite(["resources/css/filament/admin/theme.css"])
</head>

<body class="lg:h-screen box-border lg:flex lg:items-center lg:justify-center md:p-10 bg-gray-100 dark:bg-gray-900">
  <div 
    x-data="{
        dark: localStorage.getItem('theme') === 'dark',
        toggleTheme() {
            this.dark = !this.dark;
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', this.dark);
        }
    }"
    x-init="document.documentElement.classList.toggle('dark', dark)" class="lg:h-screen mt-20 md:mt-0 overflow-hidden box-border flex items-center justify-center w-full">
    <button 
      @click="toggleTheme"
      class="absolute top-6 right-6 md:top-4 md:right-4 p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
      <!-- Light Mode Icon -->
      <x-heroicon-o-sun 
          x-show="!dark" 
          class="w-6 h-6 text-yellow-500"
      />

      <!-- Dark Mode Icon -->
      <x-heroicon-o-moon 
          x-show="dark" 
          class="w-6 h-6 text-gray-100"
      />
    </button>
    <div class="h-fit lg:h-[calc(100vh-80px)] mb-16 py-10 lg:py-0 lg:my-5 md:my-10 md:mx-20 w-6/7 lg:w-auto flex border border-transparent bg-gray-50 dark:bg-gray-800 rounded-4xl overflow-hidden shadow-2xl">
      {{-- Kiri: gambar full --}}
      <div class="w-2/5 hidden lg:block p-2">
        <img src="{{ asset('img/rsu.jpg') }}" class="w-full h-full object-cover rounded-3xl shadow-xl">
      </div>
      
      {{-- Kanan: form login --}}
      <div class="w-full h-full lg:w-3/5 flex items-center justify-center">
      {{ $slot }}
      </div>
    </div>
  </div>

  @livewire('notifications')
  @filamentScripts
  @livewireScripts
</body>
</html>
