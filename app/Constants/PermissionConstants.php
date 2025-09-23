<?php

namespace App\Constants;

class PermissionConstants
{
    // Acciones estándar CRUD
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_LIST = 'list';
    public const ACTION_VIEW = 'view';

    // Modelos del sistema
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

    // Permisos específicos para AccountReceivable
    public const ACCOUNT_RECEIVABLE_CREATE = self::MODEL_ACCOUNT_RECEIVABLE . '.' . self::ACTION_CREATE;
    public const ACCOUNT_RECEIVABLE_VIEW = self::MODEL_ACCOUNT_RECEIVABLE . '.' . self::ACTION_VIEW;
    public const ACCOUNT_RECEIVABLE_UPDATE = self::MODEL_ACCOUNT_RECEIVABLE . '.' . self::ACTION_UPDATE;
    public const ACCOUNT_RECEIVABLE_DELETE = self::MODEL_ACCOUNT_RECEIVABLE . '.' . self::ACTION_DELETE;
    public const ACCOUNT_RECEIVABLE_LIST = self::MODEL_ACCOUNT_RECEIVABLE . '.' . self::ACTION_LIST;

    // Permisos específicos para AssignedProduct
    public const ASSIGNED_PRODUCT_CREATE = self::MODEL_ASSIGNED_PRODUCT . '.' . self::ACTION_CREATE;
    public const ASSIGNED_PRODUCT_VIEW = self::MODEL_ASSIGNED_PRODUCT . '.' . self::ACTION_VIEW;
    public const ASSIGNED_PRODUCT_UPDATE = self::MODEL_ASSIGNED_PRODUCT . '.' . self::ACTION_UPDATE;
    public const ASSIGNED_PRODUCT_DELETE = self::MODEL_ASSIGNED_PRODUCT . '.' . self::ACTION_DELETE;
    public const ASSIGNED_PRODUCT_LIST = self::MODEL_ASSIGNED_PRODUCT . '.' . self::ACTION_LIST;

    // Permisos específicos para Bill
    public const BILL_CREATE = self::MODEL_BILL . '.' . self::ACTION_CREATE;
    public const BILL_VIEW = self::MODEL_BILL . '.' . self::ACTION_VIEW;
    public const BILL_UPDATE = self::MODEL_BILL . '.' . self::ACTION_UPDATE;
    public const BILL_DELETE = self::MODEL_BILL . '.' . self::ACTION_DELETE;
    public const BILL_LIST = self::MODEL_BILL . '.' . self::ACTION_LIST;

    // Permisos específicos para Branch
    public const BRANCH_CREATE = self::MODEL_BRANCH . '.' . self::ACTION_CREATE;
    public const BRANCH_VIEW = self::MODEL_BRANCH . '.' . self::ACTION_VIEW;
    public const BRANCH_UPDATE = self::MODEL_BRANCH . '.' . self::ACTION_UPDATE;
    public const BRANCH_DELETE = self::MODEL_BRANCH . '.' . self::ACTION_DELETE;
    public const BRANCH_LIST = self::MODEL_BRANCH . '.' . self::ACTION_LIST;

    // Permisos específicos para Category
    public const CATEGORY_CREATE = self::MODEL_CATEGORY . '.' . self::ACTION_CREATE;
    public const CATEGORY_VIEW = self::MODEL_CATEGORY . '.' . self::ACTION_VIEW;
    public const CATEGORY_UPDATE = self::MODEL_CATEGORY . '.' . self::ACTION_UPDATE;
    public const CATEGORY_DELETE = self::MODEL_CATEGORY . '.' . self::ACTION_DELETE;
    public const CATEGORY_LIST = self::MODEL_CATEGORY . '.' . self::ACTION_LIST;

    // Permisos específicos para Client
    public const CLIENT_CREATE = self::MODEL_CLIENT . '.' . self::ACTION_CREATE;
    public const CLIENT_VIEW = self::MODEL_CLIENT . '.' . self::ACTION_VIEW;
    public const CLIENT_UPDATE = self::MODEL_CLIENT . '.' . self::ACTION_UPDATE;
    public const CLIENT_DELETE = self::MODEL_CLIENT . '.' . self::ACTION_DELETE;
    public const CLIENT_LIST = self::MODEL_CLIENT . '.' . self::ACTION_LIST;

