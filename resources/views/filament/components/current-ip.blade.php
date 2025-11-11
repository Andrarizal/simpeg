<div
    x-data="{ ipClient: null, ipServer: null, deviceInfo: null }"
    x-init="
        async () => {
            try {
                // Ambil IP publik (via API eksternal)
                const res = await fetch('https://api.ipify.org?format=json');
                const { ip } = await res.json();

                // Fungsi untuk dapatkan fingerprint canvas
                function getCanvasFingerprint() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    ctx.textBaseline = 'top';
                    ctx.font = '16px Arial';
                    ctx.fillText('SIMPEG-FINGERPRINT', 2, 2);
                    return canvas.toDataURL();
                }

                // Sumber fingerprint
                const fingerprintSource = [
                    navigator.userAgent,
                    navigator.platform,
                    screen.width + 'x' + screen.height,
                    window.devicePixelRatio,
                    navigator.language,
                    Intl.DateTimeFormat().resolvedOptions().timeZone,
                    navigator.hardwareConcurrency,
                    navigator.deviceMemory || 'unknown',
                    getCanvasFingerprint()
                ].join('|');

                // Fungsi hash dengan fallback
                async function hashString(str) {
                    if (window.crypto && crypto.subtle) {
                        const encoder = new TextEncoder();
                        const data = encoder.encode(str);
                        const hashBuffer = await crypto.subtle.digest('SHA-256', data);
                        const hashArray = Array.from(new Uint8Array(hashBuffer));
                        return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
                    } else {
                        // fallback manual
                        let hash = 0;
                        for (let i = 0; i < str.length; i++) {
                            const chr = str.charCodeAt(i);
                            hash = (hash << 5) - hash + chr;
                            hash |= 0;
                        }
                        return hash.toString(16);
                    }
                }

                // Buat fingerprint (pendekkan jadi 16 karakter)
                const fullHash = await hashString(fingerprintSource);
                const fingerprint = fullHash.slice(0, 16);

                // Kirim ke backend
                const result = await fetch('/report-ip', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ip,
                        device_id: fingerprint,
                        device_info: navigator.userAgent,
                        platform: navigator.platform
                    })
                });

                const json = await result.json();
                ipClient = json.client_ip;
                ipServer = json.server_ip;
                deviceInfo = fingerprint;
            } catch (err) {
                console.error('Gagal mendapatkan data perangkat:', err);
            }
        }"
    class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 text-sm flex flex-col gap-1"
>
    <div>
        <strong>IP Publik (Client):</strong>
        <span x-text="ipClient ?? 'Mendeteksi...'"></span>
    </div>
    <div>
        <strong>IP Server Terlihat:</strong>
        <span x-text="ipServer ?? 'Mendeteksi...'"></span>
    </div>
    <div>
        <strong>Device Fingerprint:</strong>
        <span x-text="deviceInfo ?? 'Mendeteksi...'"></span>
    </div>
</div>
