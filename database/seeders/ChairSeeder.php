<?php

namespace Database\Seeders;

use App\Models\Chair;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChairSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chairs = [
            [
                'name' => 'Direktur',
                'level' => 1
            ],
            [
                'name' => 'Kepala Seksi Pelayanan & Keperawatan',
                'level' => 2
            ],
            [
                'name' => 'Kepala Seksi Mutu & Data Informasi',
                'level' => 2
            ],
            [
                'name' => 'Kepala Seksi Pelayanan & Sarana Penunjang',
                'level' => 2
            ],
            [
                'name' => 'Kepala Sub Bagian Tata Usaha',
                'level' => 2
            ],
            [
                'name' => 'Koordinator Pengembangan Mutu',
                'level' => 3
            ],
            [
                'name' => 'Koordinator Keperawatan',
                'level' => 3
            ],
            [
                'name' => 'Koordinator Pelayanan Penunjang',
                'level' => 3
            ],
            [
                'name' => 'Koordinator RM & Casemix',
                'level' => 3
            ],
            [
                'name' => 'Koordinator Pelayanan Medis',
                'level' => 3
            ],
            [
                'name' => 'Koordinator Keuangan & Akuntansi',
                'level' => 3
            ],
            [
                'name' => 'Koordinator Sarana Pelayanan',
                'level' => 3
            ],
            [
                'name' => 'Koordinator Umum & Kepegawaian',
                'level' => 3
            ],
            [
                'name' => 'Koordiantor Perencanaan, Evaluasi & Pelaporan',
                'level' => 3
            ],
            [
                'name' => 'Koordinator Data dan Informasi',
                'level' => 3
            ],
            [
                'name' => 'Perawat',
                'level' => 4
            ],
            [
                'name' => 'Training Perawat',
                'level' => 4
            ],
            [
                'name' => 'Bidan',
                'level' => 4
            ],
            [
                'name' => 'Dokter Umum',
                'level' => 4
            ],
            [
                'name' => 'Training Dokter Umum',
                'level' => 4
            ],
            [
                'name' => 'Dokter Gigi',
                'level' => 4
            ],
            [
                'name' => 'Farmasi',
                'level' => 4
            ],
            [
                'name' => 'Administrasi Farmasi',
                'level' => 4
            ],
            [
                'name' => 'Apoteker',
                'level' => 4
            ],
            [
                'name' => 'Laboratorium',
                'level' => 4
            ],
            [
                'name' => 'Training Laboratorium',
                'level' => 4
            ],
            [
                'name' => 'Radiologi',
                'level' => 4
            ],
            [
                'name' => 'Pendaftaran',
                'level' => 4
            ],
            [
                'name' => 'Rekam Medis',
                'level' => 4
            ],
            [
                'name' => 'Casemix',
                'level' => 4
            ],
            [
                'name' => 'Keuangan',
                'level' => 4
            ],
            [
                'name' => 'Kassa',
                'level' => 4
            ],
            [
                'name' => 'Sekretariat',
                'level' => 4
            ],
            [
                'name' => 'Humas Marketing',
                'level' => 4
            ],
            [
                'name' => 'Diklat',
                'level' => 4
            ],
            [
                'name' => 'SDM',
                'level' => 4
            ],
            [
                'name' => 'Logistik',
                'level' => 4
            ],
            [
                'name' => 'Sanitasi',
                'level' => 4
            ],
            [
                'name' => 'Elektromedis',
                'level' => 4
            ],
            [
                'name' => 'Fisioterapi',
                'level' => 4
            ],
            [
                'name' => 'Gizi',
                'level' => 4
            ],
            [
                'name' => 'Ahli Gizi',
                'level' => 4
            ],
            [
                'name' => 'Training Gizi',
                'level' => 4
            ],
            [
                'name' => 'CSSD',
                'level' => 4
            ],
            [
                'name' => 'Satpam',
                'level' => 4
            ],
            [
                'name' => 'Umum',
                'level' => 4
            ],
            [
                'name' => 'SIRS',
                'level' => 4
            ],
            [
                'name' => 'Laundry',
                'level' => 4
            ],
            [
                'name' => 'Driver UGD',
                'level' => 4
            ],
            [
                'name' => 'Training Driver',
                'level' => 4
            ],
            [
                'name' => 'Teknisi',
                'level' => 4
            ],
        ];

        foreach ($chairs as $chair){
            Chair::create($chair);
        }
    }
}
