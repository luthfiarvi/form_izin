<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AssignAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'luthfiarviandi1@gmail.com';

        // Upsert admin akun
        DB::table('users')->updateOrInsert(
            ['email' => $email],
            [
                'name' => 'Admin Izin',
                'password' => Hash::make('admin1234'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'is_kepala_kepegawaian' => false,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
