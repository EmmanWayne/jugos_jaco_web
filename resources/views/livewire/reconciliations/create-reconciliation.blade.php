<div class="w-full mx-auto" x-data="{
    showEmployeeDropdown: false,
    selectedEmployee: @entangle('employee_id'),
    reconciliationCreated: @entangle('reconciliation_created')
}">
    <style scoped>
        .custom-container {
            width: 100%;
            padding-left: 15px;
            padding-right: 15px;
        }
        .custom-row {
            display: flex !important;
            flex-wrap: wrap;
            margin-left: -15px;
            margin-right: -15px;
            gap: 15px;
            align-items: flex-start;
        }
        .custom-col-6 {
            flex: 0 0 auto;
            width: calc(50% - 15px);
            padding-left: 15px;
            padding-right: 15px;
            min-height: 100px;
        }
        .custom-col-4 {
            flex: 0 0 auto;
            width: calc(33.33333333% - 15px);
            padding-left: 15px;
            padding-right: 15px;
            min-height: 100px;
        }
        .custom-bg-light {
            background-color: #f8f9fa;
        }
        @media (max-width: 768px) {
            .custom-col-6, .custom-col-4 {
                width: 100% !important;
            }
            .custom-row {
                flex-direction: column;
            }
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-contado {
            background-color: #10b981;
            color: white;
        }
        .status-credito {
            background-color: #f59e0b;
            color: white;
        }
        .status-efectivo {
            background-color: #3b82f6;
            color: white;
        }
        .status-deposito {
            background-color: #8b5cf6;
            color: white;
        }
    </style>

    <div class="fi-section-content-ctn">
        <!-- Header -->
        <div class="fi-section-header-ctn flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    📊 Cuadre Diario de Ventas
                </h2>
                <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                    Gestión y seguimiento de ventas por empleado
                </p>
            </div>
        </div>

        <div class="custom-container p-6">
            <div class="custom-row">
                <!-- Left Column - Sales by Employee -->
                <div class="custom-col-6">
                    <div class="custom-bg-light p-4 rounded-lg border border-gray-200">
                        <!-- Employee Selection -->
                        <div class="fi-section-content p-6">
                            <div class="fi-fo-field-wrp">
                                <div class="grid gap-y-2">
                                    <div class="flex items-center gap-x-3 justify-between">
                                        <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                                👤 Seleccionar Empleado
                                            </span>
                                        </label>
                                    </div>
                                    
                                    <div class="grid gap-y-2">
                                        <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 ring-gray-950/10 dark:ring-white/20 focus-within:ring-2 focus-within:ring-primary-600 dark:focus-within:ring-primary-500">
                                            <div class="relative w-full" x-data="{ open: false }">
                                                <button 
                                                    @click="open = !open"
                                                    type="button"
                                                    class="fi-select-input block w-full border-none bg-transparent py-1.5 pe-8 ps-3 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 text-left"
                                                >
                                                    <span x-show="!selectedEmployee" class="text-gray-400 dark:text-gray-500">Seleccione un empleado</span>
                                                    <span x-show="selectedEmployee" class="text-gray-950 dark:text-white">
                                                        @if($employee_id)
                                                            {{ $employees->find($employee_id)?->first_name }} {{ $employees->find($employee_id)?->last_name }}
                                                        @endif
                                                    </span>
                                                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </button>
                                                
                                                <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-gray-950/10 dark:ring-white/20 overflow-auto focus:outline-none">
                                                    @foreach($employees as $employee)
                                                        <button 
                                                            wire:click="$set('employee_id', '{{ $employee->id }}')"
                                                            @click="open = false; selectedEmployee = '{{ $employee->id }}'"
                                                            type="button"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-950 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700"
                                                        >
                                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('employee_id')
                                    <div class="fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            @if($employee_id)
                                <div class="mt-4 fi-badge inline-flex items-center gap-x-1 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-gray-50 text-gray-600 ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                                    📅 Fecha: {{ now()->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>

                        <!-- Sales Table -->
                        @if($employee_id && count($sales) > 0)
                            <div class="mb-6">
                                <div class="fi-section-content-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                                    <div class="fi-section-header-ctn flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h4 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">💰 Ventas del Día</h4>
                                        </div>
                                    </div>
                                    <div class="fi-section-content">
                                        <div class="overflow-hidden">
                                            <div class="fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10">
                                                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                                                    <thead class="fi-ta-header-ctn divide-y divide-gray-200 dark:divide-white/5">
                                                        <tr class="bg-gray-50 dark:bg-white/5">
                                                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start">
                                                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                                                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Hora</span>
                                                                </span>
                                                            </th>
                                                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start">
                                                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                                                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Cliente</span>
                                                                </span>
                                                            </th>
                                                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start">
                                                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                                                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Total</span>
                                                                </span>
                                                            </th>
                                                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start">
                                                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                                                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Tipo</span>
                                                                </span>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="fi-ta-body divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                                                        @foreach($sales as $sale)
                                                            <tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                                                    <div class="fi-ta-col-wrp px-3 py-4">
                                                                        <div class="fi-ta-text text-sm leading-6 text-gray-950 dark:text-white">
                                                                            {{ $sale['time'] }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                                                    <div class="fi-ta-col-wrp px-3 py-4">
                                                                        <div class="fi-ta-text text-sm leading-6 text-gray-950 dark:text-white">
                                                                            {{ $sale['client'] }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                                                    <div class="fi-ta-col-wrp px-3 py-4">
                                                                        <div class="fi-ta-text text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                                                            L {{ number_format($sale['total'], 2) }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                                                    <div class="fi-ta-col-wrp px-3 py-4">
                                                                        <div class="fi-badge inline-flex items-center gap-x-1 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $sale['type'] === 'Contado' ? 'bg-success-50 text-success-700 ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30' : 'bg-warning-50 text-warning-700 ring-warning-600/10 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30' }}">
                                                                            {{ $sale['type'] }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($employee_id && count($sales) === 0)
                            <div class="text-center py-8">
                                <div class="text-gray-400 text-4xl mb-2">📋</div>
                                <p class="text-gray-500">No hay ventas registradas para hoy</p>
                            </div>
                        @endif

                        <!-- Collections Table -->
                        @if($employee_id && count($payments) > 0)
                            <div class="mb-6">
                                <div class="fi-section-content-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                                    <div class="fi-section-header-ctn flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h4 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">💰 Cobros Recibidos</h4>
                                        </div>
                                    </div>
                                    <div class="fi-section-content">
                                        <div class="overflow-hidden">
                                            <div class="fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10">
                                                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                                                    <thead class="fi-ta-header-ctn divide-y divide-gray-200 dark:divide-white/5">
                                                        <tr class="bg-gray-50 dark:bg-white/5">
                                                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start">
                                                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                                                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Hora</span>
                                                                </span>
                                                            </th>
                                                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start">
                                                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                                                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Cliente</span>
                                                                </span>
                                                            </th>
                                                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start">
                                                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                                                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Monto</span>
                                                                </span>
                                                            </th>
                                                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start">
                                                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                                                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Método</span>
                                                                </span>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="fi-ta-body divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                                                        @foreach($payments as $payment)
                                                            <tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                                                    <div class="fi-ta-col-wrp px-3 py-4">
                                                                        <div class="fi-ta-text text-sm leading-6 text-gray-950 dark:text-white">
                                                                            {{ $payment['time'] }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                                                    <div class="fi-ta-col-wrp px-3 py-4">
                                                                        <div class="fi-ta-text text-sm leading-6 text-gray-950 dark:text-white">
                                                                            {{ $payment['client'] }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                                                    <div class="fi-ta-col-wrp px-3 py-4">
                                                                        <div class="fi-ta-text text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                                                            L {{ number_format($payment['amount'], 2) }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                                                    <div class="fi-ta-col-wrp px-3 py-4">
                                                                        <div class="fi-badge inline-flex items-center gap-x-1 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-primary-50 text-primary-700 ring-primary-600/10 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                                                                            {{ $payment['method'] }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($employee_id && count($payments) === 0)
                            <div class="text-center py-6">
                                <div class="text-gray-400 text-3xl mb-2">💰</div>
                                <p class="text-gray-500 text-sm">No hay cobros registrados para hoy</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column - Financial Summary -->
                <div class="custom-col-6">
                    <div class="custom-bg-light p-4 rounded-lg border border-gray-200">
                        <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                            🧮 Resumen Financiero
                        </h3>
                        
                        @if($employee_id)
                            <!-- Financial Summary -->
                            <div class="fi-section-content-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mb-6">
                                <div class="fi-section-header-ctn flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                            📊 Resumen Financiero
                                        </h3>
                                    </div>
                                </div>
                                <div class="fi-section-content p-6">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="fi-stats-card rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                                            <div class="fi-stats-card-value text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                                                L {{ number_format($total_cash_sales, 2) }}
                                            </div>
                                            <div class="fi-stats-card-label text-sm font-medium text-gray-500 dark:text-gray-400">
                                                💰 Ventas al Contado
                                            </div>
                                        </div>
                                        <div class="fi-stats-card rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                                            <div class="fi-stats-card-value text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                                                L {{ number_format($total_credit_sales, 2) }}
                                            </div>
                                            <div class="fi-stats-card-label text-sm font-medium text-gray-500 dark:text-gray-400">
                                                🏦 Ventas a Crédito
                                            </div>
                                        </div>
                                        <div class="fi-stats-card rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                                            <div class="fi-stats-card-value text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                                                L {{ number_format($total_collections, 2) }}
                                            </div>
                                            <div class="fi-stats-card-label text-sm font-medium text-gray-500 dark:text-gray-400">
                                                💵 Cobros Recibidos
                                            </div>
                                        </div>
                                        <div class="fi-stats-card rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                                            <div class="fi-stats-card-value text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                                                L {{ number_format($total_sales, 2) }}
                                            </div>
                                            <div class="fi-stats-card-label text-sm font-medium text-gray-500 dark:text-gray-400">
                                                📈 Total Ventas
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cash Management Section -->
                            <div class="fi-section-content-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mb-6">
                                <div class="fi-section-header-ctn flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                            💰 Efectivo Recibido
                                        </h3>
                                    </div>
                                </div>
                                <div class="fi-section-content p-6">
                                    <div class="fi-stats-card rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                                        <div class="fi-stats-card-value text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                                            0.00
                                        </div>
                                        <div class="fi-stats-card-label text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Monto en efectivo
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Deposits Management Section -->
                            <div class="fi-section-content-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mb-6">
                                <div class="fi-section-header-ctn flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                            🏦 Gestión de Depósitos
                                        </h3>
                                    </div>
                                </div>
                                <div class="fi-section-content p-6">
                                    <div class="space-y-4">
                                        <!-- Amount Input -->
                                        <div class="fi-fo-field-wrp">
                                            <div class="grid gap-y-2">
                                                <div class="flex items-center gap-x-3 justify-between">
                                                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                                                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                                            Monto
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 ring-gray-950/10 dark:ring-white/20">
                                                    <input type="number" step="0.01" placeholder="0.00" disabled
                                                        class="fi-input block w-full border-none bg-transparent py-1.5 px-3 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 dark:text-white dark:placeholder:text-gray-500 sm:text-sm sm:leading-6" />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bank Selection -->
                                        <div class="fi-fo-field-wrp">
                                            <div class="grid gap-y-2">
                                                <div class="flex items-center gap-x-3 justify-between">
                                                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                                                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                                            Banco
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 ring-gray-950/10 dark:ring-white/20">
                                                    <select disabled class="fi-select-input block w-full border-none bg-transparent py-1.5 pe-8 ps-3 text-base text-gray-950 transition duration-75 focus:ring-0 disabled:text-gray-500 dark:text-white sm:text-sm sm:leading-6">
                                                        <option>Nombre del banco</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reference Input -->
                                        <div class="fi-fo-field-wrp">
                                            <div class="grid gap-y-2">
                                                <div class="flex items-center gap-x-3 justify-between">
                                                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                                                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                                            Referencia
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 ring-gray-950/10 dark:ring-white/20">
                                                    <input type="text" placeholder="Número de referencia" disabled
                                                        class="fi-input block w-full border-none bg-transparent py-1.5 px-3 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 dark:text-white dark:placeholder:text-gray-500 sm:text-sm sm:leading-6" />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Add Deposit Button -->
                                        <div class="flex justify-center">
                                            <button type="button" disabled
                                                class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-color-primary opacity-50 cursor-not-allowed">
                                                <span class="fi-btn-label">➕ Agregar Depósito</span>
                                            </button>
                                        </div>

                                        <!-- Deposits Table -->
                                        <div class="fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10">
                                            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                                                <thead class="fi-ta-header-ctn divide-y divide-gray-200 dark:divide-white/5">
                                                    <tr class="bg-gray-50 dark:bg-white/5">
                                                        <th class="fi-ta-header-cell px-3 py-3.5 text-start">
                                                            <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Hora</span>
                                                        </th>
                                                        <th class="fi-ta-header-cell px-3 py-3.5 text-start">
                                                            <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Banco</span>
                                                        </th>
                                                        <th class="fi-ta-header-cell px-3 py-3.5 text-start">
                                                            <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Monto</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="fi-ta-body divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                                                    <tr class="fi-ta-row">
                                                        <td class="fi-ta-cell p-0 px-3 py-4">
                                                            <div class="fi-ta-text text-sm leading-6 text-gray-950 dark:text-white">10:00</div>
                                                        </td>
                                                        <td class="fi-ta-cell p-0 px-3 py-4">
                                                            <div class="fi-ta-text text-sm leading-6 text-gray-950 dark:text-white">Banco Nacional</div>
                                                        </td>
                                                        <td class="fi-ta-cell p-0 px-3 py-4">
                                                            <div class="fi-ta-text text-sm font-medium leading-6 text-gray-950 dark:text-white">L 1000.00</div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reconciliation Status -->
                            @if($reconciliation_created && $current_reconciliation)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">
                                                Cuadre Inicializado
                                            </h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>Se ha creado un cuadre diario con estado <strong>PENDIENTE</strong> para el empleado seleccionado.</p>
                                                <p class="mt-1">ID del Cuadre: <strong>#{{ $current_reconciliation->id }}</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-12">
                                <div class="text-gray-400 text-6xl mb-4">👤</div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Seleccione un Empleado</h3>
                                <p class="text-gray-500">Elija un empleado para ver sus ventas y cobros del día</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>