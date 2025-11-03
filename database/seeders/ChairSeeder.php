<?php

namespace Database\Seeders;

use App\Models\Chair;
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
                'name' => 'Koor HD', 
                'level' => 4
            ],
            [
                'name' => 'Perawat HD',
                'level' => 4
            ],
            [
                'name' => 'Koor OK',
                'level' => 4
            ],
            [
                'name' => 'Perawat OK',
                'level' => 4
            ],
            [
                'name' => 'Perawat Anestesi',
                'level' => 4
            ],
            [
                'name' => 'Koor Poliklinik',
                'level' => 4
            ],
            [
                'name' => 'Perawat Poliklinik', 'level' => 4
            ],
            [
                'name' => 'Koor Bima',
                'level' => 4
            ],
            [
                'name' => 'Perawat Bima', 'level' => 4
            ],
            [
                'name' => 'Koor Rama', 'level' => 4
            ],
            [
                'name' => 'Perawat Rama', 'level' => 4
            ],
            [
                'name' => 'Koor ICU', 'level' => 4
            ],
            [
                'name' => 'Perawat ICU', 'level' => 4
            ],
            [
                'name' => 'Koor Shinta', 'level' => 4
            ],
            [
                'name' => 'Perawat Shinta', 'level' => 4
            ],
            [
                'name' => 'Koor UGD', 'level' => 4
            ],
            [
                'name' => 'Perawat UGD', 'level' => 4
            ],
            [
                'name' => 'Koor Bidan', 'level' => 4
            ],
            [
                'name' => 'Pelaksana Bidan', 'level' => 4
            ],
            [
                'name' => 'Koor Farmasi', 'level' => 4
            ],
            [
                'name' => 'Apoteker', 'level' => 4
            ],
            [
                'name' => 'Tenaga Teknis Farmasi', 'level' => 4
            ],
            [
                'name' => 'Administrasi Farmasi', 'level' => 4
            ],
            [
                'name' => 'Koor Laboratorium', 'level' => 4
            ],
            [
                'name' => 'Pelasana Laboratorium', 'level' => 4
            ],
            [
                'name' => 'Koor Radiologi', 
                'level' => 4
            ],
            [
                'name' => 'Radiografer', 
                'level' => 4
            ],
            [
                'name' => 'Koor Pendaftaran', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana Pendaftaran', 
                'level' => 4
            ],
            [
                'name' => 'Koor RM', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana Rekam Medis', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana Casemix', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana Keuangan', 
                'level' => 4
            ],
            [
                'name' => 'Koor Kassa', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana Kassa', 
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
                'name' => 'Staf SDM', 
                'level' => 4
            ],
            [
                'name' => 'Staf Diklat', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana Logistik', 
                'level' => 4
            ],
            [
                'name' => 'Sanitarian', 
                'level' => 4],
           
                [
                'name' => 'Fisioterapis', 
                'level' => 4
            ],
            [
                'name' => 'Elektromedis', 
                'level' => 4
            ],
            [
                'name' => 'Kepala Unit Elektromedis & Teknisi', 
                'level' => 4
            ],
            [
                'name' => 'Kepala Unit Sanitasi & CSSD', 
                'level' => 4
            ],
            [
                'name' => 'Koor Gizi', 
                'level' => 4
            ],
            [
                'name' => 'Ahli Gizi', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana Gizi', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana CSSD', 
                'level' => 4
            ],
            [
                'name' => 'Koor Satpam-Umum', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana Satpam', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana Umum', 
                'level' => 4
            ],
            [
                'name' => 'Analis Sistem', 
                'level' => 4
            ],
            [
                'name' => 'Analis Hardware', 
                'level' => 4
            ],
            [
                'name' => 'Programer', 
                'level' => 4
            ],
            [
                'name' => 'UX Designer', 
                'level' => 4
            ],
            [
                'name' => 'Koor Laundry', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana Laundry', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana Driver', 
                'level' => 4
            ],
            [
                'name' => 'Pelaksana Teknisi', 
                'level' => 4
            ],
            [
                'name' => 'Koor Dokter', 
                'level' => 4
            ],
            [
                'name' => 'Dokter Umum', 
                'level' => 4
            ],
            [
                'name' => 'Dokter Gigi', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Anestesi', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Radiologi', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Anak', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Penyakit Dalam', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Jantung & Pembuluh Darah', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Saraf', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Bedah', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Obsgyn', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Ortopaedi', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Kedokteran Jiwa', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis THT-KL', 
                'level' => 4
            ],
            [
                'name' => 'Sub Spesialis Ginjal Hipertensi', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Patologi Klinik', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Kedokteran Fisik dan Rehabilitasi', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Mata', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Dematologi dan Venereologi', 
                'level' => 4
            ],
            [
                'name' => 'Spesialis Urologi', 
                'level' => 4
            ],
            [
                'name' => 'Dokter Internship', 
                'level' => 4
            ],
        ];

        $id = 1;
        foreach ($chairs as $chair){
            Chair::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $chair['name'],
                    'level' => $chair['level']
                ]
            );
            $id++;
        }
    }
}
