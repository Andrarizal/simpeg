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
                'level' => 1,
                'head_id' => null
            ],
            [
                'name' => 'Kepala Seksi Pelayanan & Keperawatan',
                'level' => 2,
                'head_id' => 1
            ],
            [
                'name' => 'Kepala Seksi Mutu & Data Informasi',
                'level' => 2,
                'head_id' => 1
            ],
            [
                'name' => 'Kepala Seksi Pelayanan & Sarana Penunjang',
                'level' => 2,
                'head_id' => 1
            ],
            [
                'name' => 'Kepala Sub Bagian Tata Usaha',
                'level' => 2,
                'head_id' => 1
            ],
            [
                'name' => 'Koordinator Pengembangan Mutu',
                'level' => 3,
                'head_id' => 3
            ],
            [
                'name' => 'Koordinator Keperawatan',
                'level' => 3,
                'head_id' => 2
            ],
            [
                'name' => 'Koordinator Pelayanan Penunjang',
                'level' => 3,
                'head_id' => 4
            ],
            [
                'name' => 'Koordinator RM & Casemix',
                'level' => 3,
                'head_id' => 3
            ],
            [
                'name' => 'Koordinator Pelayanan Medis',
                'level' => 3,
                'head_id' => 2
            ],
            [
                'name' => 'Koordinator Keuangan & Akuntansi',
                'level' => 3,
                'head_id' => 5
            ],
            [
                'name' => 'Koordinator Sarana Pelayanan',
                'level' => 3,
                'head_id' => 4
            ],
            [
                'name' => 'Koordinator Umum & Kepegawaian',
                'level' => 3,
                'head_id' => 5
            ],
            [
                'name' => 'Koordiantor Perencanaan, Evaluasi & Pelaporan',
                'level' => 3,
                'head_id' => 5
            ],
            [
                'name' => 'Koordinator Data dan Informasi',
                'level' => 3,
                'head_id' => 3
            ],
            [
                'name' => 'Koor HD', 
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Perawat HD',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Koor OK',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Perawat OK',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Koor Poliklinik',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Perawat Poliklinik',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Koor Bima',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Perawat Bima',
                'level' => 4,
                'head_id' => 7
            ],
            
            [
                'name' => 'Koor Shinta',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Perawat Shinta',
                'level' => 4,
                'head_id' => 7
            ],
            
            [
                'name' => 'Koor Rama',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Perawat Rama',
                'level' => 4,
                'head_id' => 7
            ],
            
            [
                'name' => 'Koor ICU',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Perawat ICU',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Koor UGD',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Perawat UGD',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Perawat Anestesi',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Koor Bidan',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Pelaksana Bidan',
                'level' => 4,
                'head_id' => 7
            ],
            [
                'name' => 'Koor Farmasi',
                'level' => 4,
                'head_id' => 8
            ],
            [
                'name' => 'Apoteker',
                'level' => 4,
                'head_id' => 8
            ],
            [
                'name' => 'Tenaga Teknis Farmasi',
                'level' => 4,
                'head_id' => 8
            ],
            [
                'name' => 'Administrasi Farmasi',
                'level' => 4,
                'head_id' => 8
            ],
            [
                'name' => 'Koor Laboratorium',
                'level' => 4,
                'head_id' => 8
            ],
            [
                'name' => 'Pelaksana Laboratorium',
                'level' => 4,
                'head_id' => 8
            ],
            [
                'name' => 'Koor Radiologi', 
                'level' => 4,
                'head_id' => 8
            ],
            [
                'name' => 'Radiografer', 
                'level' => 4,
                'head_id' => 8
            ],
            [
                'name' => 'Koor Pendaftaran', 
                'level' => 4,
                'head_id' => 9
            ],
            [
                'name' => 'Pelaksana Pendaftaran', 
                'level' => 4,
                'head_id' => 9
            ],
            [
                'name' => 'Koor RM', 
                'level' => 4,
                'head_id' => 9
            ],
            [
                'name' => 'Pelaksana Rekam Medis', 
                'level' => 4,
                'head_id' => 9
            ],
            [
                'name' => 'Koor Casemix', 
                'level' => 4,
                'head_id' => 9
            ],
            [
                'name' => 'Pelaksana Casemix', 
                'level' => 4,
                'head_id' => 9
            ],
            [
                'name' => 'Pelaksana Keuangan', 
                'level' => 4,
                'head_id' => 11
            ],
            [
                'name' => 'Koor Kassa', 
                'level' => 4,
                'head_id' => 11
            ],
            [
                'name' => 'Pelaksana Kassa', 
                'level' => 4,
                'head_id' => 11
            ],
            [
                'name' => 'Sekretariat', 
                'level' => 4,
                'head_id' => 13
            ],
            [
                'name' => 'Humas Marketing', 
                'level' => 4,
                'head_id' => 6
            ],
            [
                'name' => 'Staf SDM', 
                'level' => 4,
                'head_id' => 6
            ],
            [
                'name' => 'Staf Diklat', 
                'level' => 4,
                'head_id' => 6
            ],
            [
                'name' => 'Fisioterapis', 
                'level' => 4,
                'head_id' => 8
            ],
            [
                'name' => 'Koor Gizi', 
                'level' => 4,
                'head_id' => 8
            ],
            [
                'name' => 'Ahli Gizi', 
                'level' => 4,
                'head_id' => 8
            ],
            [
                'name' => 'Pelaksana Gizi', 
                'level' => 4,
                'head_id' => 8
            ],
            [
                'name' => 'Pelaksana Logistik', 
                'level' => 4,
                'head_id' => 13
            ],
            [
                'name' => 'Kepala Unit Sanitasi & CSSD', 
                'level' => 4,
                'head_id' => 12
            ],
            [
                'name' => 'Sanitarian', 
                'level' => 4,
                'head_id' => 12
            ],
            [
                'name' => 'Pelaksana CSSD', 
                'level' => 4,
                'head_id' => 11
            ],
            [
                'name' => 'Kepala Unit Elektromedis & Teknisi', 
                'level' => 4,
                'head_id' => 12
            ],
            [
                'name' => 'Elektromedis', 
                'level' => 4,
                'head_id' => 12
            ],
            [
                'name' => 'Pelaksana Teknisi', 
                'level' => 4,
                'head_id' => 12
            ],
            [
                'name' => 'Koor Satpam-Umum', 
                'level' => 4,
                'head_id' => 13
            ],
            [
                'name' => 'Pelaksana Satpam', 
                'level' => 4,
                'head_id' => 13
            ],
            [
                'name' => 'Pelaksana Umum', 
                'level' => 4,
                'head_id' => 13
            ],
            [
                'name' => 'Analis Sistem', 
                'level' => 4,
                'head_id' => 15
            ],
            [
                'name' => 'Analis Hardware', 
                'level' => 4,
                'head_id' => 15
            ],
            [
                'name' => 'Programer', 
                'level' => 4,
                'head_id' => 15
            ],
            [
                'name' => 'UX Designer', 
                'level' => 4,
                'head_id' => 15
            ],
            [
                'name' => 'Koor Laundry', 
                'level' => 4,
                'head_id' => 12
            ],
            [
                'name' => 'Pelaksana Laundry', 
                'level' => 4,
                'head_id' => 12
            ],
            [
                'name' => 'Pelaksana Driver', 
                'level' => 4,
                'head_id' => 13
            ],
            [
                'name' => 'Koor Dokter', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Dokter Umum', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Dokter Gigi', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Anestesi', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Radiologi', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Anak', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Penyakit Dalam', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Jantung & Pembuluh Darah', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Saraf', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Bedah', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Obsgyn', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Ortopaedi', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Kedokteran Jiwa', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis THT-KL', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Sub Spesialis Ginjal Hipertensi', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Patologi Klinik', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Kedokteran Fisik dan Rehabilitasi', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Mata', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Dematologi dan Venereologi', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Spesialis Urologi', 
                'level' => 4,
                'head_id' => 10
            ],
            [
                'name' => 'Dokter Internship', 
                'level' => 4,
                'head_id' => 10
            ],
        ];

        $id = 1;
        foreach ($chairs as $chair){
            Chair::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $chair['name'],
                    'level' => $chair['level'],
                    'head_id' => $chair['head_id'],
                ]
            );
            $id++;
        }
    }
}
