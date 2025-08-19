<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    use ApiResponse;

    private $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }
    public function getProducts(Request $request)
    {
        try {
            $clientId = $request->get('client_id');
            $search = $request->get('search', '');

            $client = Client::find($clientId) ?? throw new NotFoundHttpException('Cliente no encontrado');

            $productResult = $this->productService->getProducts(
                $search,
                $client->type_price_id,
                $client->employee_id
            );

            return $this->successResponse($productResult, 'Productos obtenidos correctamente');
            
        } catch (\Exception $e) {
            return $this->errorResponse($e, 500, 'Error al obtener productos');
        }
    }
}
