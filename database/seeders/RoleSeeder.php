<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $roles = [
            'admin',
            'consultant',
            'client',
        ];
        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::where(['name' => 'admin', 'guard_name' => 'web'])->firstOrFail();
        $admin->givePermissionTo(Permission::where('guard_name', 'web')->get());

        $consultant = Role::where(['name' => 'consultant', 'guard_name' => 'web'])->firstOrFail();
        $consultant->givePermissionTo([Permission::where('guard_name', 'web')->whereIn(
            'name',
            [
                'qualityControl show',
                'qualityControl index',
                'qualityControl download',
                'document show',
                'document index',
                'fase index',
                'fase show',
                'fase update',
            ]
        )->get()]);

        $client = Role::where(['name' => 'client', 'guard_name' => 'web'])->firstOrFail();
        $client->givePermissionTo([Permission::where('guard_name', 'web')->whereIn(
            'name',
            [
                'document index', 'fase index', 'fase update',
                'qualityControl index', 'document download'
            ]
        )->get()]);
    }
}
