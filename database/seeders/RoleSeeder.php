<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\UserRole;
use App\Constants\PermissionConstants;
use App\Constants\PermissionGroups;
use App\Constants\ModelConstants;
use Illuminate\Support\Facades\Log;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Iniciando la creación de roles y permisos...');

        try {
            // Crear roles
            $this->createRoles();
            
            // Crear permisos
            $this->createPermissions();
            
            // Asignar permisos a roles
            $this->assignPermissionsToRoles();
            
            $this->command->info('Roles y permisos creados exitosamente.');
            
        } catch (\Exception $e) {
            $this->command->error('Error al crear roles y permisos: ' . $e->getMessage());
            Log::error('Error en RoleSeeder: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crear los roles del sistema
     */
    private function createRoles(): void
    {
        $this->command->info('Creando roles...');
        
        $roles = [
            UserRole::SUPERADMIN->value => 'Super Administrador con acceso completo',
            UserRole::ADMIN->value => 'Administrador con acceso completo',
            UserRole::EMPLOYED->value => 'Empleado con permisos limitados',
            UserRole::CASHEER->value => 'Cajero con permisos de ventas y pagos',
        ];

        foreach ($roles as $roleName => $description) {
            Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );
            $this->command->info("✓ Rol creado: {$roleName}");
        }
    }

    /**
     * Crear todos los permisos del sistema
     */
    private function createPermissions(): void
    {
        $this->command->info('Creando permisos...');
        
        // Obtener todos los permisos usando PermissionGroups para SuperAdmin (que incluye todos)
        $allPermissions = PermissionGroups::getSuperAdminPermissions();
        
        $createdCount = 0;
        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['guard_name' => 'web']
            );
            $createdCount++;
        }
        
        $this->command->info("✓ {$createdCount} permisos creados/verificados");
    }

    /**
     * Asignar permisos a los roles
     */
    private function assignPermissionsToRoles(): void
    {
        $this->command->info('Asignando permisos a roles...');
        
        // Obtener roles
        $superAdminRole = Role::findByName(UserRole::SUPERADMIN->value);
        $adminRole = Role::findByName(UserRole::ADMIN->value);
        $cashierRole = Role::findByName(UserRole::CASHEER->value);

        // SUPERADMIN: Acceso completo usando PermissionGroups
        $superAdminPermissions = PermissionGroups::getSuperAdminPermissions();
        $this->syncPermissionsToRole($superAdminRole, $superAdminPermissions, 'SUPERADMIN');

        // ADMIN: Acceso completo usando PermissionGroups
        $adminPermissions = PermissionGroups::getAdminPermissions();
        $this->syncPermissionsToRole($adminRole, $adminPermissions, 'ADMIN');

        // EMPLOYED: Solo crear el rol sin permisos específicos
        $this->command->info('✓ EMPLOYED: Rol creado sin permisos específicos asignados');

        // CASHIER: Permisos específicos de cajero usando PermissionGroups
        $cashierPermissions = PermissionGroups::getCashierPermissions();
        $this->syncPermissionsToRole($cashierRole, $cashierPermissions, 'CASHIER');
    }

    /**
     * Sincronizar permisos a un rol con validación
     */
    private function syncPermissionsToRole(Role $role, array $permissions, string $roleName): void
    {
        try {
            // Validar que todos los permisos existen
            $validPermissions = $this->validatePermissions($permissions);
            
            // Sincronizar permisos
            $role->syncPermissions($validPermissions);
            
            $this->command->info("✓ {$roleName}: " . count($validPermissions) . " permisos asignados");
            
        } catch (\Exception $e) {
            $this->command->error("✗ Error asignando permisos a {$roleName}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validar que los permisos existen en la base de datos
     */
    private function validatePermissions(array $permissions): array
    {
        $validPermissions = [];
        $invalidPermissions = [];
        
        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            
            if ($permission) {
                $validPermissions[] = $permission;
            } else {
                $invalidPermissions[] = $permissionName;
            }
        }
        
        if (!empty($invalidPermissions)) {
            $this->command->warn('Permisos no encontrados: ' . implode(', ', $invalidPermissions));
            Log::warning('Permisos no encontrados en RoleSeeder', ['permissions' => $invalidPermissions]);
        }
        
        return $validPermissions;
    }

    /**
     * Mostrar resumen de permisos por rol
     */
    public function showPermissionsSummary(): void
    {
        $this->command->info("\n=== RESUMEN DE PERMISOS POR ROL ===");
        
        $roles = Role::with('permissions')->get();
        
        foreach ($roles as $role) {
            $this->command->info("\n{$role->name}: {$role->permissions->count()} permisos");
            
            // Agrupar permisos por modelo para mejor legibilidad
            $permissionsByModel = [];
            foreach ($role->permissions as $permission) {
                $parts = explode('.', $permission->name);
                $model = $parts[0] ?? 'Unknown';
                $action = $parts[1] ?? 'Unknown';
                
                if (!isset($permissionsByModel[$model])) {
                    $permissionsByModel[$model] = [];
                }
                $permissionsByModel[$model][] = $action;
            }
            
            foreach ($permissionsByModel as $model => $actions) {
                $this->command->line("  - {$model}: " . implode(', ', $actions));
            }
        }
    }
}