    // Permisos específicos para ClientVisitDay
    public const CLIENT_VISIT_DAY_CREATE = self::MODEL_CLIENT_VISIT_DAY . '.' . self::ACTION_CREATE;
    public const CLIENT_VISIT_DAY_VIEW = self::MODEL_CLIENT_VISIT_DAY . '.' . self::ACTION_VIEW;
    public const CLIENT_VISIT_DAY_UPDATE = self::MODEL_CLIENT_VISIT_DAY . '.' . self::ACTION_UPDATE;
    public const CLIENT_VISIT_DAY_DELETE = self::MODEL_CLIENT_VISIT_DAY . '.' . self::ACTION_DELETE;
    public const CLIENT_VISIT_DAY_LIST = self::MODEL_CLIENT_VISIT_DAY . '.' . self::ACTION_LIST;

    // Permisos específicos para DailySalesReconciliation
    public const DAILY_SALES_RECONCILIATION_CREATE = self::MODEL_DAILY_SALES_RECONCILIATION . '.' . self::ACTION_CREATE;
    public const DAILY_SALES_RECONCILIATION_VIEW = self::MODEL_DAILY_SALES_RECONCILIATION . '.' . self::ACTION_VIEW;
    public const DAILY_SALES_RECONCILIATION_UPDATE = self::MODEL_DAILY_SALES_RECONCILIATION . '.' . self::ACTION_UPDATE;
    public const DAILY_SALES_RECONCILIATION_DELETE = self::MODEL_DAILY_SALES_RECONCILIATION . '.' . self::ACTION_DELETE;
    public const DAILY_SALES_RECONCILIATION_LIST = self::MODEL_DAILY_SALES_RECONCILIATION . '.' . self::ACTION_LIST;

    // Permisos específicos para Deposit
    public const DEPOSIT_CREATE = self::MODEL_DEPOSIT . '.' . self::ACTION_CREATE;
    public const DEPOSIT_VIEW = self::MODEL_DEPOSIT . '.' . self::ACTION_VIEW;
    public const DEPOSIT_UPDATE = self::MODEL_DEPOSIT . '.' . self::ACTION_UPDATE;
    public const DEPOSIT_DELETE = self::MODEL_DEPOSIT . '.' . self::ACTION_DELETE;
    public const DEPOSIT_LIST = self::MODEL_DEPOSIT . '.' . self::ACTION_LIST;

    // Permisos específicos para DetailAssignedProduct
    public const DETAIL_ASSIGNED_PRODUCT_CREATE = self::MODEL_DETAIL_ASSIGNED_PRODUCT . '.' . self::ACTION_CREATE;
    public const DETAIL_ASSIGNED_PRODUCT_VIEW = self::MODEL_DETAIL_ASSIGNED_PRODUCT . '.' . self::ACTION_VIEW;
    public const DETAIL_ASSIGNED_PRODUCT_UPDATE = self::MODEL_DETAIL_ASSIGNED_PRODUCT . '.' . self::ACTION_UPDATE;
    public const DETAIL_ASSIGNED_PRODUCT_DELETE = self::MODEL_DETAIL_ASSIGNED_PRODUCT . '.' . self::ACTION_DELETE;
    public const DETAIL_ASSIGNED_PRODUCT_LIST = self::MODEL_DETAIL_ASSIGNED_PRODUCT . '.' . self::ACTION_LIST;

    // Permisos específicos para Employee
    public const EMPLOYEE_CREATE = self::MODEL_EMPLOYEE . '.' . self::ACTION_CREATE;
    public const EMPLOYEE_VIEW = self::MODEL_EMPLOYEE . '.' . self::ACTION_VIEW;
    public const EMPLOYEE_UPDATE = self::MODEL_EMPLOYEE . '.' . self::ACTION_UPDATE;
    public const EMPLOYEE_DELETE = self::MODEL_EMPLOYEE . '.' . self::ACTION_DELETE;
    public const EMPLOYEE_LIST = self::MODEL_EMPLOYEE . '.' . self::ACTION_LIST;

