<?php

namespace App\Services;

use App\Enums\PaymentTypeEnum;
use App\Models\AccountReceivable;
use App\Models\Sale;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class AccountReceivableService
{
	/**
	 * Crea una cuenta por cobrar única (manual o vinculada a venta).
	 * - Si se provee $sale, valida y usa el total restante (total - pagado) de la venta.
	 * - Si no se provee $sale, crea una cuenta manual con $totalAmount.
	 */
	public function create(
		?Sale $sale = null,
		?float $totalAmount = null,
		?string $name = null,
		?string $notes = null,
		null|\DateTimeInterface|CarbonInterface $dueDate = null,
		?float $amountPaidNow = null,
	): AccountReceivable {
		if ($sale) {
			$this->validateSale($sale);
			[$total, $paid, $remaining] = $this->calculateSaleAmounts($sale, $amountPaidNow);
			$effectiveDueDate = $this->resolveDueDate($dueDate, $sale);
			$finalName = $name ?: $this->buildDefaultName($sale);

			$payload = [
				'sales_id' => $sale->id,
				'name' => $finalName,
				// Importante: el dominio usa total_amount = monto a cobrar (restante)
				'total_amount' => $remaining,
				'remaining_balance' => $remaining,
				'due_date' => $effectiveDueDate,
				'notes' => $notes,
			];
		} else {
			if ($totalAmount === null || $totalAmount <= 0) {
				throw new \InvalidArgumentException('Debe proporcionar un monto total mayor que cero para la cuenta por cobrar.');
			}
			$effectiveDueDate = $this->resolveDueDate($dueDate, null);
			$finalName = $name ?: 'Cuenta por cobrar';
			$amount = round($totalAmount, 2);

			$payload = [
				'sales_id' => null,
				'name' => $finalName,
				'total_amount' => $amount,
				'remaining_balance' => $amount,
				'due_date' => $effectiveDueDate,
				'notes' => $notes,
			];
		}

		return DB::transaction(function () use ($payload) {
			return AccountReceivable::create($payload);
		});
	}

	private function buildDefaultName(Sale $sale): string
	{
		$identifier = $sale->full_invoice_number ?? ('Venta #' . $sale->id);
		return 'Cuenta por cobrar - ' . $identifier;
	}

	/**
	 * Helpers privados
	 */
	private function validateSale(Sale $sale): void
	{
		if ($sale->payment_type !== PaymentTypeEnum::CREDIT) {
			throw new \InvalidArgumentException('Solo se pueden crear cuentas por cobrar para ventas de tipo crédito.');
		}

		if ($sale->accountReceivable()->exists()) {
			throw new \RuntimeException('La venta ya tiene una cuenta por cobrar asociada.');
		}

		if (($sale->client->account_receivable_count ?? 0) == 2) {
			throw new \RuntimeException('El cliente ya tiene dos cuentas por cobrar activas.');
		}
	}

	/**
	 * Calcula montos de una venta en base al pago realizado
	 * @return array{0: float, 1: float, 2: float} [total, paid, remaining]
	 */
	private function calculateSaleAmounts(Sale $sale, ?float $amountPaidNow = null): array
	{
		$total = (float) ($sale->total_amount ?? 0);
		if ($total <= 0) {
			throw new \InvalidArgumentException('El total de la venta debe ser mayor a cero.');
		}

		$paid = $amountPaidNow;
		if ($paid === null) {
			$paid = (float) ($sale->cash_amount ?? 0);
		}

		if ($paid < 0) {
			throw new \InvalidArgumentException('El monto pagado no puede ser negativo.');
		}

		$paid = min($paid, $total);
		$remaining = round($total - $paid, 2);

		return [$total, $paid, $remaining];
	}

	private function resolveDueDate(null|\DateTimeInterface|CarbonInterface $dueDate, ?Sale $sale)
	{
		if ($dueDate) {
			return \Carbon\Carbon::parse($dueDate);
		}

		if ($sale && $sale->due_date) {
			return \Carbon\Carbon::parse($sale->due_date);
		}

		return null;
	}
}

