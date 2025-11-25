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

<body class="box-border lg:flex lg:items-center lg:justify-center md:p-10 bg-gray-100 dark:bg-gray-900">
  <div 
    x-data="{
        dark: localStorage.getItem('theme') === 'dark',
        toggleTheme() {
            this.dark = !this.dark;
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', this.dark);
        }
    }"
    x-init="document.documentElement.classList.toggle('dark', dark)" class="mt-20 md:mt-0 overflow-hidden box-border flex flex-col items-center justify-center w-full">
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
    <div class="flex justify-center lg:justify-start items-center gap-4">
      <img src="{{ asset('img/rsumpyk.png') }}" class="w-14 h-14">
      <h1 class="font-semibold leading-tight lg:text-xl hidden lg:block">Sistem Informasi Manajemen dan Tenaga Pegawai</h1>
      <h1 class="font-semibold leading-tight text-2xl lg:hidden">SIMANTAP</h1>
    </div>
    <div class="h-full my-8 p-0 lg:p-10 w-6/7 lg:w-full border border-transparent bg-transparent lg:bg-gray-50 lg:dark:bg-gray-800 rounded-4xl lg:shadow-2xl">
      {{-- Kanan: form login --}}
      <div class="w-full h-full flex items-center justify-center">
      {{ $slot }}
      </div>
    </div>
  </div>

  @livewire('notifications')
  @filamentScripts
  @livewireScripts
</body>
</html>
