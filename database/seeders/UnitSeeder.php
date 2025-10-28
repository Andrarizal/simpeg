<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'leader_id' => null,
            ],
            [
                'name' => 'HD',
                'leader_id' => null,
            ],
            [
                'name' => 'OK',
                'leader_id' => null,
            ],
            [
                'name' => 'POLI',
                'leader_id' => null,
            ],
            [
                'name' => 'BIMA',
                'leader_id' => null,
            ],
            [
                'name' => 'SHINTA',
                'leader_id' => null,
            ],
            [
                'name' => 'RAMA',
                'leader_id' => null,
            ],
            [
                'name' => 'ICU',
                'leader_id' => null,
            ],
            [
                'name' => 'UGD',
                'leader_id' => null,
            ],
            [
                'name' => 'VK KIA',
                'leader_id' => null,
            ],
            [
                'name' => 'Farmasi',
                'leader_id' => null,
            ],
            [
                'name' => 'Laboratorium',
                'leader_id' => null,
            ],
            [
                'name' => 'Radiologi',
                'leader_id' => null,
            ],
            [
                'name' => 'Pendaftaran',
                'leader_id' => null,
            ],
            [
                'name' => 'Rekam Medis',
                'leader_id' => null,
            ],
            [
                'name' => 'Casemix',
                'leader_id' => null,
            ],
            [
                'name' => 'Keuangan',
                'leader_id' => null,
            ],
            [
                'name' => 'Kassa',
                'leader_id' => null,
            ],
            [
                'name' => 'Sekretariat, SDM Diklat, Humas Marketing',
                'leader_id' => null,
            ],
            [
                'name' => 'Pemeliharaan Sarana Rumah Sakit (PSRS)',
                'leader_id' => null,
            ],
            [
                'name' => 'Fisioterapi',
                'leader_id' => null,
            ],
            [
                'name' => 'Gizi',
                'leader_id' => null,
            ],
            [
                'name' => 'Fisioterapi',
                'leader_id' => null,
            ],
            [
                'name' => 'Central Sterile Supply Departrment (CSSD)',
                'leader_id' => null,
            ],
            [
                'name' => 'Satuan Keamanan (SATPAM)',
                'leader_id' => null,
            ],
            [
                'name' => 'Umum',
                'leader_id' => null,
            ],
            [
                'name' => 'Sistem Informasi Rumah Sakit (SIRS)',
                'leader_id' => null,
            ],
            [
                'name' => 'Laundry',
                'leader_id' => null,
            ],
            [
                'name' => 'Driver',
                'leader_id' => null,
            ],
            [
                'name' => 'Teknisi',
                'leader_id' => null,
            ],
            [
                'name' => 'Dokter Umum',
                'leader_id' => null,
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
