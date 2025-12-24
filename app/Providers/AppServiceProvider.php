<?php

namespace App\Providers;

use App\Livewire\FloatingNotification;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentView::registerRenderHook(
        'panels::head.start',
            fn (): string => Blade::render(<<<'HTML'
                <style>
                    @font-face {
                        font-family: 'SF-Pro';
                        src: url('/fonts/SF-Pro.ttf');
                    }

                    :root {
                        --font-family: 'SF-Pro', sans-serif !important;
                        --sidebar-width: 18rem!important;
                    }

                    .fi-card,

                    .fi-input-wrp, 
                    .fi-select-input,
                    .fi-fo-file-upload-input-ctn,

                    .fi-btn,
                    .fi-icon-btn,

                    .fi-modal-window,
                    .fi-dropdown-panel,

                    .fi-sidebar-item-btn,
                    .fi-sidebar-footer .hover\:bg-gray-100, /* Hover effect footer */

                    .fi-no-notification,
                    .fi-tabs-item,

                    .fi-badge {
                        border-radius: var(--radius-xl) !important;
                    }

                    .fi-section,
                    .fi-sc-tabs,
                    .fi-ta-ctn,
                    .fi-modal-window,
                    .fi-wi-stats-overview-stat {
                        border-radius: var(--radius-3xl)!important;
                    }

                    .fi-wi-stats-overview-stat {
                        overflow: hidden;
                    }

                    .fi-btn {
                        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)
                    }

                    .fi-ta-ctn, .fi-tabs, .fi-section, .fi-section.fi-section-not-contained .fi-wi-stats-overview-stat, .fi-sidebar-item.fi-active {
                        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)
                    }

                    .fi-section-not-contained, .fi-tabs.fi-contained {
                        box-shadow: none
                    }
                </style>
            HTML)
        );

        FilamentView::registerRenderHook(
        'panels::head.end',
            function (): string {
                $currentRoute = Route::currentRouteName();
                $isDashboard = Str::endsWith($currentRoute, '.pages.dashboard');

                $css = <<<CSS
                    .fi-global-search-ctn {
                        display: none !important;
                    }

                    .fi-page-content > *:first-child:not(:last-child) {
                        position: absolute; 
                        
                        width: 0;
                        height: 0;
                        opacity: 0;
                        overflow: hidden;
                        
                        pointer-events: none; 
                        z-index: -1;
                    }

                    .fi-main {
                        margin-inline: 0 !important;
                        max-width: 100% !important;
                        min-height: 100vh;
                        background-color: #f8fafc; 

                        background-image: 
                            radial-gradient(at 0% 0%, #afe1af 0px, transparent 50%),
                            radial-gradient(at 100% 0%, #f3e8ff 0px, transparent 50%),
                            radial-gradient(at 100% 100%, #d0ffa3 0px, transparent 50%),
                            radial-gradient(at 0% 50%, #eff6ff 0px, transparent 50%);

                        background-attachment: fixed;
                        background-size: cover;
                    }

                    .dark .fi-main {
                        background-color: #1c3b29; 
                        background-image: 
                            radial-gradient(at 0% 0%, #0f172a 0px, transparent 50%), 
                            radial-gradient(at 100% 100%, #293300 0px, transparent 50%);
                    }

                    .fi-sidebar-header {
                        display: none !important;
                    }

                    .fi-sidebar-nav .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
                        background-image: none !important;
                        background-color: var(--primary-600) !important;
                    }

                    .fi-sidebar-nav .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-icon, 
                    .fi-sidebar-nav .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-label {
                        color: #ffffff !important; /* Paksa Icon Putih */
                        opacity: 1 !important;
                    }

                    .fi-sidebar-nav .fi-sidebar-item.fi-active > .fi-sidebar-item-btn:hover {
                        filter: brightness(90%); 
                    }

                    .fi-in-entry-content-col {
                        row-gap: 0
                    }

                    .fi-in-text-affixed {
                        align-items: center;
                    }

                    .fi-topbar-database-notifications-btn .fi-icon-btn-badge-ctn {
                        display: none !important;
                    }

                    .fi-no-notification:has(.lock-notif) button[x-on\:click="close"] {
                        display: none !important;
                    }

                    .fi-section-content:has(div[wire\:poll\.5s="updateChartData"]) canvas {
                        width: 100% !important;
                    }

                    @media (min-width: 1024px) {
                        aside.fi-sidebar,
                        main.fi-main,
                        header.fi-topbar,
                        .fi-sidebar-header,
                        .fi-sidebar-footer {
                            transition-property: width, min-width, margin-inline-start, margin-left !important;
                            transition-duration: 500ms !important;
                            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1) !important;
                            will-change: width, min-width, margin-inline-start;
                        }

                        aside.fi-sidebar {
                            width: var(--collapsed-sidebar-width) !important;
                            min-width: var(--collapsed-sidebar-width) !important;
                        }

                        
                        body:has(.fi-main-ctn-sidebar-open) aside.fi-sidebar,
                        .fi-layout:has(.fi-main-ctn-sidebar-open) aside.fi-sidebar,
                        .fi-main-ctn-sidebar-open aside.fi-sidebar {
                            width: var(--sidebar-width) !important;
                            min-width: var(--sidebar-width) !important;
                        }

                        @container style(--sidebar-width: var(--collapsed-sidebar-width)) {
                            .fi-sidebar-item-label { display: none; }
                        }
                        
                        aside.fi-sidebar:not([style*="20rem"]) .fi-sidebar-item-label {
                            transition: opacity 100ms ease !important; 
                        }
                        
                        body:not(:has(.fi-main-ctn-sidebar-open)) .fi-sidebar-item-label {
                            opacity: 0 !important;
                            pointer-events: none !important;
                            white-space: nowrap !important;
                            width: 0 !important;
                        }

                        .fi-topbar {
                            display: none !important;
                        }
                        
                        .fi-main {
                            padding-top: .5rem !important;
                        }

                        .fi-sidebar {
                            top: 0 !important;     
                            height: 100vh !important; 
                            border-right: 1px solid rgb(229 231 235);
                            z-index: 30 !important;
                        }
                        .dark .fi-sidebar {
                            border-right: 1px solid rgba(255, 255, 255, 0.1);
                        }

                        .fi-sidebar-nav {
                            container-type: inline-size;
                            container-name: sidebar-nav; 
                            scrollbar-width: none;  /* Firefox */
                            -ms-overflow-style: none;  /* IE and Edge */
                        }
                        
                        .fi-sidebar-nav::-webkit-scrollbar {
                            display: none;
                        }

                        .fi-ta-header-cell {
                            top: 0 !important;
                        }

                        .fi-sidebar-item {
                            justify-content: center!important;
                        }

                        .fi-sidebar-item.fi-active {
                            display: list-item!important;
                        }

                        .fi-sidebar-item-btn {
                            width: 100%!important;
                            justify-content: flex-start !important;
                            padding-inline: 0.55rem !important;
                        }

                        @container sidebar-nav (max-width: 10rem) {
                            .fi-sidebar-item-btn {
                                width: fit-content!important;
                                justify-content: center;
                                justify-self: center !important;
                            }
                        }

                        .fi-sidebar-item-btn span {
                            margin-left: 0.5rem;
                        }

                        .fi-page-filament-pages-dashboard .fi-main,
                        .fi-page-dashboard .fi-main {
                            padding-top: 2rem !important; /* Beri jarak 32px dari atas */
                        }
                        
                        .fi-page-dashboard .fi-header {
                            margin-bottom: 1.5rem;
                        }
                    }
                CSS;

                if ($isDashboard) {
                    $css .= <<<CSS
                        @media (min-width: 1024px) {
                            .fi-main {
                                padding-top: 2rem !important; 
                            }
                            
                            .fi-header {
                                margin-bottom: 0 !important;
                            }
                        }
                    CSS;
                }

                return Blade::render("<style>$css</style>");
            }
        );

        FilamentView::registerRenderHook(
            'panels::sidebar.nav.start',
            fn () => view('filament.components.sidebar-header'),
        );
        
        FilamentView::registerRenderHook(
            'panels::sidebar.footer',
            fn () => view('filament.components.sidebar-footer')
        );

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn () => \Livewire\Livewire::mount(FloatingNotification::class)
        );
    }
}
