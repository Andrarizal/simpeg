<div class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-xl shadow-[0px_10px_30px_-10px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-200 dark:border-gray-800 overflow-hidden ring-1 ring-gray-950/5 dark:ring-white/10">
        
        <div class="bg-emerald-50 dark:bg-emerald-500/10 px-6 py-8 flex flex-col items-center justify-center text-center border-b border-emerald-100 dark:border-emerald-500/20">
            <div class="h-16 w-16 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mb-4 ring-4 ring-white dark:ring-white/5">
                <x-heroicon-s-check-badge class="w-8 h-8" />
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Dokumen Valid & Terverifikasi</h2>
            <p class="text-sm text-emerald-700 dark:text-emerald-400 mt-1 font-medium">Mempunyai Tanda Tangan Elektronik</p>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-gray-500 dark:text-gray-400">
                        <x-heroicon-o-user class="w-5 h-5" />
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Penandatangan</span>
                </div>
                <div class="text-gray-950 dark:text-white font-semibold text-right">{{ $signer }}</div>
            </div>

            <div class="px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-gray-500 dark:text-gray-400">
                        <x-heroicon-o-briefcase class="w-5 h-5" />
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Jabatan</span>
                </div>
                <div class="text-gray-950 dark:text-white font-semibold text-right">{{ $chair }}</div>
            </div>

            <div class="px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-gray-500 dark:text-gray-400">
                         <x-heroicon-o-clock class="w-5 h-5" />
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Waktu Verifikasi</span>
                </div>
                <div class="text-gray-950 dark:text-white font-semibold text-right">{{ $date }}</div>
            </div>

        </div>

        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            <div class="flex items-start gap-3">
                 <x-heroicon-s-shield-check class="w-5 h-5 text-gray-400 dark:text-gray-500 mt-0.5" />
                 
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    Sertifikat Elektronik Dijamin oleh: <span class="font-medium text-gray-700 dark:text-gray-300">SK Direktur ...</span><br>
                    Tentang Penerbitan Tanda Tangan Elektronik
                </p>
            </div>
        </div>
    </div>