    // Permisos específicos para FinishedProductInventory
    public const FINISHED_PRODUCT_INVENTORY_CREATE = self::MODEL_FINISHED_PRODUCT_INVENTORY . '.' . self::ACTION_CREATE;
    public const FINISHED_PRODUCT_INVENTORY_VIEW = self::MODEL_FINISHED_PRODUCT_INVENTORY . '.' . self::ACTION_VIEW;
    public const FINISHED_PRODUCT_INVENTORY_UPDATE = self::MODEL_FINISHED_PRODUCT_INVENTORY . '.' . self::ACTION_UPDATE;
    public const FINISHED_PRODUCT_INVENTORY_DELETE = self::MODEL_FINISHED_PRODUCT_INVENTORY . '.' . self::ACTION_DELETE;
    public const FINISHED_PRODUCT_INVENTORY_LIST = self::MODEL_FINISHED_PRODUCT_INVENTORY . '.' . self::ACTION_LIST;

    // Permisos específicos para InvoicesSeries
    public const INVOICES_SERIES_CREATE = self::MODEL_INVOICES_SERIES . '.' . self::ACTION_CREATE;
    public const INVOICES_SERIES_VIEW = self::MODEL_INVOICES_SERIES . '.' . self::ACTION_VIEW;
    public const INVOICES_SERIES_UPDATE = self::MODEL_INVOICES_SERIES . '.' . self::ACTION_UPDATE;
    public const INVOICES_SERIES_DELETE = self::MODEL_INVOICES_SERIES . '.' . self::ACTION_DELETE;
    public const INVOICES_SERIES_LIST = self::MODEL_INVOICES_SERIES . '.' . self::ACTION_LIST;

    // Permisos específicos para Location
    public const LOCATION_CREATE = self::MODEL_LOCATION . '.' . self::ACTION_CREATE;
    public const LOCATION_VIEW = self::MODEL_LOCATION . '.' . self::ACTION_VIEW;
    public const LOCATION_UPDATE = self::MODEL_LOCATION . '.' . self::ACTION_UPDATE;
    public const LOCATION_DELETE = self::MODEL_LOCATION . '.' . self::ACTION_DELETE;
    public const LOCATION_LIST = self::MODEL_LOCATION . '.' . self::ACTION_LIST;

    // Permisos específicos para ManagementInventory
    public const MANAGEMENT_INVENTORY_CREATE = self::MODEL_MANAGEMENT_INVENTORY . '.' . self::ACTION_CREATE;
    public const MANAGEMENT_INVENTORY_VIEW = self::MODEL_MANAGEMENT_INVENTORY . '.' . self::ACTION_VIEW;
    public const MANAGEMENT_INVENTORY_UPDATE = self::MODEL_MANAGEMENT_INVENTORY . '.' . self::ACTION_UPDATE;
    public const MANAGEMENT_INVENTORY_DELETE = self::MODEL_MANAGEMENT_INVENTORY . '.' . self::ACTION_DELETE;
    public const MANAGEMENT_INVENTORY_LIST = self::MODEL_MANAGEMENT_INVENTORY . '.' . self::ACTION_LIST;

    // Permisos específicos para Payment
    public const PAYMENT_CREATE = self::MODEL_PAYMENT . '.' . self::ACTION_CREATE;
    public const PAYMENT_VIEW = self::MODEL_PAYMENT . '.' . self::ACTION_VIEW;
    public const PAYMENT_UPDATE = self::MODEL_PAYMENT . '.' . self::ACTION_UPDATE;
    public const PAYMENT_DELETE = self::MODEL_PAYMENT . '.' . self::ACTION_DELETE;
    public const PAYMENT_LIST = self::MODEL_PAYMENT . '.' . self::ACTION_LIST;

    // Permisos específicos para Product
    public const PRODUCT_CREATE = self::MODEL_PRODUCT . '.' . self::ACTION_CREATE;
    public const PRODUCT_VIEW = self::MODEL_PRODUCT . '.' . self::ACTION_VIEW;
    public const PRODUCT_UPDATE = self::MODEL_PRODUCT . '.' . self::ACTION_UPDATE;
    public const PRODUCT_DELETE = self::MODEL_PRODUCT . '.' . self::ACTION_DELETE;
    public const PRODUCT_LIST = self::MODEL_PRODUCT . '.' . self::ACTION_LIST;

