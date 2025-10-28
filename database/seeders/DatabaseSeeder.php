<?php

namespace Database\Seeders;

use App\Models\Chair;
use App\Models\Role;
use App\Models\Staff;
use App\Models\StaffStatus;
use App\Models\SystemRule;
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
        // $this->call([ChairSeeder::class, UnitSeeder::class]);

        $this->call([SystemRule::class]);
        // $roles = [
        //     ['name' => 'Admin'],
        //     ['name' => 'User'],
        // ];

        // foreach ($roles as $role) {
        //     Role::create($role);
        // }

        // User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => '123456',
        //     'role_id' => 1
        // ]);

        // Staff::factory(4)->afterCreating(function ($staff) {
        //         $role = $staff->chair->level === 4 ? 2 : 1;
        //         User::factory()->create([
        //             'name' => $staff->name,
        //             'email' => $staff->personal_email,
        //             'password' => '123456',
        //             'role_id' => $role,
        //             'staff_id' => $staff->id,
        //         ]);
        //     })
        // ->create();
    }
}
