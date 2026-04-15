<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@buyyourbook.ci'],
            [
                'name' => 'Administrateur',
                'password' => bcrypt('password'),
                'role' => UserRole::Admin,
                'phone' => '+225 0101010101',
                'is_active' => true,
            ]
        );
    }
}
