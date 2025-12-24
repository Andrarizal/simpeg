<x-filament-panels::page>

    <style>
        /* Container Utama */
        .hv-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow: auto;
            padding: 20px;
        }

        /* Item Wrapper (Baris per Staff) */
        .hv-item {
            display: flex;
            align-items: center; /* Vertically Center Parent relative to Children */
        }

        /* Wrapper untuk Anak-anak (Sebelah Kanan) */
        .hv-children {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* CARD STYLE */
        .hv-card {
            min-width: 150px; /* Lebar minimum kartu */
            max-width: 200px;
            padding: 12px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            text-align: center;
            position: relative;
            z-index: 2;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            transition: all 0.2s;
            margin: 10px 0 10px 30px; /* Margin Kanan untuk jarak garis */
        }

        .hv-card:hover {
            border-color: #eab308;
            transform: scale(1.02);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        /* Dark Mode Support */
        :is(.dark) .hv-card {
            background: #1f2937;
            border-color: #374151;
            color: #f3f4f6;
        }

        /* --- LOGIKA GARIS KONEKTOR (CSS MAGIC) --- */

        /* 1. Garis Horizontal Pendek (Keluar dari Kanan Parent) */
        .hv-item::after {
            content: '';
            position: absolute;
            /* Logika ini akan ditangani di child, tapi kita siapkan placeholder */
        }

        /* 2. Logika Garis pada Children Wrapper */
        .hv-children-wrapper {
            display: flex;
            align-items: center;
        }
        
        /* Garis Penghubung Horizontal (Parent ke Children Group) */
        .hv-connector {
            width: 30px; /* Panjang garis konektor */
            height: 2px;
            background-color: #ccc;
        }

        /* Garis Siku pada setiap Anak */
        .hv-child-branch {
            display: flex;
            align-items: center;
            position: relative;
        }

        /* Garis Horizontal Masuk ke Kartu Anak */
        .hv-child-branch::before {
            content: '';
            position: absolute;
            left: -30px; /* Mundur ke kiri */
            top: 50%;
            width: 30px; /* Panjang garis */
            height: 2px;
            background-color: #ccc;
            margin-left: 30px;
        }

        /* Garis Vertikal Tiang Listrik */
        .hv-child-branch::after {
            content: '';
            position: absolute;
            left: -30px;
            width: 2px;
            background-color: #ccc;
            height: 100%; /* Default full height */
            top: 0;
            margin-left: 30px;
        }

        /* FIX: Menghapus kelebihan garis vertikal pada anak PERTAMA */
        .hv-children > .hv-child-branch:first-child::after {
            top: 50%;
            height: 50%;
        }

        /* FIX: Menghapus kelebihan garis vertikal pada anak TERAKHIR */
        .hv-children > .hv-child-branch:last-child::after {
            height: 50%;
            bottom: 50%;
        }

        /* FIX: Jika anak cuma satu, tidak perlu garis vertikal, cukup horizontal */
        .hv-children > .hv-child-branch:only-child::after {
            display: none;
        }
        /* Dan perpanjang sedikit garis horizontalnya biar nyambung */
        .hv-children > .hv-child-branch:only-child::before {
            width: 60px;
            left: -60px;
            margin-top: -1px
        }

        @media print {
            /* 1. Sembunyikan Elemen Filament yang Tidak Perlu */
            .fi-sidebar,        /* Sidebar Kiri */
            .fi-topbar,         /* Navbar Atas */
            .fi-header,         /* Header Judul Halaman & Tombol */
            .fi-footer,         /* Footer jika ada */
            button,
            .floating-notif {  /* Sembunyikan container utama dulu... */
                display: none !important;
            }

            body, .fi-body, .fi-main {
                background: white !important;
                height: auto !important;
                overflow: visible !important;
            }

            /* Reset wrapper scroll kita agar tidak terpotong di kertas */
            .overflow-auto {
                overflow: visible !important;
                height: auto !important;
                border: none !important;
            }

            /* Posisikan Bagan di Pojok Kiri Atas Kertas */
            .hv-container {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                gap: 10px; /* Kurangi gap biar muat */
                zoom: 31.6%;
            }

            .hv-card {
                max-width: 360px;
            }
            
            /* Aturan Halaman Kertas */
            @page {
                size: Legal potrait; /* Paksa mode Landscape */
                margin: 0;    /* Margin kertas */
            }

            /* Paksa browser mencetak background color (kartu & garis) */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    <div 
        x-data 
        x-init="$nextTick(() => {
            const root = document.getElementById('org-root-node');
            if (root) {
                // Scroll elemen root ke tengah layar (Vertikal & Horizontal)
                root.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center', 
                    inline: 'center' 
                });
            }
        })"
        class="hv-container overflow-x-auto">
        {{-- Loop Root --}}
        @foreach($rootChairs as $root)
            <div class="flex items-center">
                @include('filament.pages.partials.org-chart-node', ['chair' => $root, 'isRoot' => true])
            </div>
        @endforeach
    </div>

</x-filament-panels::page>