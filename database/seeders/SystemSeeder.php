<?php

namespace Database\Seeders;

use App\Models\SystemRule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'group' => 'company',
                'key' => 'company_name',
                'value' => 'RSU Mitra Paramedika',
                'type' => 'string',
                'description' => 'Nama Perusahaan'
            ],
            [
                'group' => 'company',
                'key' => 'company_address',
                'value' => 'Widomartani, Ngemplak, Sleman, DIY',
                'type' => 'string',
                'description' => 'Alamat Perusahaan'
            ],
            [
                'group' => 'company',
                'key' => 'company_email',
                'value' => 'rsumpyk@gmail.com',
                'type' => 'string',
                'description' => 'Email Perusahaan'
            ],
            [
                'group' => 'system',
                'key' => 'system_name',
                'value' => 'Sistem Informasi Kepegawaian',
                'type' => 'string',
                'description' => 'Nama Sistem'
            ],
            [
                'group' => 'policy',
                'key' => 'max_leave_days',
                'value' => '12',
                'type' => 'integer',
                'description' => 'Maksimal Cuti Setahun'
            ],
            [
                'group' => 'policy',
                'key' => 'max_permission_days',
                'value' => '4',
                'type' => 'integer',
                'description' => 'Maksimal Izin Setahun'
            ],
        ];

        foreach($rules as $rule){
            SystemRule::create($rule);
        }
    }
}
