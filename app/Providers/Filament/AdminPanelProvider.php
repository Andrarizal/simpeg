<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Livewire\Livewire;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            // ->font('SF Pro', provider: GoogleFontProvider::class)
            ->sidebarCollapsibleOnDesktop()
            ->login(Login::class)
            ->userMenuItems([
                Action::make('profile')
                    ->label(fn() => Auth::user()?->name ?? 'Profil Saya')
                    ->icon('heroicon-o-user')
                    ->url(fn() => route('filament.admin.resources.profiles.index')),
            ])
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
            
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render(<<<'HTML'
                        <style>
                        .no-arrow select,
                        .no-arrow .fi-input,
                        .no-arrow .fi-ta-col-select-input {
                            
                            background-image: none !important; 
                            background-position: 0 0 !important;
                            background-size: 0 0 !important;
                            
                            appearance: none !important;
                            -webkit-appearance: none !important;
                            -moz-appearance: none !important;
                            
                            padding-top: 4px !important;
                            padding-bottom: 4px !important;
                            padding-left: 0 !important;
                            padding-right: 0 !important;
                            
                            text-align: center !important;
                            text-align-last: center !important; /* Khusus Chrome */
                            
                            min-width: 40px !important;
                            width: 100% !important;
                        }
                        
                        /* Hilangkan border focus ring jika ingin terlihat bersih total */
                        .no-arrow select:focus {
                            ring: 0 !important;
                            border-color: transparent !important;
                        }
                        
                        .fi-ta-cell:has(.sticky-col-name) {
                            position: sticky !important;
                            left: 0 !important;
                            /* Z-index rendah agar tidak menutupi header/dropdown */
                            z-index: 10 !important; 
                            
                            background-color: white; 
                        }
                        .dark .fi-ta-cell:has(.sticky-col-name) {
                            background-color: rgb(24 24 27);
                        }

                        /* --- 2. CONFIG STICKY HEADER (Judul) --- */
                        .fi-ta-header-cell:has(.sticky-col-name) {
                            position: sticky !important;
                            left: 0 !important;
                            /* Z-index sedang, di atas data tapi di bawah dropdown */
                            z-index: 20 !important; 
                            background-color: inherit;
                        }

                        /* --- 3. SOLUSI ANDA: BOOST FILTER Z-INDEX --- */
                        /* Kita paksa Panel Dropdown (Filter/Action) punya level tertinggi */
                        .fi-dropdown-panel {
                            z-index: 100 !important; /* Angka ini harus > 20 */
                        }
                        
                        /* Opsional: Jika Header tabel sticky juga menutupi, naikkan header tabel container */
                        .fi-ta-header-ctn {
                            z-index: 30 !important;
                        }
                    </style>
                    <script>
                        window.GlobalGPS = {
                            lat: null,
                            lng: null,
                            acc: null,
                            timestamp: 0,
                            isReady: false,
                            watchId: null,
                            
                            start() {
                                if (this.watchId) return;
                                if (!navigator.geolocation) return;

                                console.log('🌍 Global GPS Engine Started...');

                                this.watchId = navigator.geolocation.watchPosition(
                                    (pos) => {
                                        this.lat = pos.coords.latitude;
                                        this.lng = pos.coords.longitude;
                                        this.acc = pos.coords.accuracy;
                                        this.timestamp = Date.now();
                                        this.isReady = true;

                                        // Broadcast Event ke AlpineJS (agar halaman presensi bisa dengar)
                                        window.dispatchEvent(new CustomEvent('gps-update', { 
                                            detail: { 
                                                lat: this.lat, 
                                                lng: this.lng, 
                                                acc: this.acc,
                                                time: this.timestamp
                                            } 
                                        }));
                                    },
                                    (err) => console.error('Global GPS Error:', err),
                                    {
                                        enableHighAccuracy: true, 
                                        maximumAge: 0,
                                        timeout: 20000
                                    }
                                );
                            }
                        };

                        navigator.permissions.query({name:'geolocation'}).then(result => {
                            if (result.state == 'granted') {
                                window.GlobalGPS.start();
                            }
                        });

                        document.addEventListener('DOMContentLoaded', async () => {
                            // --- 1. VARIABEL GLOBAL UNTUK MENYIMPAN STATE ---
                            // Kita simpan jumlah notifikasi terakhir di sini agar tidak hilang saat HTML di-refresh Livewire
                            let previousNotificationCount = 0; 
                            let isFirstLoad = true;

                            if ('serviceWorker' in navigator) {
                                try {
                                    await navigator.serviceWorker.register('/sw.js');
                                    console.log('Service Worker Registered');
                                } catch (error) {
                                    console.error('SW Register Failed:', error);
                                }
                            }
                            
                            // --- 2. REQUEST PERMISSION ---
                            if (Notification.permission !== "granted" && Notification.permission !== "denied") {
                                Notification.requestPermission();
                            }

                            const showSystemNotification = async () => {
                                if (Notification.permission !== "granted") return;

                                try {
                                    const response = await fetch('/latest-notification');
                                    const data = await response.json();

                                    if (data.status == 'found') {
                                        if ('serviceWorker' in navigator) {
                                            const registration = await navigator.serviceWorker.ready;
                                            
                                            // Tampilkan via Service Worker
                                            await registration.showNotification(data.title, {
                                                body: data.body,
                                                icon: "/img/rsumpyk.png", // Ganti path logo
                                                vibrate: [200, 100, 200, 100, 200],
                                                tag: "simantap-" + Date.now(),
                                                // Renotify: Jika tag kebetulan sama, paksa bunyi lagi
                                                renotify: true,
                                                data: { url: data.url ?? window.location.href } // Simpan URL untuk di-klik
                                            });
                                        } else {
                                            const notif = new Notification(data.title, {
                                                body: data.body,
                                                icon: "/img/rsumpyk.png", // Pastikan path ini benar
                                                tag: "simantap-" + Date.now() 
                                            });

                                            notif.onclick = () => { 
                                                window.focus(); 
                                                if(data.url) window.location.href = data.url; // Redirect jika ada link
                                                notif.close(); 
                                            };
                                        }
                                    } else {
                                        console.error('data not found');
                                    }
                                } catch (error) {
                                    console.error('Gagal mengambil detail notifikasi:', error);
                                }
                            };

                            const checkNotifications = () => {
                                // Selector spesifik sesuai HTML yang Anda kirim
                                // Kita cari span di dalam div class 'fi-icon-btn-badge-ctn'
                                const badgeSpan = document.querySelector('.total-notifications');

                                if (badgeSpan) {
                                    // Ambil angka dari teks (misal "1", "5", "99+")
                                    // Gunakan regex untuk mengambil hanya angka (jika ada karakter lain)
                                    const text = badgeSpan.innerText.trim();
                                    const currentCount = parseInt(text.replace(/[^0-9]/g, '')) || 0;

                                    // LOGIKA DETEKSI KENAIKAN
                                    // Jika ini bukan load pertama DAN angka sekarang LEBIH BESAR dari sebelumnya
                                    if (!isFirstLoad && currentCount > previousNotificationCount) {
                                        console.log('Notifikasi baru! Mengambil detail...');
                                        showSystemNotification();
                                    }

                                    // Update state global
                                    previousNotificationCount = currentCount;
                                    isFirstLoad = false;
                                } else {
                                    // Jika badge hilang (berarti 0 notifikasi)
                                    previousNotificationCount = 0;
                                }
                            };

                            // --- 3. JALANKAN MUTATION OBSERVER ---
                            // Karena Livewire mengubah DOM, kita pantau terus menerus
                            const observer = new MutationObserver((mutations) => {
                                // Setiap ada perubahan di DOM, kita cek ulang angkanya
                                checkNotifications();
                            });

                            const targetNode = document.querySelector('body');
                            if (targetNode) {
                                observer.observe(targetNode, { 
                                    childList: true, 
                                    subtree: true, 
                                    characterData: true 
                                });
                            }
                            
                            // Cek sekali di awal (jika sudah ada notif saat login)
                            checkNotifications();
                        });
                    </script>
                HTML)
            )
            ->favicon(asset('img/rsumpyk.svg'))
            ->brandLogo(fn () => view('filament.schemas.components.brand-logo'))
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s');
    }

    public function boot(): void
    {
        Filament::serving(function () {
            if (!Auth::check()) return;

            if (Livewire::isLivewireRequest()) return;
            if (session()->has('doc_expiry_notified')) return;

            $user = Auth::user();

            if ($user->staff && $user->staff->administration) {
                
                $adminRecord = $user->staff->administration;
                
                $documentsToCheck = [
                    'sip_expiry' => 'Surat Izin Praktek (SIP)',
                    'str_expiry' => 'Surat Tanda Registrasi (STR)',
                    'mcu_expiry' => 'Medical Check Up (MCU)',
                    'spk_expiry' => 'Surat Penugasan Klinis (SPK)',
                    'rkk_expiry' => 'Rincian Kewenangan Klinis (RKK)',
                    'utw_expiry' => 'Uraian Tugas Wewenang (UTW)',
                ];

                foreach ($documentsToCheck as $field => $label) {
                    $expiryValue = $adminRecord->$field;

                    if ($expiryValue) {
                        $expiryDate = Carbon::parse($expiryValue);
                        $today = Carbon::now()->startOfDay();
                        $warningDate = Carbon::now()->addDays(7)->endOfDay(); 

                        // Logika: H-7 atau Sudah Lewat
                        if ($expiryDate->lessThanOrEqualTo($warningDate)) {
                            $daysLeft = $today->diffInDays($expiryDate, false);
                            
                            if ($daysLeft < 0) {
                                $title = '⛔ Dokumen Kadaluarsa';
                                $body = "Dokumen {$label} telah beberapa hari kadaluarsa.";
                                $color = 'danger';
                            } elseif ($daysLeft == 0) {
                                $title = '⚠️ Dokumen Kadaluarsa Hari Ini';
                                $body = "Dokumen {$label} Anda kadaluarsa HARI INI!";
                                $color = 'warning';
                            } else {
                                $title = '⚠️ Peringatan Dokumen';
                                $body = "Dokumen {$label} Anda akan kadaluarsa dalam {$daysLeft} hari lagi (" . $expiryDate->format('d M Y') . ")";
                                $color = 'warning';
                            }
                            
                            $secretMarker = '<span class="lock-notif hidden"></span>';

                            Notification::make()
                                ->title(new HtmlString($secretMarker . $title))
                                ->body($body)
                                ->status($color)
                                ->persistent()
                                ->actions([
                                    Action::make('perbarui')
                                        ->button()
                                        ->label('Perbarui')
                                        ->url(StaffAdministrationResource::getUrl('edit', ['record' => $adminRecord->id])),
                                    Action::make('dismiss')
                                        ->label('Perbarui Nanti')
                                        ->color('gray')
                                        ->dispatch('mark-expiry-read')
                                        ->close()
                                ])
                                ->send();
                        }
                    }
                }
            }
        });
    }
}
