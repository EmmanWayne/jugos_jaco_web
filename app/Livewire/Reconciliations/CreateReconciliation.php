<?php

namespace App\Livewire\Reconciliations;

use App\Enums\BankEnum;
use App\Enums\ReconciliationStatusEnum;
use App\Enums\PaymentTermEnum;
use App\Enums\PaymentTypeEnum;
use App\Models\DailySalesReconciliation;
use App\Models\Deposit;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateReconciliation extends Component
{
    // Form properties
    public ?string $employee_id = null;
    public ?string $reconciliation_date = null;
    
    // Sales data
    public array $sales = [];
    public array $payments = [];
    
    // Totals
    public float $total_cash_sales = 0.0;
    public float $total_credit_sales = 0.0;
    public float $total_sales = 0.0;
    public float $total_collections = 0.0;
    public float $total_cash_collections = 0.0;
    public float $total_deposit_collections = 0.0;
    public float $total_cash_expected = 0.0;
    public float $total_deposit_expected = 0.0;
    public float $total_deposit_sales = 0.0;
    public float $cash_received = 0.0;
    public float $cash_difference = 0.0;
    public float $deposits_made = 0.0;
    public float $deposit_difference = 0.0;
    
    // Deposit form properties
    public float $deposit_amount = 0.0;
    public ?string $deposit_bank = null;
    public ?string $deposit_reference = null;
    public array $deposits = [];
    
    // Collections for dropdowns
    public $employees;
    public $banks;
    
    // State
    public bool $reconciliation_created = false;
    public ?DailySalesReconciliation $current_reconciliation = null;
    
    protected $rules = [
        'employee_id' => 'required|exists:employees,id',
    ];
    
    protected $messages = [
        'employee_id.required' => 'Debe seleccionar un empleado.',
        'employee_id.exists' => 'El empleado seleccionado no es válido.',
    ];

    public function mount(): void
    {
        $this->employees = Employee::orderBy('first_name')->get();
        $this->reconciliation_date = now()->format('Y-m-d');
        $this->banks = BankEnum::options();
        $this->loadDeposits();
    }
    
    public function updatedEmployeeId()
    {
        if ($this->employee_id) {
            // Solo cargamos los datos del empleado pero no creamos el cuadre automáticamente
            $this->loadEmployeeDataOnly();
        } else {
            $this->resetData();
        }
    }
    
    // Método para cargar solo los datos del empleado sin crear el cuadre
    protected function loadEmployeeDataOnly()
    {
        if (!$this->employee_id) {
            return;
        }
        
        $today = Carbon::today();
        
        // Load today's sales for the selected employee
        $this->sales = Sale::with(['client'])
            ->where('employee_id', $this->employee_id)
            ->toDay()
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'time' => $sale->sale_date->format('H:i'),
                    'client' => $sale->client ? $sale->client->business_name : 'Cliente General',
                    'subtotal' => $sale->subtotal,
                    'total' => $sale->total_amount,
                    'type' => $sale->payment_term->getLabel(),
                    'payment_method' => $sale->payment_method->getLabel(),
                ];
            })->toArray();
        
        // Load today's payments/collections for the selected employee
        $this->payments = Payment::with(['model.sale.client'])
            ->where('model_type', 'App\\Models\\AccountReceivable')
            ->whereHas('model.sale', function ($query) {
                $query->where('employee_id', $this->employee_id);
            })
            ->whereDate('payment_date', $today)
            ->get()
            ->map(function ($payment) {
                $accountReceivable = $payment->model;
                return [
                    'id' => $payment->id,
                    'time' => $payment->payment_date->format('H:i'),
                    'client' => $accountReceivable->sale->client->business_name ?? 'Cliente General',
                    'amount' => $payment->amount,
                    'method' => $this->getPaymentMethodLabel($payment->payment_method)
                ];
            })->toArray();
        
        $this->calculateTotals();
        
        // Verificar si ya existe un cuadre para hoy
        $today = Carbon::today();
        $existing = DailySalesReconciliation::getForEmployeeAndDate($this->employee_id, $today);
            
        if ($existing) {
            $this->current_reconciliation = $existing;
            $this->reconciliation_created = true;
            $this->loadDeposits();
        }
    }
    
    // Método completo que carga datos y crea el cuadre
    protected function loadEmployeeData()
    {
        $this->loadEmployeeDataOnly();
        
        if (!$this->reconciliation_created) {
            $this->createPendingReconciliation();
        }
    }
    
    protected function calculateTotals()
    {
        $this->total_cash_sales = collect($this->sales)
            ->where('type', PaymentTermEnum::CASH->getLabel())
            ->sum('total');
            
        $this->total_credit_sales = collect($this->sales)
            ->where('type', PaymentTermEnum::CREDIT->getLabel())
            ->sum('total');
            
        $this->total_sales = $this->total_cash_sales + $this->total_credit_sales;
        
        // Calcular cobros desglosados por método de pago
        $this->total_cash_collections = collect($this->payments)
            ->where('method', PaymentTypeEnum::CASH->getLabel())
            ->sum('amount');
            
        $this->total_deposit_collections = collect($this->payments)
            ->where('method', PaymentTypeEnum::DEPOSIT->getLabel())
            ->sum('amount');
            
        $this->total_collections = collect($this->payments)
            ->sum('amount');
            
        // Calcular ventas pagadas con depósitos
        $this->total_deposit_sales = collect($this->sales)
            ->where('type', PaymentTermEnum::CASH->getLabel())
            ->where('payment_method', PaymentTypeEnum::DEPOSIT->getLabel())
            ->sum('total');
            
        // Calcular efectivo esperado (solo ventas al contado con método de pago en efectivo + cobros en efectivo)
        $cash_only_sales = collect($this->sales)
            ->where('type', PaymentTermEnum::CASH->getLabel())
            ->where('payment_method', PaymentTypeEnum::CASH->getLabel())
            ->sum('total');
            
        $this->total_cash_expected = $cash_only_sales + $this->total_cash_collections;
        
        // Calcular depósitos esperados (ventas pagadas con depósitos + cobros en depósitos)
        $this->total_deposit_expected = $this->total_deposit_sales + $this->total_deposit_collections;
        
        // Calcular diferencia de efectivo (efectivo recibido - efectivo esperado)
        $this->calculateCashDifference();
        
        // Calcular total de depósitos realizados y diferencia de depósitos
        $this->calculateDepositTotals();
    }
    
    // Método para iniciar el cuadre (llamado desde el botón)
    public function initializeReconciliation()
    {
        if (!$this->employee_id) {
            session()->flash('error', 'Debe seleccionar un empleado para inicializar el cuadre.');
            return;
        }

        $this->current_reconciliation = $this->createPendingReconciliation();
        $this->loadDeposits();

        session()->flash('success', 'Cuadre inicializado correctamente.');
    }
    
    protected function createPendingReconciliation()
    {
        if (!$this->employee_id) {
            return null;
        }
        
        $today = Carbon::today();
        
        // Check if reconciliation already exists for today
        $existing = DailySalesReconciliation::getForEmployeeAndDate($this->employee_id, $today);
            
        if ($existing) {
            $this->current_reconciliation = $existing;
            $this->reconciliation_created = true;
            return $existing;
        }
        
        // Calcular el efectivo esperado y la diferencia de efectivo
        $this->calculateCashDifference();
        
        // Calcular cash_sales (ventas al contado en efectivo, excluyendo depósitos)
        $cash_sales = $this->total_cash_sales - $this->total_deposit_sales;
        
        try {
            // Create new pending reconciliation
            $reconciliation = DailySalesReconciliation::create([
                'employee_id' => $this->employee_id,
                'cashier_id' => Auth::id(),
                'branch_id' => Auth::user()->employee?->branch_id ?? 1, // Default branch
                'reconciliation_date' => $today,
                'total_cash_sales' => $this->total_cash_sales,
                'cash_sales' => $cash_sales,
                'total_credit_sales' => $this->total_credit_sales,
                'deposit_sales' => $this->total_deposit_sales,
                'total_sales' => $this->total_sales,
                'cash_collections' => $this->total_cash_collections,
                'deposit_collections' => $this->total_deposit_collections,
                'total_collections' => $this->total_collections,
                'total_cash_received' => $this->cash_received,
                'total_deposits' => 0, // Will be filled later
                'total_cash_expected' => $this->total_cash_expected,
                'total_deposit_expected' => $this->total_deposit_expected,
                'cash_difference' => $this->cash_difference,
                'status' => ReconciliationStatusEnum::PENDING,
                'notes' => null
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate entry error
            if ($e->getCode() === '23000') {
                // Check if it's our unique constraint
                if (str_contains($e->getMessage(), 'unique_employee_date_reconciliation')) {
                    session()->flash('error', 'Ya existe un cuadre para este empleado en la fecha seleccionada.');
                    return null;
                }
            }
            
            // Re-throw other database errors
            throw $e;
        }
        
        $this->current_reconciliation = $reconciliation;
        $this->reconciliation_created = true;
        
        return $reconciliation;
    }
    
    protected function resetData()
    {
        $this->sales = [];
        $this->payments = [];
        $this->total_cash_sales = 0.0;
        $this->total_credit_sales = 0.0;
        $this->total_deposit_sales = 0.0;
        $this->total_sales = 0.0;
        $this->total_collections = 0.0;
        $this->total_cash_collections = 0.0;
        $this->total_deposit_collections = 0.0;
        $this->total_cash_expected = 0.0;
        $this->total_deposit_expected = 0.0;
        $this->cash_received = 0.0;
        $this->cash_difference = 0.0;
        $this->reconciliation_created = false;
        $this->current_reconciliation = null;
        $this->deposits = [];
        $this->resetDepositForm();
    }
    
    protected function getPaymentMethodLabel($paymentMethod): string
    {
        return match($paymentMethod) {
            'cash' => 'Efectivo',
            'deposit' => 'Depósito',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
            default => 'Otro'
        };
    }
    
    // Método para actualizar el efectivo recibido
    public function updateCashReceived($value)
    {
        $this->cash_received = floatval($value);
        $this->calculateCashDifference();
        
        if ($this->current_reconciliation) {
            $this->current_reconciliation->update([
                'total_cash_received' => $this->cash_received,
                'cash_difference' => $this->cash_difference
            ]);
        }
    }
    
    // Método para calcular la diferencia de efectivo
    protected function calculateCashDifference()
    {
        $this->cash_difference = $this->cash_received - $this->total_cash_expected;
    }
    
    protected function calculateDepositTotals()
    {
        // Calcular el total de depósitos realizados
        $this->deposits_made = collect($this->deposits)->sum('amount');
        
        // Calcular la diferencia entre depósitos realizados y esperados
        $this->deposit_difference = $this->deposits_made - $this->total_deposit_expected;
    }
    
    // Método para guardar el cuadre (llamado desde el botón)
    public function saveReconciliation()
    {
        // Validar que se haya seleccionado un empleado
        if (empty($this->employee_id)) {
            session()->flash('error', 'Debe seleccionar un empleado para crear el cuadre');
            return;
        }
        
        // Validar que se haya ingresado el efectivo recibido
        if ($this->cash_received <= 0) {
            session()->flash('error', 'Debe ingresar el efectivo recibido');
            return;
        }
        
        if (!$this->current_reconciliation) {
            // Si no hay un cuadre inicializado, lo creamos primero
            $this->createPendingReconciliation();
        }
        
        if ($this->current_reconciliation) {
            // Iniciar una transacción para asegurar que todas las operaciones se realicen de forma atómica
            DB::beginTransaction();
            
            try {
                // Calcular cash_sales (ventas en efectivo sin depósitos)
                $cash_sales = $this->total_cash_sales - $this->total_deposit_sales;
                
                // Actualizar el estado del cuadre a COMPLETED
                $this->current_reconciliation->update([
                    'status' => ReconciliationStatusEnum::COMPLETED,
                    'total_cash_received' => $this->cash_received,
                    'total_cash_expected' => $this->total_cash_expected,
                    'cash_difference' => $this->cash_difference,
                    'total_deposits' => $this->deposits_made,
                    'total_deposit_expected' => $this->total_deposit_expected,
                    'deposit_difference' => $this->deposit_difference,
                    'total_cash_sales' => $this->total_cash_sales,
                    'cash_sales' => $cash_sales,
                    'total_credit_sales' => $this->total_credit_sales,
                    'deposit_sales' => $this->total_deposit_sales,
                    'total_sales' => $this->total_sales,
                    'cash_collections' => $this->total_cash_collections,
                    'deposit_collections' => $this->total_deposit_collections,
                    'total_collections' => $this->total_collections
                ]);
                
                // Recargar el cuadre para tener los datos actualizados
                $this->current_reconciliation->refresh();
                
                // Confirmar la transacción
                DB::commit();
                
                // Mostrar mensaje de éxito
                session()->flash('message', 'Cuadre guardado correctamente');
                
                // Redireccionar a la lista de cuadres
                $this->redirect(\App\Filament\Resources\DailySalesReconciliationResource::getUrl('index'));
            } catch (\Exception $e) {
                // Si ocurre algún error, revertir la transacción
                DB::rollBack();
                
                // Mostrar mensaje de error
                session()->flash('error', 'Error al guardar el cuadre: ' . $e->getMessage());
            }
        }
    }
    
    // Método para cargar los depósitos existentes
    protected function loadDeposits()
    {
        if ($this->current_reconciliation) {
            $this->deposits = Deposit::where('model_id', $this->current_reconciliation->id)
                ->get()
                ->map(function ($deposit) {
                    return [
                        'id' => $deposit->id,
                        'amount' => $deposit->amount,
                        'bank' => $deposit->bank->value,
                        'reference_number' => $deposit->reference_number,
                        'description' => "Déposito generado en venta",
                    ];
                })->toArray();
            
            // Actualizar el total de depósitos realizados y calcular la diferencia
            $this->calculateDepositTotals();
        } else {
            $this->deposits = [];
            $this->deposits_made = 0.0;
            $this->deposit_difference = 0.0;
        }
    }
    
    // Método para guardar un nuevo depósito
    public function saveDeposit()
    {
        $this->validate([
            'deposit_amount' => 'required|numeric|min:0.01',
            'deposit_bank' => 'required|string',
            'deposit_reference' => 'required|string',
        ], [
            'deposit_amount.required' => 'El monto es requerido',
            'deposit_amount.numeric' => 'El monto debe ser un número',
            'deposit_amount.min' => 'El monto debe ser mayor a 0',
            'deposit_bank.required' => 'El banco es requerido',
            'deposit_reference.required' => 'La referencia es requerida',
        ]);
        
        // Si no hay un cuadre inicializado, lo creamos primero
        if (!$this->current_reconciliation) {
            $this->createPendingReconciliation();
        }
        
        // Iniciar una transacción para asegurar que todas las operaciones se realicen de forma atómica
        DB::beginTransaction();
        
        try {
            // Crear el depósito asociado al cuadre actual
            Deposit::create([
                'amount' => $this->deposit_amount,
                'bank' => $this->deposit_bank,
                'reference_number' => $this->deposit_reference,
                'model_id' => $this->current_reconciliation->id,
                'branch_id' => Auth::user()->employee?->branch_id ?? 1,
            ]);
            
            // Actualizar el total de depósitos en el cuadre
            $total_deposits = Deposit::where('model_id', $this->current_reconciliation->id)->sum('amount');
            $this->deposits_made = $total_deposits;
            $this->deposit_difference = $this->deposits_made - $this->total_deposit_expected;
            
            $this->current_reconciliation->update([
                'total_deposits' => $total_deposits,
                'deposit_difference' => $this->deposit_difference
            ]);
            
            // Confirmar la transacción
            DB::commit();
            
            // Limpiar los campos del formulario de depósito
            $this->resetDepositForm();
            
            // Mostrar mensaje de éxito
            session()->flash('deposit_message', 'Depósito guardado correctamente');
        } catch (\Exception $e) {
            // Si ocurre algún error, revertir la transacción
            DB::rollBack();
            
            // Mostrar mensaje de error
            session()->flash('deposit_error', 'Error al guardar el depósito: ' . $e->getMessage());
        }
        
        // Recargar los depósitos
        $this->loadDeposits();
    }
    
    // Método para eliminar un depósito
    public function deleteDeposit($depositId)
    {
        $deposit = Deposit::find($depositId);
        
        if ($deposit && $deposit->model_id == $this->current_reconciliation->id) {
            // Iniciar una transacción para asegurar que todas las operaciones se realicen de forma atómica
            DB::beginTransaction();
            
            try {
                // Eliminar el depósito
                $deposit->delete();
                
                // Actualizar el total de depósitos en el cuadre
                $total_deposits = Deposit::where('model_id', $this->current_reconciliation->id)->sum('amount');
                $this->deposits_made = $total_deposits;
                $this->deposit_difference = $this->deposits_made - $this->total_deposit_expected;
                
                $this->current_reconciliation->update([
                    'total_deposits' => $total_deposits,
                    'deposit_difference' => $this->deposit_difference
                ]);
                
                // Confirmar la transacción
                DB::commit();
                
                // Mostrar mensaje de éxito
                session()->flash('deposit_message', 'Depósito eliminado correctamente');
            } catch (\Exception $e) {
                // Si ocurre algún error, revertir la transacción
                DB::rollBack();
                
                // Mostrar mensaje de error
                session()->flash('deposit_error', 'Error al eliminar el depósito: ' . $e->getMessage());
            }
            
            // Recargar los depósitos
            $this->loadDeposits();
        }
    }
    
    // Método para resetear el formulario de depósito
    protected function resetDepositForm()
    {
        $this->deposit_amount = 0.0;
        $this->deposit_bank = null;
        $this->deposit_reference = null;
    }
    
    public function render()
    {
        return view('livewire.reconciliations.create-reconciliation');
    }
}