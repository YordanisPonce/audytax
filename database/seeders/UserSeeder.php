<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $users = collect([
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => bcrypt('adminadmin'),
                'role' => 'admin',
                'phone' => '+5358212822'
            ],
            [
                'name' => 'Consultant',
                'email' => 'consultant@consultant.com',
                'email_verified_at' => now(),
                'password' => bcrypt('consultantconsultant'),
                'role' => 'consultant',
                'phone' => '+5358212822'
            ],
            [
                'name' => 'Client',
                'email' => 'client@client.com',
                'email_verified_at' => now(),
                'password' => bcrypt('clientclient'),
                'role' => 'client',
                'phone' => '+5358212822'
            ],
        ]);

        $users->map(function ($user) {
            $user = collect($user);
            $newUser = User::create($user->except('role')->toArray());
            $newUser->assignRole($user['role']);
        });
    }
}
