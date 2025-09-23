<?php

namespace App\Constants;

/**
 * Constantes para los nombres de modelos del sistema
 * 
 * Esta clase centraliza todos los nombres de modelos utilizados
 * en el sistema de permisos y otras funcionalidades.
 */
class ModelConstants
{
    public const MODEL_ACCOUNT_RECEIVABLE = 'AccountReceivable';
    public const MODEL_ASSIGNED_PRODUCT = 'AssignedProduct';
    public const MODEL_BILL = 'Bill';
    public const MODEL_BRANCH = 'Branch';
    public const MODEL_CATEGORY = 'Category';
    public const MODEL_CLIENT = 'Client';
    public const MODEL_CLIENT_VISIT_DAY = 'ClientVisitDay';
    public const MODEL_DAILY_SALES_RECONCILIATION = 'DailySalesReconciliation';
    public const MODEL_DEPOSIT = 'Deposit';
    public const MODEL_DETAIL_ASSIGNED_PRODUCT = 'DetailAssignedProduct';
    public const MODEL_EMPLOYEE = 'Employee';
    public const MODEL_FINISHED_PRODUCT_INVENTORY = 'FinishedProductInventory';
    public const MODEL_INVOICES_SERIES = 'InvoicesSeries';
    public const MODEL_LOCATION = 'Location';
    public const MODEL_MANAGEMENT_INVENTORY = 'ManagementInventory';
    public const MODEL_PAYMENT = 'Payment';
    public const MODEL_PRODUCT = 'Product';
    public const MODEL_PRODUCT_PRICE = 'ProductPrice';
    public const MODEL_PRODUCT_RETURN = 'ProductReturn';
    public const MODEL_PRODUCT_UNIT = 'ProductUnit';
    public const MODEL_RAW_MATERIALS_INVENTORY = 'RawMaterialsInventory';
    public const MODEL_RESOURCE_MEDIA = 'ResourceMedia';
    public const MODEL_SALE = 'Sale';
    public const MODEL_SALE_DETAIL = 'SaleDetail';
    public const MODEL_SALE_TAX_TOTAL = 'SaleTaxTotal';
    public const MODEL_TAX_CATEGORY = 'TaxCategory';
    public const MODEL_TYPE_PRICE = 'TypePrice';
    public const MODEL_UNIT = 'Unit';
    public const MODEL_USER = 'User';

    
    /**
     * Obtiene todos los modelos disponibles
     */
    public static function getAllModels(): array
    {
        return [
            self::MODEL_ACCOUNT_RECEIVABLE,
            self::MODEL_ASSIGNED_PRODUCT,
            self::MODEL_BILL,
            self::MODEL_BRANCH,
            self::MODEL_CATEGORY,
            self::MODEL_CLIENT,
            self::MODEL_CLIENT_VISIT_DAY,
            self::MODEL_DAILY_SALES_RECONCILIATION,
            self::MODEL_DEPOSIT,
            self::MODEL_DETAIL_ASSIGNED_PRODUCT,
            self::MODEL_EMPLOYEE,
            self::MODEL_FINISHED_PRODUCT_INVENTORY,
            self::MODEL_INVOICES_SERIES,
            self::MODEL_LOCATION,
            self::MODEL_MANAGEMENT_INVENTORY,
            self::MODEL_PAYMENT,
            self::MODEL_PRODUCT,
            self::MODEL_PRODUCT_PRICE,
            self::MODEL_PRODUCT_RETURN,
            self::MODEL_PRODUCT_UNIT,
            self::MODEL_RAW_MATERIALS_INVENTORY,
            self::MODEL_RESOURCE_MEDIA,
            self::MODEL_SALE,
            self::MODEL_SALE_DETAIL,
            self::MODEL_SALE_TAX_TOTAL,
            self::MODEL_TAX_CATEGORY,
            self::MODEL_TYPE_PRICE,
            self::MODEL_UNIT,
            self::MODEL_USER,
        ];
    }

    /**
     * Obtiene modelos por categoría
     * 
     * @return array
     */
    public static function getModelsByCategory(): array
    {
        return [
            'users' => [
                self::MODEL_USER,
                self::MODEL_EMPLOYEE,
            ],
            'products' => [
                self::MODEL_PRODUCT,
                self::MODEL_PRODUCT_PRICE,
                self::MODEL_PRODUCT_UNIT,
                self::MODEL_CATEGORY,
                self::MODEL_UNIT,
                self::MODEL_TYPE_PRICE,
            ],
            'inventory' => [
                self::MODEL_FINISHED_PRODUCT_INVENTORY,
                self::MODEL_RAW_MATERIALS_INVENTORY,
                self::MODEL_MANAGEMENT_INVENTORY,
                self::MODEL_ASSIGNED_PRODUCT,
                self::MODEL_DETAIL_ASSIGNED_PRODUCT,
            ],
            'sales' => [
                self::MODEL_SALE,
                self::MODEL_SALE_DETAIL,
                self::MODEL_SALE_TAX_TOTAL,
                self::MODEL_DAILY_SALES_RECONCILIATION,
            ],
            'clients' => [
                self::MODEL_CLIENT,
                self::MODEL_CLIENT_VISIT_DAY,
                self::MODEL_LOCATION,
            ],
            'finance' => [
                self::MODEL_PAYMENT,
                self::MODEL_ACCOUNT_RECEIVABLE,
                self::MODEL_BILL,
                self::MODEL_DEPOSIT,
                self::MODEL_TAX_CATEGORY,
            ],
            'administration' => [
                self::MODEL_BRANCH,
                self::MODEL_INVOICES_SERIES,
            ],
            'returns' => [
                self::MODEL_PRODUCT_RETURN,
            ],
            'media' => [
                self::MODEL_RESOURCE_MEDIA,
            ],
        ];
    }

    /**
     * Verifica si un modelo existe
     * 
     * @param string $model
     * @return bool
     */
    public static function modelExists(string $model): bool
    {
        return in_array($model, self::getAllModels());
    }
}