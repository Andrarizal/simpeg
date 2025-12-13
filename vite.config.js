import os from "os";
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

async function getPublicIp() {
    try {
        const res = await fetch("https://api.ipify.org?format=json");
        const { ip } = await res.json();
        console.log("🌍 Public IP:", ip);
    } catch (e) {
        console.log("Gagal ambil IP publik:", e.message);
    }
}

getPublicIp(); // hanya log, tidak dipakai di konfigurasi

function getLocalIp() {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        for (const iface of interfaces[name]) {
            if (iface.family == "IPv4" && !iface.internal) {
                return iface.address;
            }
        }
    }
    return "localhost";
}

const localIp = getLocalIp();

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
    input: ["resources/css/filament/admin/theme.css"],
    server: {
        host: true, // atau '0.0.0.0'
        port: 5173, // sesuaikan kalau mau port lain
        strictPort: false,
        hmr: {
            host: localIp,
            protocol: "ws",
            clientPort: 5173,
        },
    },
});
