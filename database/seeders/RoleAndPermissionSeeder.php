<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        $role = Role::updateOrCreate(
            ['id' => 1],
            [
                'id' => 1,
                'key' => 'admin',
                'label' => 'Administrator',
            ]
        );

        $permission = Permission::updateOrCreate(
            ['id' => 1],
            [
                'id' => 1,
                'key' => 'all',
                'label' => 'All permissions',
            ]
        );

        $role->permissions()->attach($permission);

        $role = Role::updateOrCreate(
            ['id' => 2],
            [
                'id' => 2,
                'key' => 'redator',
                'label' => 'Redator',
            ]
        );

        $permission = Permission::updateOrCreate(
            ['id' => 2],
            [
                'id' => 2,
                'key' => 'read_post',
                'label' => 'ver posts',
            ]
        );

        $role->permissions()->attach($permission);

        $permission = Permission::updateOrCreate(
            ['id' => 3],
            [
                'id' => 3,
                'key' => 'view_post',
                'label' => 'Ver post',
            ]
        );

        $role->permissions()->attach($permission);

        $permission = Permission::updateOrCreate(
            ['id' => 4],
            [
                'id' => 4,
                'key' => 'create_post',
                'label' => 'Criar post',
            ]
        );

        $role->permissions()->attach($permission);

        $permission = Permission::updateOrCreate(
            ['id' => 5],
            [
                'id' => 5,
                'key' => 'update_post',
                'label' => 'Atualizar post',
            ]
        );

        $role->permissions()->attach($permission);

        $permission = Permission::updateOrCreate(
            ['id' => 6],
            [
                'id' => 6,
                'key' => 'delete_post',
                'label' => 'Deletar post',
            ]
        );
        $role->permissions()->attach($permission);

    }
}
