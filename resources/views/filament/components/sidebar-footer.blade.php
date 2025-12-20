@php
    $user = auth()->user();
@endphp

<div 
    x-data="{
        isFooterOpen: JSON.parse(localStorage.getItem('isFooterOpen') ?? 'true'),
        
        toggleFooter() {
            this.isFooterOpen = !this.isFooterOpen;
            localStorage.setItem('isFooterOpen', this.isFooterOpen);
        }
    }"
    class="space-y-2 border-t border-gray-200 dark:border-white/10"
    {{-- Mengatur alignment: Kalo terbuka 'start', kalo tertutup 'center' --}}
    :class="[
    $store.sidebar.isOpen ? 'text-start' : 'flex flex-col items-center text-center', 
    !isFooterOpen ? 'pb-0' : 'pb-4'
]"
>

    <button 
        @click="toggleFooter()"
        type="button"
        class="flex items-center justify-center w-full py-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-white/5 dark:hover:text-gray-300 transition-colors cursor-pointer"
        :title="isFooterOpen ? 'Sembunyikan Menu User' : 'Tampilkan Menu User'"
    >
        <x-heroicon-m-chevron-down 
            class="w-4 h-4 transition-transform duration-300"
            x-show="isFooterOpen"
        />

        <x-heroicon-m-ellipsis-horizontal 
            class="w-5 h-5 transition-transform duration-300"
            x-show="!isFooterOpen"
        />
    </button>
    
    <div 
        x-show="isFooterOpen" 
        x-collapse 
        class="pb-0 space-y-3"
        {{-- Styling layout saat sidebar utama collapse/expand --}}
        :class="$store.sidebar.isOpen ? 'text-start' : 'flex flex-col items-center text-center'"
    >

      <div 
          x-data="{
              theme: localStorage.getItem('theme') || 'system',
              
              init() {
                  this.applyTheme(this.theme);
                  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                      if (this.theme === 'system') this.applyTheme('system');
                  });
              },

              setTheme(val) {
                  this.theme = val;
                  localStorage.setItem('theme', val);
                  this.applyTheme(val);
                  window.dispatchEvent(new CustomEvent('theme-changed', { detail: val }));
              },

              cycleTheme() {
                  if (this.theme === 'light') this.setTheme('dark');
                  else if (this.theme === 'dark') this.setTheme('system');
                  else this.setTheme('light');
              },

              applyTheme(val) {
                  const isDark = val === 'dark' || (val === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                  document.documentElement.classList.toggle('dark', isDark);
              }
          }"
          class="flex justify-center px-2 transition-all duration-300"
      >
          <div x-show="$store.sidebar.isOpen" x-transition class="flex justify-self-center gap-1 px-2 py-1.5 w-fit bg-gray-100 rounded-xl dark:bg-white/5">
              <button @click="setTheme('light')" :class="theme === 'light' ? 'bg-white text-primary-600 shadow-sm dark:bg-gray-800 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400'" class="flex items-center justify-center px-2 py-1.5 rounded-lg transition-all gap-1">
                  <x-heroicon-m-sun class="w-5 h-5" />
                  <span class="text-sm">Terang</span>
              </button>
              <button @click="setTheme('dark')" :class="theme === 'dark' ? 'bg-white text-primary-600 shadow-sm dark:bg-gray-800 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400'" class="flex items-center justify-center px-2 py-1.5 rounded-lg transition-all gap-1">
                  <x-heroicon-m-moon class="w-5 h-5" />
                  <span class="text-sm">Gelap</span>
              </button>
              <button @click="setTheme('system')" :class="theme === 'system' ? 'bg-white text-primary-600 shadow-sm dark:bg-gray-800 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400'" class="flex items-center justify-center px-2 py-1.5 rounded-lg transition-all gap-1">
                  <x-heroicon-m-computer-desktop class="w-5 h-5" />
                  <span class="text-sm">Sistem</span>
              </button>
          </div>

          <div x-show="!$store.sidebar.isOpen" x-transition>
              <button 
                  @click="cycleTheme()" 
                  class="flex items-center justify-center w-10 h-10 mx-auto text-gray-500 transition bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-white/5 dark:text-gray-400 dark:hover:bg-white/10"
                  x-tooltip="'Ganti Tema'"
              >
                  <x-heroicon-m-sun x-show="theme === 'light'" class="w-5 h-5" />
                  <x-heroicon-m-moon x-show="theme === 'dark'" class="w-5 h-5" />
                  <x-heroicon-m-computer-desktop x-show="theme === 'system'" class="w-5 h-5" />
              </button>
          </div>
      </div>

      <a 
          href="{{ \App\Filament\Resources\Profiles\ProfileResource::getUrl('index') }}" 
          class="flex items-center gap-3 mx-4 px-2 py-2 rounded-xl cursor-pointer dark:hover:bg-white/5 hover:bg-gray-100 dark:hover:text-white transition-all duration-300"
          :class="!$store.sidebar.isOpen && 'justify-center px-0'" 
      >
          <div class="shrink-0">
              <x-filament-panels::avatar.user size="sm" :user="$user" />
          </div>
          
          <div 
              x-show="$store.sidebar.isOpen" 
              x-transition:enter="transition ease-out duration-200 delay-100"
              x-transition:enter-start="opacity-0 translate-x-[-10px]"
              x-transition:enter-end="opacity-100 translate-x-0"
              class="flex flex-col overflow-hidden text-left"
          >
              <span class="text-sm font-bold truncate text-gray-950 dark:text-white">
                  {{ $user->name }}
              </span>
              <span class="text-xs text-gray-500 truncate dark:text-gray-400">
                  {{ $user->staff->chair->name }}
              </span>
          </div>
      </a>

      <div class="px-4 transition-all duration-300" :class="!$store.sidebar.isOpen && 'px-0'">
          <form action="{{ filament()->getLogoutUrl() }}" method="post">
              @csrf
              <button 
                  type="submit" 
                  class="flex items-center gap-3 py-2 text-sm font-medium text-gray-500 transition rounded-xl hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white group"
                  :class="$store.sidebar.isOpen ? 'w-full px-2' : 'justify-center w-10 h-10 mx-auto'"
                  x-tooltip="!$store.sidebar.isOpen ? { 
                      content: 'Keluar', 
                      placement: 'right' 
                  } : ''"
              >
                  <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 group-hover:text-primary-600 shrink-0"/>
                  
                  <span 
                      x-show="$store.sidebar.isOpen" 
                      x-transition 
                      class="truncate"
                  >
                      Keluar
                  </span>
              </button>
          </form>
      </div>
    </div>
</div>