<?php

namespace App\Providers;

use App\Models\AccountReceivable;
use App\Models\AssignedProduct;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Client;
use App\Models\ClientVisitDay;
use App\Models\DailySalesReconciliation;
use App\Models\Deposit;
use App\Models\DetailAssignedProduct;
use App\Models\Employee;
use App\Models\FinishedProductInventory;
use App\Models\InvoicesSeries;
use App\Models\Location;
use App\Models\ManagementInventory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductReturn;
use App\Models\ProductUnit;
use App\Models\RawMaterialsInventory;
use App\Models\ResourceMedia;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SaleTaxTotal;
use App\Models\TaxCategory;
use App\Models\TypePrice;
use App\Models\Unit;
use App\Models\User;
use App\Policies\AccountReceivablePolicy;
use App\Policies\AssignedProductPolicy;
use App\Policies\BillPolicy;
use App\Policies\BranchPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ClientPolicy;
use App\Policies\ClientVisitDayPolicy;
use App\Policies\DailySalesReconciliationPolicy;
use App\Policies\DepositPolicy;
use App\Policies\DetailAssignedProductPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\FinishedProductInventoryPolicy;
use App\Policies\InvoicesSeriesPolicy;
use App\Policies\LocationPolicy;
use App\Policies\ManagementInventoryPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProductPricePolicy;
use App\Policies\ProductReturnPolicy;
use App\Policies\ProductUnitPolicy;
use App\Policies\RawMaterialsInventoryPolicy;
use App\Policies\ResourceMediaPolicy;
use App\Policies\SalePolicy;
use App\Policies\SaleDetailPolicy;
use App\Policies\SaleTaxTotalPolicy;
use App\Policies\TaxCategoryPolicy;
use App\Policies\TypePricePolicy;
use App\Policies\UnitPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        AccountReceivable::class => AccountReceivablePolicy::class,
        AssignedProduct::class => AssignedProductPolicy::class,
        Bill::class => BillPolicy::class,
        Branch::class => BranchPolicy::class,
        Category::class => CategoryPolicy::class,
        Client::class => ClientPolicy::class,
        ClientVisitDay::class => ClientVisitDayPolicy::class,
        DailySalesReconciliation::class => DailySalesReconciliationPolicy::class,
        Deposit::class => DepositPolicy::class,
        DetailAssignedProduct::class => DetailAssignedProductPolicy::class,
        Employee::class => EmployeePolicy::class,
        FinishedProductInventory::class => FinishedProductInventoryPolicy::class,
        InvoicesSeries::class => InvoicesSeriesPolicy::class,
        Location::class => LocationPolicy::class,
        ManagementInventory::class => ManagementInventoryPolicy::class,
        Payment::class => PaymentPolicy::class,
        Product::class => ProductPolicy::class,
        ProductPrice::class => ProductPricePolicy::class,
        ProductReturn::class => ProductReturnPolicy::class,
        ProductUnit::class => ProductUnitPolicy::class,
        RawMaterialsInventory::class => RawMaterialsInventoryPolicy::class,
        ResourceMedia::class => ResourceMediaPolicy::class,
        Sale::class => SalePolicy::class,
        SaleDetail::class => SaleDetailPolicy::class,
        SaleTaxTotal::class => SaleTaxTotalPolicy::class,
        TaxCategory::class => TaxCategoryPolicy::class,
        TypePrice::class => TypePricePolicy::class,
        Unit::class => UnitPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
