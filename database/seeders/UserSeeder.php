<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'lastname' => 'yaghoobi',
                'companyname' => 'dsadsa',
                'companycode' => 12345678,
                'role' => 'Admin',
                'email' => 'ashkanygh5@gmail.com',
                'phone' => '09019029914',
                'email_verified_at' => now(),
                'password' => Hash::make(12341234),
            ],
            [
                'name' => 'Client',
                'lastname' => 'yaghoobi',
                'companyname' => 'dsadsa',
                'companycode' => 12345678,
                'role' => 'Client',
                'email' => 'aliygh021@gmail.com',
                'phone' => '09019029916',
                'email_verified_at' => now(),
                'password' => Hash::make(12341234),
            ],
            [
                'name' => 'Company',
                'lastname' => 'yaghoobi',
                'companyname' => 'dsadsa',
                'companycode' => 12345678,
                'role' => 'Company',
                'email' => 'aliygh8080@gmail.com',
                'phone' => '09019029915',
                'email_verified_at' => now(),
                'password' => Hash::make(12341234),
            ]
        ]);
    }
}
