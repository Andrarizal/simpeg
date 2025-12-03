@php
$presence = \App\Models\Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', now()->toDateString())->whereNull('check_out')->count() > 0;
$officeLat = setting('lat') ?? 0; 
$officeLng = setting('lng') ?? 0;
$radius = 200;
@endphp

<div 
x-data="{
    lat: null,
    lng: null,
    radius: Infinity,
    gpsAccuracy: 0,

    canCheckIn: false,
    loading: false,
    mapLoaded: false,

    isFakeDetected: false,
    fakeMessage: '',
    lastUpdateTime: 0,   // Waktu terakhir data masuk (Heartbeat)
    lastMoveTime: 0,     // Waktu terakhir koordinat berubah (Jitter)
    lastLat: 0,
    lastLng: 0,
    checkInterval: null,
    isLocked: false,

    officeLat: {{ $officeLat }},
    officeLng: {{ $officeLng }},
    maxRadius: {{ $radius }},

    mode: '{{ $presence ? "check-out" : "check-in" }}',
    getModalId() {
        const modal = document.querySelector(`[id^='fi-'][id$='-action-0']`);
        return modal.id;
    },

    getMap() {
        if (!this.lat || !this.lng) return '';
        this.mapLoaded = false;
        return `https://www.google.com/maps?q=${this.lat},${this.lng}&hl=es;z=17&output=embed`;
    },

    // Helper Hitung Jarak (Haversine Simpel) untuk deteksi Jitter
    getDist(lat1, lon1, lat2, lon2) {
        {{-- if (lat1 == 0) return 100;  --}}
        if (!lat1 || !lat2) return Infinity; 
        var p = 0.017453292519943295;    
        var c = Math.cos;
        var a = 0.5 - c((lat2 - lat1) * p)/2 + 
                c(lat1 * p) * c(lat2 * p) * (1 - c((lon2 - lon1) * p))/2;
        return 12742 * Math.asin(Math.sqrt(a)) * 1000; // Meter
    },

    // (Dipanggil setiap ada event gps-update)
    processGPS(data) {
        // Jangan proses jika sudah terdeteksi fake
        if (this.isFakeDetected || this.isLocked) return;

        const now = Date.now();
        this.lastUpdateTime = now; // Reset Heartbeat (Napas GPS)

        // --- A. CEK TELEPORTASI (Lompatan Jauh) ---
        const distMove = this.getDist(this.lastLat, this.lastLng, data.lat, data.lng);
        
        if (this.lastLat !== 0 && distMove > 1000) { 
             this.vonisFake('Teleportasi lokasi terdeteksi (>1km instan).');
             return;
        }

        // --- B. CEK JITTER (Anti-Diam) ---
        // Jika bergerak > 0.5m dan akurasi bagus, reset timer diam
        if (distMove > 0.5 && data.acc < 50) {
            this.lastMoveTime = now;
            this.lastLat = data.lat;
            this.lastLng = data.lng;
        }

        // --- C. UPDATE UI ---
        this.lat = data.lat;
        this.lng = data.lng;
        this.gpsAccuracy = data.acc;
        
        // Hitung Radius Client-Side (Agar tombol responsif)
        this.radius = this.getDist(data.lat, data.lng, this.officeLat, this.officeLng);
        
        this.canCheckIn = (this.radius <= this.maxRadius) && !this.isFakeDetected;
        this.loading = false;

        if (this.canCheckIn) {
            console.log('🎯 Posisi Valid & Radius Masuk. Mengunci GPS...');
            
            this.isLocked = true; 
            
            if (this.checkInterval) {
                clearInterval(this.checkInterval);
                this.checkInterval = null;
            }
            
            this.statusText = 'Posisi Terkunci (Siap Presensi)';
        }
    },

    vonisFake(msg) {
        this.isFakeDetected = true;
        this.fakeMessage = msg;
        this.canCheckIn = false;
        clearInterval(this.checkInterval);
        console.error('⛔ FAKE GPS DETECTED:', msg);
    },

    async submitCheckIn() {
        if (!this.canCheckIn || this.isFakeDetected) return;

        this.loading = true;
        console.log('🚀 Memulai proses Submit Check-in...');

        try {
            // Contoh: kirim data ke backend
            const response = await fetch('/check-in-by-gps', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    lat: this.lat,
                    lng: this.lng,
                    radius: this.radius,
                    mode: this.mode
                })
            });

            const json = await response.json();
            console.log('✅ Response Server:', json);

            if (json.status === 'ok') {
                $wire.dispatch('close-modal', { id:this.getModalId() });
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan sistem.');
        }
        this.loading = false;
    }
}
" x-init="
    async () => {
        try {
            console.clear();
            console.log('%c🏁 SYSTEM START: Inisialisasi GPS...', 'background:blue; color:white; padding:4px;');

            loading = true;

            isFakeDetected = false;
            
            // Reset Timer
            lastUpdateTime = 0;
            lastMoveTime = Date.now();

            const handler = (e) => processGPS(e.detail);
            window.addEventListener('gps-update', handler);

            // Jika Global GPS sebenarnya sudah nyala dari tadi (misal dari Dashboard)
            // Kita ambil data terakhirnya SEKARANG JUGA biar gak nunggu update berikutnya
            if (window.GlobalGPS && window.GlobalGPS.isReady) {
                console.log('⚡ Mengambil data Global GPS yang sudah ready...');
                processGPS({
                    lat: window.GlobalGPS.lat,
                    lng: window.GlobalGPS.lng,
                    acc: window.GlobalGPS.acc
                });
            } else {
                // Jika belum nyala, pancing start (opsional, jaga-jaga provider belum init)
                if (window.GlobalGPS) window.GlobalGPS.start();
            }

            // --- (HEARTBEAT MONITOR) ---
            if (checkInterval) clearInterval(checkInterval);
            
            let now = Date.now();
            
            checkInterval = setInterval(() => {
                // Jangan cek jika belum dapat data pertama (Cold Start)
                if (lastUpdateTime === 0) return;

                now = Date.now();
                const diff = now - lastUpdateTime;
                
                // Adaptive Timeout: 
                // Jika Akurasi Bagus (<20m), batas diam 20 detik.
                // Jika Akurasi Buruk (>20m), batas diam 120 detik (Network location memang lambat).
                let timeoutLimit = (gpsAccuracy < 20) ? 20000 : 60000;

                console.log(`Akurasi Terakhir: ${gpsAccuracy}m`);

                // Cek 1: Heartbeat Mati (Tidak ada update sama sekali)
                if (diff > timeoutLimit && gpsAccuracy < 50) {
                    // Hanya vonis jika akurasi bagus (karena GPS asli harusnya cepat)
                    vonisFake('Lokasi Statis Tidak Wajar (Matikan Fake GPS).');
                }

                // Cek 2: Jitter Mati (Akurasi Tinggi TAPI Koordinat Tidak Geser > 0.5m)
                // Ini menangkap Fake GPS yang 'mengirim data' tapi koordinatnya di-lock.
                if (gpsAccuracy < 20 && (now - lastMoveTime > 30000)) {
                    console.error('%c⚠️ ALARM: LOKASI STATIS TIDAK WAJAR!', 'font-size:16px; color:red;');
                    isFakeDetected = true;
                    fakeMessage = 'Lokasi Statis Tidak Wajar. Matikan Fake GPS.';
                    loading = false;
                    navigator.geolocation.clearWatch(watcher);
                    clearInterval(checkInterval);
                }
            }, 2000); // Cek setiap 2 detik
        } catch (err) {
            console.error('Gagal mendapatkan data perangkat:', err);
        }
}
" class="relative space-y-4">

    <div x-show="isFakeDetected" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline" x-text="fakeMessage"></span>
    </div>

    {{-- Map --}}
    <div 
        x-show="!mapLoaded"
        x-transition.opacity
        class="absolute inset-0 z-10 flex items-center justify-center bg-gray-100 overflow-hidden rounded-xl h-60"
    >
        <!-- Skeleton Shimmer -->
        <div class="absolute inset-0 bg-linear-to-r from-gray-200 via-gray-300 to-gray-200 animate-shimmer"></div>

        <!-- Spinner -->
        <div class="relative z-20">
            <div class="h-10 w-10 border-4 border-gray-300 border-t-blue-600 rounded-full animate-spin"></div>
        </div>
    </div>

    <iframe
        x-show="!isFakeDetected && lat"
        x-bind:src="getMap()"
        @load="mapLoaded = true"
        width="100%"
        height="240"
        class="rounded-xl border"
        allowfullscreen=""
        loading="lazy">
    </iframe>

    <style>
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .animate-shimmer {
        animation: shimmer 2.2s infinite linear;
    }
    </style>

    <div class="flex flex-col md:flex-row items-center justify-between">
        {{-- Radius Info --}}
        <div class="text-sm mb-4 md:mb-0 w-full md:w-auto">
            <div class="text-left">
                <strong>Latitude:</strong>
                <span x-text="lat ?? 'Mendeteksi...'"></span>
            </div>
            <div class="text-left">
                <strong>Longitude:</strong>
                <span x-text="lng ?? 'Mendeteksi...'"></span>
            </div>
            <div class="text-left">
                <strong>Radius:</strong>
                <span x-text="radius != Infinity ? radius.toFixed(2) + ' meter' : 'Mendeteksi...'"></span>
            </div>
        </div>

        {{-- Tombol Check In --}}
        <div class="text-right w-full md:w-auto">
            <button
                x-bind:disabled="!canCheckIn || isFakeDetected"
                @click="submitCheckIn"
                class="px-4 py-2 rounded-lg text-white flex items-center justify-center gap-2 w-full md:w-auto"
                :class="(canCheckIn && !isFakeDetected) ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-gray-400 cursor-not-allowed'">
                <x-heroicon-o-finger-print class="w-5 h-5" />
                @if ($presence)
                    Check-Out
                @else
                    Check-In
                @endif
            </button>
        </div>
    </div>

</div>
