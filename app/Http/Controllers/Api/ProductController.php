<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\ProductRequest;
use App\Http\Resources\Api\Product\ProductResource;
use App\Models\Product;
use App\Services\Api\ProductService;
use Exception;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $service)
    {
        $this->productService = $service;
    }

    public function index()
    {
        $products = $this->productService->getAll();
        $resource = ProductResource::collection($products)->response()->getData(true);

        // Memasukkan hasil resource ke dalam helper ApiResponse
        return ApiResponse::success('Data Produk Berhasil Diambil', $resource);
    }

    public function store(ProductRequest $request)
    {
        try {
            $product = $this->productService->store($request->validated());
            return ApiResponse::success('Produk Berhasil Ditambahkan', new ProductResource($product));
        } catch (Exception $e) {
            return ApiResponse::error('Gagal menambah produk: ' . $e->getMessage(), 500);
        }
    }

    public function show(Product $product)
    {
        // Load relasi category agar muncul di resource
        return ApiResponse::success('Detail Produk', new ProductResource($product->load(['category', 'sizes'])));
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {
            $updated = $this->productService->update($product, $request->validated());
            return ApiResponse::success('Produk Berhasil Diperbarui', new ProductResource($updated));
        } catch (Exception $e) {
            return ApiResponse::error('Gagal memperbarui produk: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->productService->delete($product);
            return ApiResponse::success('Produk Berhasil Dihapus');
        } catch (Exception $e) {
            return ApiResponse::error('Gagal menghapus produk', 500);
        }
    }
}
