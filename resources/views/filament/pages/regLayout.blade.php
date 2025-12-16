<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>

    @filamentStyles
    @livewireStyles
    @vite(["resources/css/filament/admin/theme.css"])
    <style>
      @font-face {
          font-family: 'SF-Pro';
          src: url('/fonts/SF-Pro.otf');
      }

      :root {
          --font-family: 'SF-Pro', sans-serif !important;
      }

      body {
        background-color: #f8fafc; 

        background-image: 
            radial-gradient(at 0% 0%, #afe1af 0px, transparent 50%),
            radial-gradient(at 100% 0%, #f3e8ff 0px, transparent 50%),
            radial-gradient(at 100% 100%, #d0ffa3 0px, transparent 50%),
            radial-gradient(at 0% 50%, #eff6ff 0px, transparent 50%);

        background-attachment: fixed;
        background-size: cover;
    }

    .dark body {
        background-color: #1c3b29; 
        background-image: 
            radial-gradient(at 0% 0%, #0f172a 0px, transparent 50%), 
            radial-gradient(at 100% 100%, #293300 0px, transparent 50%);
    }

    .fi-card,

    .fi-input-wrp, 
    .fi-select-input,
    .fi-fo-file-upload-input-ctn,

    .fi-btn,
    .fi-icon-btn,
    .fi-tabs-item {
        border-radius: var(--radius-xl) !important;
    }

    .fi-section,
    .fi-sc-tabs,
    .fi-ta-ctn, {
        border-radius: var(--radius-3xl)!important;
    }

    .fi-btn {
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)
    }

    .fi-section, .fi-section.fi-section-not-contained .fi-wi-stats-overview-stat, .fi-sidebar-item.fi-active {
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)
    }
    </style>
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
      class="absolute top-6 right-6 md:top-4 md:right-4 p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition shadow-lg">
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
    <div class="h-full my-8 p-0 lg:mt-0 lg:p-10 w-6/7 lg:w-full border border-transparent bg-transparent rounded-4xl">
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
