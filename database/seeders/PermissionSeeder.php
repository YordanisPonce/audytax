<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = collect([
            ['name' => 'user create', 'module_name' => 'user',],
            ['name' => 'user update', 'module_name' => 'user',],
            ['name' => 'user delete', 'module_name' => 'user',],
            ['name' => 'user show', 'module_name' => 'user',],
            ['name' => 'user index', 'module_name' => 'user',],
            //Auditory permisions
            ['name' => 'auditoryType create', 'module_name' => 'auditory',],
            ['name' => 'auditoryType update', 'module_name' => 'auditory',],
            ['name' => 'auditoryType delete', 'module_name' => 'auditory',],
            ['name' => 'auditoryType show', 'module_name' => 'auditory',],
            ['name' => 'auditoryType index', 'module_name' => 'auditory',],

            //fase permisions
            ['name' => 'fase create', 'module_name' => 'fase',],
            ['name' => 'fase update', 'module_name' => 'fase',],
            ['name' => 'fase delete', 'module_name' => 'fase',],
            ['name' => 'fase show', 'module_name' => 'fase',],
            ['name' => 'fase index', 'module_name' => 'fase',],
            //Document permisions
            ['name' => 'document create', 'module_name' => 'document',],
            ['name' => 'document update', 'module_name' => 'document',],
            ['name' => 'document delete', 'module_name' => 'document',],
            ['name' => 'document show', 'module_name' => 'document',],
            ['name' => 'document index', 'module_name' => 'document',],
            ['name' => 'document download', 'module_name' => 'document',],

             //QualityControl permisions
            ['name' => 'qualityControl create', 'module_name' => 'document',],
            ['name' => 'qualityControl update', 'module_name' => 'document',],
            ['name' => 'qualityControl delete', 'module_name' => 'document',],
            ['name' => 'qualityControl show', 'module_name' => 'document',],
            ['name' => 'qualityControl index', 'module_name' => 'document',],
            ['name' => 'qualityControl download', 'module_name' => 'document',],

            ['name' => 'permission index', 'module_name' => 'permission'],
            ['name' => 'permission create', 'module_name' => 'permission'],
            ['name' => 'permission update', 'module_name' => 'permission'],
            ['name' => 'permission delete', 'module_name' => 'permission'],
            ['name' => 'permission show', 'module_name' => 'permission'],

            ['name' => 'role index', 'module_name' => 'role'],
            ['name' => 'role create', 'module_name' => 'role'],
            ['name' => 'role update', 'module_name' => 'role'],
            ['name' => 'role delete', 'module_name' => 'role'],
            ['name' => 'role show', 'module_name' => 'role'],

            ['name' => 'database_backup viewAny', 'module_name' => 'database_backup'],
            ['name' => 'database_backup create', 'module_name' => 'database_backup'],
            ['name' => 'database_backup delete', 'module_name' => 'database_backup'],
            ['name' => 'database_backup download', 'module_name' => 'database_backup'],

            ['name' => 'menu users_list', 'module_name' => 'menu'],
            ['name' => 'menu role_permission', 'module_name' => 'menu'],
            ['name' => 'menu role_permission_permissions', 'module_name' => 'menu'],
            ['name' => 'menu role_permission_roles', 'module_name' => 'menu'],
            ['name' => 'menu database_backup', 'module_name' => 'menu'],
        ]);

        $web = collect([]);

        $permissions->map(function ($permission) use ($web) {
            $web->push([
                'name' => $permission['name'],
                'module_name' => $permission['module_name'],
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Permission::insert($web->toArray());
    }
}
