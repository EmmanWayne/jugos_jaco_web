<?php

namespace Database\Seeders;

use App\Models\TaxCategory;
use Illuminate\Database\Seeder;

class TaxCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen registros para evitar duplicados
        if (TaxCategory::count() > 0) {
            $this->command->info('Tax categories already exist. Skipping seeder.');
            return;
        }

        $productCategories = [
            [
                'name' => 'Exento',
                'rate' => 0.00,
                'sequence_invoice' => 1,
                'type_tax_use' => 'sale',
                'is_active' => true,
                'description' => 'Productos exentos de impuesto según la ley hondureña',
                'display_type' => 'tax_source',
                'base_tax_id' => null,
                'is_for_products' => true,
                'calculation_type' => 'exempt',
            ],
            [
                'name' => 'ISV 15%',
                'rate' => 15.00,
                'sequence_invoice' => 2,
                'type_tax_use' => 'sale',
                'is_active' => true,
                'description' => 'Impuesto sobre Ventas general del 15%',
                'display_type' => 'tax_source',
                'base_tax_id' => null,
                'is_for_products' => true,
                'calculation_type' => 'tax',
            ],
            [
                'name' => 'ISV 18%',
                'rate' => 18.00,
                'sequence_invoice' => 3,
                'type_tax_use' => 'sale',
                'is_active' => true,
                'description' => 'Impuesto sobre Ventas específico del 18%',
                'display_type' => 'tax_source',
                'base_tax_id' => null,
                'is_for_products' => true,
                'calculation_type' => 'tax',
            ],
        ];

        $createdCategories = [];
        foreach ($productCategories as $category) {
            $createdCategories[] = TaxCategory::create($category);
        }

        $displayLines = [
            [
                'name' => 'Importe Exonerado',
                'rate' => 0.00,
                'sequence_invoice' => 1,
                'type_tax_use' => 'sale',
                'is_active' => true,
                'description' => 'Línea de factura para importes exonerados',
                'display_type' => 'base_display',
                'base_tax_id' => $createdCategories[0]->id, // Exento
                'is_for_products' => false,
                'calculation_type' => 'exempt',
            ],
            [
                'name' => 'Importe Exento',
                'rate' => 0.00,
                'sequence_invoice' => 2,
                'type_tax_use' => 'sale',
                'is_active' => true,
                'description' => 'Línea de factura para base imponible exenta',
                'display_type' => 'base_display',
                'base_tax_id' => $createdCategories[0]->id, // Exento
                'is_for_products' => false,
                'calculation_type' => 'exempt',
            ],
            [
                'name' => 'Importe Gravado 15%',
                'rate' => 15.00,
                'sequence_invoice' => 3,
                'type_tax_use' => 'sale',
                'is_active' => true,
                'description' => 'Línea de factura para base imponible 15%',
                'display_type' => 'base_display',
                'base_tax_id' => $createdCategories[1]->id, // ISV 15%
                'is_for_products' => false,
                'calculation_type' => 'base',
            ],
            [
                'name' => 'Importe Gravado 18%',
                'rate' => 18.00,
                'sequence_invoice' => 4,
                'type_tax_use' => 'sale',
                'is_active' => true,
                'description' => 'Línea de factura para base imponible 18%',
                'display_type' => 'base_display',
                'base_tax_id' => $createdCategories[2]->id, // ISV 18%
                'is_for_products' => false,
                'calculation_type' => 'base',
            ],
            [
                'name' => 'ISV 15%',
                'rate' => 15.00,
                'sequence_invoice' => 5,
                'type_tax_use' => 'sale',
                'is_active' => true,
                'description' => 'Línea de factura para impuesto calculado 15%',
                'display_type' => 'tax_display',
                'base_tax_id' => $createdCategories[1]->id, // ISV 15%
                'is_for_products' => false,
                'calculation_type' => 'tax',
            ],
            [
                'name' => 'ISV 18%',
                'rate' => 18.00,
                'sequence_invoice' => 6,
                'type_tax_use' => 'sale',
                'is_active' => true,
                'description' => 'Línea de factura para impuesto calculado 18%',
                'display_type' => 'tax_display',
                'base_tax_id' => $createdCategories[2]->id, // ISV 18%
                'is_for_products' => false,
                'calculation_type' => 'tax',
            ],
        ];

        foreach ($displayLines as $line) {
            TaxCategory::create($line);
        }
    }
}
