<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Http\Resources\AccountReceivableDetailResource;
use App\Http\Resources\AccountReceivableResource;
use App\Http\Resources\PaymentResource;
use App\Models\AccountReceivable;
use App\Services\AccountReceivableService;
use App\Services\PaymentService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\NullsafeMethodCall;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccountReceivableController extends Controller
{
    use ApiResponse;

    private $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService();
    }

    /**
     * Get all accountReceivable by employee
     * 
     * @return JsonResponse
     */
    public function getAccountReceivable(): JsonResponse
    {
        try {
            $accountReceivable = AccountReceivable::byEmployee(Auth::user()->employee_id)
                ->orderBy('due_date')
                ->get();

            return $this->successResponse(
                AccountReceivableResource::collection($accountReceivable),
                "Cuentas por cobrar obtenidas exitosamente."
            );
        } catch (Exception $exc) {
            return $this->errorResponse(
                $exc,
                $exc->getCode(),
                "Courrio un error al obtener las cuentas por cobrar."
            );
        }
    }

    /**
     * Get account receivable by id
     * 
     * @param int $accountReceivableId
     * @return JsonResponse
     */
    public function getAccountReceivableById(int $accountReceivableId): JsonResponse
    {
        try {
            $accountReceivable = AccountReceivable::findOrFail($accountReceivableId);

            return $this->successResponse(
                new AccountReceivableDetailResource($accountReceivable->load('payments')),
                "Pagos obtenidos exitosamente."
            );
        } catch (Exception $exc) {
            return $this->errorResponse(
                $exc,
                $exc->getCode(),
                "Ocurrió un error al obtener la cuenta por cobrar"
            );
        }
    }

    /**
     * Process a payment for an account receivable
     * * @param int $accountReceivableId
     * @param PaymentRequest $request
     * @return JsonResponse
     */
    public function processPayment(int $accountReceivableId, PaymentRequest $request): JsonResponse
    {
        try {
            $accountReceivable = AccountReceivable::findOrFail($accountReceivableId);
            $result = $this->paymentService->processPayment(
                $accountReceivable,
                [
                    'amount' => $request['amount'],
                    'payment_date' => now(),
                    'payment_method' => $request['payment_method'],
                    'notes' => $request['notes'],
                ]
            );

            return $this->successResponse(
                new PaymentResource($result['data']['payment']),
                $result['message']
            );
        } catch (Exception  $exc) {
            return $this->errorResponse(
                $exc,
                $exc->getCode(),
                "Ocurrió un error al realizar el pago."
            );
        }
    }
}
