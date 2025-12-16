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

    .fi-section,
    .fi-card,
    .fi-input-wrp, 
    .fi-select-input,
    .fi-fo-file-upload-input-ctn,
    .fi-btn,
    .fi-icon-btn {
        border-radius: var(--radius-xl) !important;
    }

    .fi-btn {
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)
    }

    .fi-section {
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)
    }
    </style>
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
    <div class="h-fit lg:h-[calc(100vh-90px)] mb-16 lg:my-5 md:my-10 md:mx-24 w-6/7 lg:w-auto flex gap-6">
      {{-- Kiri: gambar full --}}
      <div class="w-2/5 hidden lg:block">
        <img src="{{ asset('img/rsu.jpg') }}" class="w-full h-full object-cover rounded-3xl shadow-2xl">
      </div>
      
      {{-- Kanan: form login --}}
      <div class="w-full h-full lg:w-3/5 py-10 lg:py-0 flex items-center justify-center border border-gray-50/20 bg-gray-50/5 dark:bg-gray-800/50 rounded-4xl overflow-hidden shadow-2xl">
      {{ $slot }}
      </div>
    </div>
  </div>

  @livewire('notifications')
  @filamentScripts
  @livewireScripts
</body>
</html>
