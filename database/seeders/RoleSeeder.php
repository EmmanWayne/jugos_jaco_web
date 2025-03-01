<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(["name" => "admin"]);
        Permission::create(["name" => "create-user"]);
        Permission::create(["name" => "view-user"]);
        Permission::create(["name" => "delete-user"]);

        $admin->givePermissionTo("create-user");
        $admin->givePermissionTo("view-user");
        $admin->givePermissionTo("delete-user");

        $employed = Role::create(["name" => "employed"]);
        $cashier = Role::create(["name" => "cashier"]);
    }
}
