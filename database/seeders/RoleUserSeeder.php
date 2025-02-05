<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    public function run()
    {
        DB::table('role_users')->delete();
        DB::table('role_users')->insert(['user_id' => 1, 'role_id' => 1]);
    }
}
