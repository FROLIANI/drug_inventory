<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            ['id'=> Role::ADMIN, 'name'=>'admin'],
            ['id'=> Role::STAFF, 'name'=>'staff'],
        ]);

        User::firstOrCreate(
            ['email'=>'admin@gmail.com'],
            [
                'name' => 'Admin Admin',
                'username'=>'admin',
                'password'=>Hash::make('admin@12345678'),
                'role_id'=> Role::ADMIN,

            ]
        );
    }
}
