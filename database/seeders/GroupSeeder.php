<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Direktur',
            ],
            [
                'name' => 'Tenaga Medis',
            ],
            [
                'name' => 'Tenaga Keperawatan',
            ],
            [
                'name' => 'Tenaga Kebidanan',
            ],
            [
                'name' => 'Tenaga Kefarmasian',
            ],
            [
                'name' => 'Tenaga Kesehatan Masyarakat',
            ],
            [
                'name' => 'Tenaga Kesehatan Lingkungan',
            ],
            [
                'name' => 'Tenaga Gizi',
            ],
            [
                'name' => 'Tenaga Teknik Biomedika',
            ],
            [
                'name' => 'Tenaga Keterapian Fisik',
            ],
            [
                'name' => 'Tenaga Keteknisian Medis',
            ],
            [
                'name' => 'Tenaga Lainnya',
            ],
            [
                'name' => 'Tenaga Internship'
            ],
        ];

        foreach ($groups as $group){
            Group::create($group);
        }
    }
}
