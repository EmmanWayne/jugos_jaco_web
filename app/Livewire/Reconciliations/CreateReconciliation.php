<?php

namespace App\Livewire\Reconciliations;

use App\Enums\ReconciliationStatusEnum;
use App\Models\DailySalesReconciliation;
use App\Models\Employee;
use App\Models\Sale;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
    
    // Collections for dropdowns
    public $employees;
    
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
    }
    
    public function updatedEmployeeId()
    {
        if ($this->employee_id) {
            $this->loadEmployeeData();
        } else {
            $this->resetData();
        }
    }
    
    protected function loadEmployeeData()
    {
        if (!$this->employee_id) {
            return;
        }
        
        $today = Carbon::today();
        
        // Load today's sales for the selected employee
        $this->sales = Sale::with(['client'])
            ->where('employee_id', $this->employee_id)
            ->whereDate('sale_date', $today)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'time' => $sale->sale_date->format('H:i'),
                    'client' => $sale->client ? $sale->client->business_name : 'Cliente General',
                    'total' => $sale->final_total,
                    'type' => $sale->payment_term === 'cash' ? 'Contado' : 'Crédito'
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
        $this->createPendingReconciliation();
    }
    
    protected function calculateTotals()
    {
        $this->total_cash_sales = collect($this->sales)
            ->where('type', 'Contado')
            ->sum('total');
            
        $this->total_credit_sales = collect($this->sales)
            ->where('type', 'Crédito')
            ->sum('total');
            
        $this->total_sales = $this->total_cash_sales + $this->total_credit_sales;
        
        $this->total_collections = collect($this->payments)
            ->sum('amount');
    }
    
    protected function createPendingReconciliation()
    {
        if (!$this->employee_id) {
            return;
        }
        
        $today = Carbon::today();
        
        // Check if reconciliation already exists for today
        $existing = DailySalesReconciliation::where('employee_id', $this->employee_id)
            ->whereDate('reconciliation_date', $today)
            ->first();
            
        if ($existing) {
            $this->current_reconciliation = $existing;
            $this->reconciliation_created = true;
            return;
        }
        
        // Create new pending reconciliation
        $this->current_reconciliation = DailySalesReconciliation::create([
            'employee_id' => $this->employee_id,
            'cashier_id' => Auth::id(),
            'branch_id' => Auth::user()->employee?->branch_id ?? 1, // Default branch
            'reconciliation_date' => $today,
            'cash_sales' => $this->total_cash_sales,
            'credit_sales' => $this->total_credit_sales,
            'total_sales' => $this->total_sales,
            'total_collections' => $this->total_collections,
            'cash_received' => 0, // Will be filled later
            'deposits_made' => 0, // Will be filled later
            'total_cash_expected' => $this->total_cash_sales + $this->total_collections,
            'cash_difference' => 0, // Will be calculated later
            'status' => ReconciliationStatusEnum::PENDING,
            'notes' => null
        ]);
        
        $this->reconciliation_created = true;
    }
    
    protected function resetData()
    {
        $this->sales = [];
        $this->payments = [];
        $this->total_cash_sales = 0.0;
        $this->total_credit_sales = 0.0;
        $this->total_sales = 0.0;
        $this->total_collections = 0.0;
        $this->reconciliation_created = false;
        $this->current_reconciliation = null;
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
    
    public function render()
    {
        return view('livewire.reconciliations.create-reconciliation');
    }
}