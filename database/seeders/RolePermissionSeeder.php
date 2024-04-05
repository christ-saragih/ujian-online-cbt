<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // mengatur permission
        $permissions = [
            'view course',
            'add course',
            'edit course',
            'delete course',
        ];

        foreach($permissions as $permission) {
            Permission::create([
                'name' => $permission
            ]);
        }

        $teacherRole = Role::create([
            'name' => 'teacher'
        ]);

        $teacherRole->givePermissionTo([
            'view course',
            'add course',
            'edit course',
            'delete course',
        ]);

        $studentRole = Role::create([
            'name' => 'student'
        ]);

        $studentRole->givePermissionTo([
            'view course',
        ]);

        // membuat data user super admin
        $user = User::create([
            'name' => 'bennefit',
            'email' => 'bennn.19@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        $user->assignRole($teacherRole);

    }
}
