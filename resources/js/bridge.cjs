const net = require("net");
const puppeteer = require("puppeteer");

// --- KONFIGURASI ---
const PORT = 5555;

// Ganti dengan Domain lokal project Laravel Anda (Pastikan tanpa slash di akhir)
const TARGET_URL = "http://simpeg.test";

(async () => {
    console.log('🚀 Membuka Browser (Mode Continuous Stream)...');
    
    const browser = await puppeteer.launch({
        headless: false, 
        defaultViewport: null,
        args: [
            '--start-maximized',
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-infobars',
            `--unsafely-treat-insecure-origin-as-secure=${TARGET_URL}`
        ]
    });
    
    const pages = await browser.pages();
    const page = pages[0];

    const client = await page.target().createCDPSession();

    // Set lokasi awal (Default Jakarta) biar map gak blank
    await client.send('Emulation.setGeolocationOverride', {
        latitude: -6.200000, 
        longitude: 106.816666,
        accuracy: 100
    });

    console.log(`➡️  Menuju: ${TARGET_URL}`);
    await page.goto(TARGET_URL);
    
    try {
        await client.send('Browser.grantPermissions', {
            origin: TARGET_URL,
            permissions: ['geolocation']
        });
    } catch(e) {}

    console.log(`✅ Siap! Menunggu Stream GPS dari HP...`);

    const server = net.createServer((socket) => {
        let buffer = ''; // Penampung potongan data
        let updateCount = 0; // Hitungan Heartbeat

        socket.on('data', async (data) => {
            // 1. Gabungkan potongan data baru ke buffer
            buffer += data.toString();

            // 2. Cek apakah ada karakter Enter (\n) yang menandakan akhir baris
            let d_index = buffer.indexOf('\n');

            // 3. Selama ada baris lengkap, proses terus!
            while (d_index > -1) {
                const line = buffer.substring(0, d_index); // Ambil satu baris
                buffer = buffer.substring(d_index + 1);    // Sisanya simpan lagi
                
                // Proses NMEA GPGGA
                if (line.includes('$GPGGA')) {
                    const parts = line.split(',');
                    const rawLat = parts[2];
                    const latDir = parts[3];
                    const rawLon = parts[4];
                    const lonDir = parts[5];

                    if(rawLat && rawLon) {
                        const latDecimal = convertNMEA(rawLat, latDir);
                        const lonDecimal = convertNMEA(rawLon, lonDir);

                        try {
                            // INJECT KE CHROME
                            await client.send('Emulation.setGeolocationOverride', {
                                latitude: latDecimal,
                                longitude: lonDecimal,
                                accuracy: 10
                            });
                            
                            updateCount++;
                            // Gunakan process.stdout agar baris ter-update (animasi)
                            process.stdout.write(`\r📡 Heartbeat #${updateCount} | 📍 Posisi: ${latDecimal.toFixed(6)}, ${lonDecimal.toFixed(6)}   `);
                        } catch (e) {
                            // Silent error
                        }
                    }
                }

                // Cari baris berikutnya di buffer
                d_index = buffer.indexOf('\n');
            }
        });

        socket.on('error', (err) => {
            console.log('\n❌ Error Socket:', err.message);
        });
    });

    server.listen(PORT, '0.0.0.0');

})();

function convertNMEA(raw, dir) {
    if(!raw) return 0;
    let dot = raw.indexOf('.');
    if(dot === -1) return 0;
    let deg = parseFloat(raw.substring(0, dot - 2));
    let min = parseFloat(raw.substring(dot - 2));
    let decimal = deg + (min / 60);
    if (dir === 'S' || dir === 'W') { decimal = decimal * -1; }
    return decimal;
}
