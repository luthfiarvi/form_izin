<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'luthfiarviandi1@gmail.com';
        DB::table('users')->where('email', $email)->update([
            'role' => 'admin',
        ]);
    }
}