    // Permisos específicos para ProductPrice
    public const PRODUCT_PRICE_CREATE = self::MODEL_PRODUCT_PRICE . '.' . self::ACTION_CREATE;
    public const PRODUCT_PRICE_VIEW = self::MODEL_PRODUCT_PRICE . '.' . self::ACTION_VIEW;
    public const PRODUCT_PRICE_UPDATE = self::MODEL_PRODUCT_PRICE . '.' . self::ACTION_UPDATE;
    public const PRODUCT_PRICE_DELETE = self::MODEL_PRODUCT_PRICE . '.' . self::ACTION_DELETE;
    public const PRODUCT_PRICE_LIST = self::MODEL_PRODUCT_PRICE . '.' . self::ACTION_LIST;

    // Permisos específicos para ProductReturn
    public const PRODUCT_RETURN_CREATE = self::MODEL_PRODUCT_RETURN . '.' . self::ACTION_CREATE;
    public const PRODUCT_RETURN_VIEW = self::MODEL_PRODUCT_RETURN . '.' . self::ACTION_VIEW;
    public const PRODUCT_RETURN_UPDATE = self::MODEL_PRODUCT_RETURN . '.' . self::ACTION_UPDATE;
    public const PRODUCT_RETURN_DELETE = self::MODEL_PRODUCT_RETURN . '.' . self::ACTION_DELETE;
    public const PRODUCT_RETURN_LIST = self::MODEL_PRODUCT_RETURN . '.' . self::ACTION_LIST;

    // Permisos específicos para ProductUnit
    public const PRODUCT_UNIT_CREATE = self::MODEL_PRODUCT_UNIT . '.' . self::ACTION_CREATE;
    public const PRODUCT_UNIT_VIEW = self::MODEL_PRODUCT_UNIT . '.' . self::ACTION_VIEW;
    public const PRODUCT_UNIT_UPDATE = self::MODEL_PRODUCT_UNIT . '.' . self::ACTION_UPDATE;
    public const PRODUCT_UNIT_DELETE = self::MODEL_PRODUCT_UNIT . '.' . self::ACTION_DELETE;
    public const PRODUCT_UNIT_LIST = self::MODEL_PRODUCT_UNIT . '.' . self::ACTION_LIST;

    // Permisos específicos para RawMaterialsInventory
    public const RAW_MATERIALS_INVENTORY_CREATE = self::MODEL_RAW_MATERIALS_INVENTORY . '.' . self::ACTION_CREATE;
    public const RAW_MATERIALS_INVENTORY_VIEW = self::MODEL_RAW_MATERIALS_INVENTORY . '.' . self::ACTION_VIEW;
    public const RAW_MATERIALS_INVENTORY_UPDATE = self::MODEL_RAW_MATERIALS_INVENTORY . '.' . self::ACTION_UPDATE;
    public const RAW_MATERIALS_INVENTORY_DELETE = self::MODEL_RAW_MATERIALS_INVENTORY . '.' . self::ACTION_DELETE;
    public const RAW_MATERIALS_INVENTORY_LIST = self::MODEL_RAW_MATERIALS_INVENTORY . '.' . self::ACTION_LIST;

    // Permisos específicos para ResourceMedia
    public const RESOURCE_MEDIA_CREATE = self::MODEL_RESOURCE_MEDIA . '.' . self::ACTION_CREATE;
    public const RESOURCE_MEDIA_VIEW = self::MODEL_RESOURCE_MEDIA . '.' . self::ACTION_VIEW;
    public const RESOURCE_MEDIA_UPDATE = self::MODEL_RESOURCE_MEDIA . '.' . self::ACTION_UPDATE;
    public const RESOURCE_MEDIA_DELETE = self::MODEL_RESOURCE_MEDIA . '.' . self::ACTION_DELETE;
    public const RESOURCE_MEDIA_LIST = self::MODEL_RESOURCE_MEDIA . '.' . self::ACTION_LIST;

    // Permisos específicos para Sale
    public const SALE_CREATE = self::MODEL_SALE . '.' . self::ACTION_CREATE;
    public const SALE_VIEW = self::MODEL_SALE . '.' . self::ACTION_VIEW;
    public const SALE_UPDATE = self::MODEL_SALE . '.' . self::ACTION_UPDATE;
    public const SALE_DELETE = self::MODEL_SALE . '.' . self::ACTION_DELETE;
    public const SALE_LIST = self::MODEL_SALE . '.' . self::ACTION_LIST;

