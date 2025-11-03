<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Staff;
use App\Models\StaffAdjustment;
use App\Models\StaffAppointment;
use App\Models\StaffContract;
use App\Models\StaffEntryEducation;
use App\Models\StaffStatus;
use App\Models\StaffWorkEducation;
use App\Models\StaffWorkExperience;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void 
    {
        $this->call([SystemSeeder::class, ChairSeeder::class, UnitSeeder::class, GroupSeeder::class]);

        $statuses = [
            ['name' => 'Tetap'],
            ['name' => 'Kontrak'],
            ['name' => 'Parttime'],
            ['name' => 'Training'],
            ['name' => 'PHL'],
            ['name' => 'Internship'],
        ];

        $roles = [
            ['name' => 'Admin'],
            ['name' => 'User'],
        ];

        foreach ($statuses as $status) {
            StaffStatus::create($status);
        }

        foreach ($roles as $role) {
            Role::create($role);
        }

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => '123456',
            'role_id' => 1
        ]);

        $units = [1,1,1,19,27,27,27];
        $groups = [1,12,12,12,12,12,12];
        $chairs = [1,3,15,53,69,70,71];
        $status = [1,1,1,1,2,4,6];

        for ($i = 0; $i < count($units); $i++){
            Staff::factory()
                ->state([
                    'staff_status_id' => $status[$i],
                    'chair_id' => $chairs[$i],
                    'group_id' => $groups[$i],
                    'unit_id' => $units[$i]
                ])
                ->afterCreating(function ($staff) {
                    $role = $staff->chair->level === 4 ? 2 : 1;

                    $role = str_contains($staff->chair->name, 'SDM') ? 1 : 2;

                    User::factory()->create([
                        'name' => $staff->name,
                        'email' => $staff->email,
                        'password' => '123456',
                        'role_id' => $role,
                        'staff_id' => $staff->id,
                    ]);

                    if ($staff->id == 4 || $staff->id == 5){
                        Unit::where('id', $staff->unit_id)->update(['leader_id' => $staff->id]);
                    }

                    StaffEntryEducation::factory()->create([
                        'staff_id' => $staff->id
                    ]);

                    if (fake()->boolean()) {
                        StaffWorkEducation::factory()->create([
                            'staff_id' => $staff->id,
                        ]);
                    }

                    if (fake()->boolean()) {
                        StaffWorkExperience::factory()->create([
                            'staff_id' => $staff->id,
                        ]);
                    }

                    switch ($staff->staff_status_id){
                        case 2:
                            StaffContract::factory()->create([
                                'staff_id' => $staff->id
                            ]);
                            break;
                        
                        case 1:
                            StaffAppointment::factory()->create([
                                'staff_id' => $staff->id
                            ]);
                            
                            if (fake()->boolean()) { // Kadang dibuat
                                StaffAdjustment::factory()->create([
                                    'staff_id' => $staff->id,
                                ]);
                            }
                            break;
                        default;
                    }
                })
            ->create();
        }
    }
}
