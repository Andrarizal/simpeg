<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'name' => 'Manajerial',
                'leader_id' => 1,
            ],
            [
                'name' => 'HD',
                'leader_id' => 16,
            ],
            [
                'name' => 'OK',
                'leader_id' => 18,
            ],
            [
                'name' => 'POLI',
                'leader_id' => 20,
            ],
            [
                'name' => 'BIMA',
                'leader_id' => 22,
            ],
            [
                'name' => 'SHINTA',
                'leader_id' => 24,
            ],
            [
                'name' => 'RAMA',
                'leader_id' => 26,
            ],
            [
                'name' => 'ICU',
                'leader_id' => 28,
            ],
            [
                'name' => 'UGD',
                'leader_id' => 30,
            ],
            [
                'name' => 'VK KIA',
                'leader_id' => 33,
            ],
            [
                'name' => 'Farmasi',
                'leader_id' => 35,
            ],
            [
                'name' => 'Laboratorium',
                'leader_id' => 39,
            ],
            [
                'name' => 'Radiologi',
                'leader_id' => 41,
            ],
            [
                'name' => 'Pendaftaran',
                'leader_id' => 43,
            ],
            [
                'name' => 'Rekam Medis',
                'leader_id' => 45,
            ],
            [
                'name' => 'Casemix',
                'leader_id' => 47,
            ],
            [
                'name' => 'Keuangan',
                'leader_id' => null,
            ],
            [
                'name' => 'Kassa',
                'leader_id' => 50,
            ],
            [
                'name' => 'Sekretariat, SDM Diklat, Humas Marketing',
                'leader_id' => null,
            ],
            [
                'name' => 'Fisioterapi',
                'leader_id' => null,
            ],
            [
                'name' => 'Gizi',
                'leader_id' => 57,
            ],
            [
                'name' => 'Logistik',
                'leader_id' => null,
            ],
            [
                'name' => 'Sanitasi & Central Sterile Supply Departrment (CSSD)',
                'leader_id' => 61,
            ],
            [
                'name' => 'Elektromedis & Teknis',
                'leader_id' => 64,
            ],
            [
                'name' => 'Satuan Keamanan (SATPAM)',
                'leader_id' => 67,
            ],
            [
                'name' => 'Umum',
                'leader_id' => 67,
            ],
            [
                'name' => 'Sistem Informasi Rumah Sakit (SIRS)',
                'leader_id' => null,
            ],
            [
                'name' => 'Laundry',
                'leader_id' => 74,
            ],
            [
                'name' => 'Driver',
                'leader_id' => null,
            ],
            [
                'name' => 'Dokter Umum',
                'leader_id' => 77,
            ],
            [
                'name' => 'Poli Gigi',
                'leader_id' => null,
            ],
        ];

        foreach ($units as $unit){
            Unit::create($unit);
        }
    }
}
