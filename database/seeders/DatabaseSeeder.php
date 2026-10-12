<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CategorySeeder::class);
        $this->call(PropertySeeder::class);

        $admin = User::firstOrNew(['email' => 'admin@example.com']);
        $admin->name = 'Admin';
        $admin->password = 'password';
        $admin->role = UserRole::ADMIN;
        $admin->save();
    }
}
