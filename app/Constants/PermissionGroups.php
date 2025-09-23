<?php

namespace App\Constants;

class PermissionGroups
{
    /**
     * Permisos para Super Administrador - Acceso completo a todo el sistema
     */
    public static function getSuperAdminPermissions(): array
    {
        $permissions = [];
        
        // Obtener todos los modelos y generar permisos CRUD para cada uno
        foreach (ModelConstants::getAllModels() as $model) {
            $permissions = array_merge($permissions, PermissionConstants::getCrudPermissions($model));
        }
        
        return array_unique($permissions);
    }

    /**
     * Permisos para Administrador - Acceso completo a todo el sistema
     */
    public static function getAdminPermissions(): array
    {
        // Los administradores tienen los mismos permisos que super admin
        return self::getSuperAdminPermissions();
    }

    /**
     * Permisos para Cajero - Acceso específico a inventarios, ventas y productos asignados
     */
    public static function getCashierPermissions(): array
    {
        $permissions = [];

        // Permisos limitados para FinishedProductInventory (crear, ver lista y ver detalles)
        $permissions[] = PermissionConstants::FINISHED_PRODUCT_INVENTORY_CREATE;
        $permissions[] = PermissionConstants::FINISHED_PRODUCT_INVENTORY_VIEW;
        $permissions[] = PermissionConstants::FINISHED_PRODUCT_INVENTORY_LIST;

        // Permisos limitados para RawMaterialsInventory (crear, ver lista y ver detalles)
        $permissions[] = PermissionConstants::RAW_MATERIALS_INVENTORY_CREATE;
        $permissions[] = PermissionConstants::RAW_MATERIALS_INVENTORY_VIEW;
        $permissions[] = PermissionConstants::RAW_MATERIALS_INVENTORY_LIST;

        // Permisos limitados para Sales (crear, ver lista y ver detalles)
        $permissions[] = PermissionConstants::SALE_CREATE;
        $permissions[] = PermissionConstants::SALE_VIEW;
        $permissions[] = PermissionConstants::SALE_LIST;

        // Permisos limitados para SaleDetails (crear, ver lista y ver detalles)
        $permissions[] = PermissionConstants::SALE_DETAIL_CREATE;
        $permissions[] = PermissionConstants::SALE_DETAIL_VIEW;
        $permissions[] = PermissionConstants::SALE_DETAIL_LIST;

        // Permisos completos para AssignedProduct usando getCrudPermissions
        $permissions = array_merge($permissions, PermissionConstants::getCrudPermissions(PermissionConstants::MODEL_ASSIGNED_PRODUCT));

        // Permisos completos para DetailAssignedProduct usando getCrudPermissions
        $permissions = array_merge($permissions, PermissionConstants::getCrudPermissions(PermissionConstants::MODEL_DETAIL_ASSIGNED_PRODUCT));

        $permissions = array_merge($permissions, PermissionConstants::getCrudPermissions(PermissionConstants::MODEL_CLIENT));

        return array_unique($permissions);
    }
}