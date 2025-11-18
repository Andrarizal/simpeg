@php
$presence = \App\Models\Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', now()->toDateString())->whereNull('check_out')->count() > 0;
@endphp
<div 
x-data="{
    lat: null,
    lng: null,
    radius: Infinity,
    canCheckIn: false,
    loading: false,
    mapLoaded: false,
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

    async submitCheckIn() {
        if (!this.canCheckIn) return;

        this.loading = true;

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
            loading = true;

            let bestPosition = null;
            let attempts = 0;

            const watcher = navigator.geolocation.watchPosition(
                async (pos) => {
                    attempts++;

                    // Ambil posisi terbaik (akurasinya kecil = akurat)
                    if (
                        !bestPosition ||
                        pos.coords.accuracy < bestPosition.coords.accuracy
                    ) {
                        bestPosition = pos;
                    }

                    // Jika akurasi sudah sangat bagus (< 40 m) ATAU sudah 3 kali percobaan
                    if (bestPosition.coords.accuracy < 40 || attempts >= 3) {

                        navigator.geolocation.clearWatch(watcher);
                        loading = false;

                        // Kirim radius check ke server
                        const loc = await fetch('/check-radius', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                lat: bestPosition.coords.latitude,
                                lng: bestPosition.coords.longitude,
                            })
                        });

                        const json = await loc.json();
                        // Update variabel Alpine
                        radius = json.radius;
                        canCheckIn = (json.radius <= 100);
                        lat = json.user_lat;
                        lng = json.user_lng;

                        return;
                    }
                },
                (err) => {
                    console.error('GPS Error:', err);
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 0,
                    timeout: 15000
                }
            );
        } catch (err) {
            console.error('Gagal mendapatkan data perangkat:', err);
        }
}
" class="relative space-y-4">

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

    <div class="flex items-center justify-between">
        {{-- Radius Info --}}
        <div class="rounded-lg text-sm">
            <div>
                <strong>Latitude:</strong>
                <span x-text="lat ?? 'Mendeteksi...'"></span>
            </div>
            <div>
                <strong>Longitude:</strong>
                <span x-text="lng ?? 'Mendeteksi...'"></span>
            </div>
            <div>
                <strong>Radius:</strong>
                <span x-text="radius != Infinity ? radius.toFixed(2) + ' meter' : 'Mendeteksi...'"></span>
            </div>
        </div>

        {{-- Tombol Check In --}}
        <div class="text-right">
            <button
                x-bind:disabled="!canCheckIn"
                @click="submitCheckIn"
                class="px-4 py-2 rounded-lg text-white flex items-center gap-2"
                :class="canCheckIn ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-gray-400 cursor-not-allowed'">
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
