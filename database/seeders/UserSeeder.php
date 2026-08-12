<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superadminRole = Role::where('name', 'Superadmin')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $userRole = Role::where('name', 'User')->first();

        User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Superadmin User',
                'password' => Hash::make('password'),
                'role_id' => $superadminRole?->id,
                'is_protected' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => $adminRole?->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User User',
                'password' => Hash::make('password'),
                'role_id' => $userRole?->id,
            ]
        );

        // Generate 200 additional random users, split between Admin and User roles
        $randomRoleIds = collect([$adminRole?->id, $userRole?->id])
            ->filter()
            ->values();

        User::factory()
            ->count(200)
            ->make() // build without saving so we can assign role_id per-user
            ->each(function (User $user) use ($randomRoleIds) {
                $user->role_id = $randomRoleIds->isNotEmpty()
                    ? $randomRoleIds->random()
                    : null;
                $user->save();
            });
    }
}