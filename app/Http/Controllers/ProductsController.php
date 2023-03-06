<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $response = $this->validateProducts($request);
        if (is_bool($response)) {
            Product::create($request->all());
            $response = [
                'status' => 'Success',
                'message' => 'Product created successfully',
            ];
        }
        return new JsonResponse($response, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Product
    {
        return Product::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $response = $this->validateProducts($request);

        if (is_bool($response)) {
            $product = Product::find($id);

            if (!$product) {
                $response = [
                    'status' => 'Error',
                    'message' => 'Product not found',
                ];
                return new JsonResponse($response, 404);
            }

            $product->update($request->all());
            $response = [
                'status' => 'Success',
                'message' => 'Product updated successfully',
            ];
        }

        return new JsonResponse($response, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            $response = [
                'status' => 'Error',
                'message' => 'Product not found',
            ];
            return new JsonResponse($response, 404);
        }

        $product->delete();
        return new JsonResponse([
            'status' => 'Success',
            'message' => 'Product deleted successfully',
        ], 200);
    }

    private function validateProducts(Request $request): JsonResponse|bool
    {
        $response = [
            'status' => 'Error',
            'message' => 'Validation failed',
            'errors' => [],
        ];

        $message = [
            'required' => 'El campo es requerido.',
            'string' => 'Tiene que ser un texto.',
            'max' => 'El campo no puede tener mas de :max caracteres.',
            'numeric' => 'Tiene que ser un numero.',
        ];

        $attributes = [
            'name' => 'Nombre',
            'description' => 'Descripción',
            'price' => 'Precio',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:80',
            'description' => 'required|string|max:150',
            'price' => 'required|numeric',
        ], $message, $attributes);

        if ($validator->fails()) {
            $response['errors'] = $validator->errors();
            return new JsonResponse($response, 400);
        }
        return true;
    }
}
