<style>
    /* 1. Posisi Wrapper (Responsif) */
    .badge-notif-wrapper {
        position: absolute;
        top: 35px;
        right: 75px;
        pointer-events: none;
    }

    @media (min-width: 768px) {
        .badge-notif-wrapper {
            top: 24px;
            right: 18px;
        }
    }

    /* 2. Desain Bola Merah (Angka) */
    .badge-angka {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 18px; 
        height: 18px;
        background-color: #dc2626; /* Warna bg-red-600 */
        color: #ffffff;
        font-size: 10px;
        font-weight: bold;
        border-radius: 50%;
        border: 2px solid #ffffff; /* Garis tepi putih */
        position: absolute;
        z-index: 10;
    }

    /* Khusus untuk Dark Mode Filament agar garis tepinya menyesuaikan */
    .dark .badge-angka {
        border-color: #111827; /* Warna dark:ring-gray-900 */
    }

    /* 3. Animasi Ping (Berkedip) */
    .badge-ping {
        display: block;
        width: 18px;
        height: 18px;
        background-color: #f87171; /* Warna bg-red-400 */
        border-radius: 50%;
        position: absolute;
        z-index: 5;
        animation: ping-anim 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    @keyframes ping-anim {
        0% { transform: scale(1); opacity: 0.75; }
        75%, 100% { transform: scale(2.5); opacity: 0; }
    }
</style>

<div class="relative">
    {{-- Badge Custom Milikmu --}}
    <div class="floating-notif fixed right-8 top-4 z-10 hidden md:flex items-center justify-center bg-white border border-gray-200 shadow-xl rounded-full dark:bg-gray-900 dark:border-white/10" style="width: 40px; height: 40px;">

        {{-- Modal Lonceng Bawaan Filament --}}
        <div class="[&_.fi-icon-btn-badge]:hidden flex items-center justify-center">
            @livewire(\Filament\Livewire\DatabaseNotifications::class)
        </div>

        @livewire(\App\Livewire\NotificationBadge::class)
    </div>
</div>