    // Permisos específicos para SaleDetail
    public const SALE_DETAIL_CREATE = self::MODEL_SALE_DETAIL . '.' . self::ACTION_CREATE;
    public const SALE_DETAIL_VIEW = self::MODEL_SALE_DETAIL . '.' . self::ACTION_VIEW;
    public const SALE_DETAIL_UPDATE = self::MODEL_SALE_DETAIL . '.' . self::ACTION_UPDATE;
    public const SALE_DETAIL_DELETE = self::MODEL_SALE_DETAIL . '.' . self::ACTION_DELETE;
    public const SALE_DETAIL_LIST = self::MODEL_SALE_DETAIL . '.' . self::ACTION_LIST;

    // Permisos específicos para SaleTaxTotal
    public const SALE_TAX_TOTAL_CREATE = self::MODEL_SALE_TAX_TOTAL . '.' . self::ACTION_CREATE;
    public const SALE_TAX_TOTAL_VIEW = self::MODEL_SALE_TAX_TOTAL . '.' . self::ACTION_VIEW;
    public const SALE_TAX_TOTAL_UPDATE = self::MODEL_SALE_TAX_TOTAL . '.' . self::ACTION_UPDATE;
    public const SALE_TAX_TOTAL_DELETE = self::MODEL_SALE_TAX_TOTAL . '.' . self::ACTION_DELETE;
    public const SALE_TAX_TOTAL_LIST = self::MODEL_SALE_TAX_TOTAL . '.' . self::ACTION_LIST;

    // Permisos específicos para TaxCategory
    public const TAX_CATEGORY_CREATE = self::MODEL_TAX_CATEGORY . '.' . self::ACTION_CREATE;
    public const TAX_CATEGORY_VIEW = self::MODEL_TAX_CATEGORY . '.' . self::ACTION_VIEW;
    public const TAX_CATEGORY_UPDATE = self::MODEL_TAX_CATEGORY . '.' . self::ACTION_UPDATE;
    public const TAX_CATEGORY_DELETE = self::MODEL_TAX_CATEGORY . '.' . self::ACTION_DELETE;
    public const TAX_CATEGORY_LIST = self::MODEL_TAX_CATEGORY . '.' . self::ACTION_LIST;

    // Permisos específicos para TypePrice
    public const TYPE_PRICE_CREATE = self::MODEL_TYPE_PRICE . '.' . self::ACTION_CREATE;
    public const TYPE_PRICE_VIEW = self::MODEL_TYPE_PRICE . '.' . self::ACTION_VIEW;
    public const TYPE_PRICE_UPDATE = self::MODEL_TYPE_PRICE . '.' . self::ACTION_UPDATE;
    public const TYPE_PRICE_DELETE = self::MODEL_TYPE_PRICE . '.' . self::ACTION_DELETE;
    public const TYPE_PRICE_LIST = self::MODEL_TYPE_PRICE . '.' . self::ACTION_LIST;

    // Permisos específicos para Unit
    public const UNIT_CREATE = self::MODEL_UNIT . '.' . self::ACTION_CREATE;
    public const UNIT_VIEW = self::MODEL_UNIT . '.' . self::ACTION_VIEW;
    public const UNIT_UPDATE = self::MODEL_UNIT . '.' . self::ACTION_UPDATE;
    public const UNIT_DELETE = self::MODEL_UNIT . '.' . self::ACTION_DELETE;
    public const UNIT_LIST = self::MODEL_UNIT . '.' . self::ACTION_LIST;

    // Permisos específicos para User
    public const USER_CREATE = self::MODEL_USER . '.' . self::ACTION_CREATE;
    public const USER_VIEW = self::MODEL_USER . '.' . self::ACTION_VIEW;
    public const USER_UPDATE = self::MODEL_USER . '.' . self::ACTION_UPDATE;
    public const USER_DELETE = self::MODEL_USER . '.' . self::ACTION_DELETE;
    public const USER_LIST = self::MODEL_USER . '.' . self::ACTION_LIST;


    /**
     * Obtiene todos los permisos CRUD para un modelo específico
     */
    public static function getPermissions(string $model): array
    {
        return self::getCrudPermissions($model);
    }

    /**
     * Obtiene todos los permisos CRUD para un modelo específico
     */
    public static function getCrudPermissions(string $model): array
    {
        return [
            $model . '.' . self::ACTION_CREATE,
            $model . '.' . self::ACTION_VIEW,
            $model . '.' . self::ACTION_UPDATE,
            $model . '.' . self::ACTION_DELETE,
            $model . '.' . self::ACTION_LIST,
        ];
    }
}