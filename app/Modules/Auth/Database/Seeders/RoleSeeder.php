<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\Permission;
use Modules\Auth\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles & permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'api';

        $permissions = [
            'content' => [
                'create content',
                'get content',
                'update content',
                'delete content',
                'list content',
            ],
            'subscription' => [
                'assign subscription'
            ]
        ];


        $permissionModels = [];

        foreach ($permissions as $group => $items) {
            foreach ($items as $permission) {
                $permissionModels[$permission] = Permission::firstOrCreate([
                    'name'       => $permission,
                    'guard_name' => $guard,
                ]);
            }
        }

        $roles = [
            'admin' => [
                'create content',
                'get content',
                'update content',
                'delete content',
                'list content',
                'assign subscription'
            ],
            'editor' => [
                'create content',
                'get content',
                'update content',
                'list content',
            ],
            'user' => [
                'list content',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate([
                'name'       => $roleName,
                'guard_name' => $guard,
            ]);

            $role->syncPermissions($rolePermissions);
        }
    }
}
