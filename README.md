# SIMANTAP (Sistem Manajemen dan Presensi Terpadu) RSU Mitra Paramedika

SIMANTAP adalah Sistem Informasi Manajemen SDM komprehensif yang dibangun menggunakan **Laravel** dan **Filament**. Sistem ini mendigitalisasi seluruh alur kerja kepegawaian, mulai dari presensi berbasis geolokasi, manajemen penjadwalan, persetujuan cuti berjenjang, hingga sistem penilaian evaluasi yang kompleks.

## Fitur Utama (Key Features)

Aplikasi ini dibagi menjadi beberapa modul utama yang dirancang menggunakan *Resources* dan *Pages* pada Filament:

*   **Manajemen Kehadiran & Presensi (Attendance Management):**
    *   Mendukung presensi menggunakan jaringan kantor atau geolokasi (GPS).
    *   Dilengkapi dengan validasi radius lokasi (kurang dari 200 meter dari titik presensi) dan sistem pendeteksi *Fake GPS*.
*   **Manajemen Jadwal (Scheduling & Shift):**
    *   Kepala Unit dan Admin SDM dapat mengelola *shift* dan membuat jadwal kerja secara otomatis (*generate*) maupun manual.
    *   Pegawai dapat mengajukan pertukaran jadwal (Tukar Jadwal) yang membutuhkan persetujuan dari Kepala Unit atau Koordinator.
*   **Manajemen Cuti & Izin (Leave & Permission Management):**
    *   Fasilitas pengajuan cuti dan izin oleh pegawai dengan melampirkan detail tanggal, alasan, dan penunjukan rekan pengganti (jika diperlukan)[cite: 1].
    *   Sistem *approval* (persetujuan) hierarkis yang mengalir berdasarkan struktur organisasi, mulai dari konfirmasi rekan pengganti, persetujuan atasan/Kepala Unit, hingga verifikasi akhir oleh Admin SDM[cite: 1].
*   **Penilaian & Evaluasi Pegawai Berjenjang (Employee Appraisal):**
    *   Sistem penilaian kinerja yang mengalir secara hierarkis mulai dari Staff, Kepala Unit, Koordinator, Kepala Seksi, hingga Direktur.
    *   Modul Evaluasi Kontrak yang mengalkulasi nilai akhir berdasarkan perbandingan KKM dan kelengkapan nilai hingga Asesor Tingkat 4.
*   **Manajemen Surat & Tugas (Letter & Task Management):**
    *   Pengelolaan alur Surat Keluar (Disposisi/Undangan dan Surat Tugas) lengkap dengan *template* surat dan distribusi ke staf terpilih.
    *   Karyawan dapat mengunggah laporan Notulensi, materi, dan foto *selfie* dari Surat Tugas untuk diverifikasi oleh tim SDM.
    *   Fitur penugasan *On-Call* dari atasan yang terintegrasi dengan sistem notifikasi.
*   **Pengembangan SDM (Employee Development):**
    *   Pegawai dapat mengunggah sertifikat dan riwayat pelatihan untuk diverifikasi oleh bagian Diklat.
*   **Keamanan Akses (Authentication):**
    *   Alur registrasi ketat yang mewajibkan verifikasi *email* dan pra-registrasi yang diverifikasi langsung (NIP, Jabatan, Unit Kerja) oleh Admin/SDM sebelum akun aktif.

## Teknologi yang Digunakan (Tech Stack)

Sistem ini dikembangkan di atas arsitektur **TALL Stack**:
*   **Framework:** Laravel 12
*   **Admin Panel / UI:** Filament v4
*   **Front-end:** Tailwind CSS, Alpine.js, Livewire
*   **Database:** MySQL

