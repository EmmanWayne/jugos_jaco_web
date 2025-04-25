<?php

namespace Database\Seeders;

use App\Enums\UserRole;
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
        $admin = Role::create(["name" => UserRole::ADMIN]);
        Permission::create(["name" => "create-user"]);
        Permission::create(["name" => "view-user"]);
        Permission::create(["name" => "delete-user"]);

        $admin->givePermissionTo("create-user");
        $admin->givePermissionTo("view-user");
        $admin->givePermissionTo("delete-user");

        $employed = Role::create(["name" => UserRole::EMPLOYED]);
        $cashier = Role::create(["name" => UserRole::CASHEER]);
    }
}
