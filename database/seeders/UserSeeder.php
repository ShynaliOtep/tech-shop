<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->delete();
        User::query()->create([
            'remember_token' => 'null,l6OFq.oe4YsySWJ7/De5vzpgq7stB56k8kw/ObPQROj74lriem',
            'email' => 'support@support.com',
            'password' => '$2y$12$PwcYwYO.f3j4FHNC3MrcG.dLnbZabriYPcMSUtuG5DVkRj.hgLF8a',
            'name' => 'secret',
        ]);
    }
}
