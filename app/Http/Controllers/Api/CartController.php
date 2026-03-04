<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cart\CartRequest;
use App\Http\Resources\Api\Cart\CartResource;
use App\Models\Cart;
use App\Services\Api\CartService;
use Exception;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        try {
            $cartData = $this->cartService->getMyCart();

            return ApiResponse::success('Data Keranjang Berhasil Diambil', [
                'items'       => CartResource::collection($cartData['items']),
                'total_items' => $cartData['total_items'],
                'total_price' => $cartData['total_price'],
            ]);
        } catch (Exception $e) {
            return ApiResponse::error('Gagal mengambil data keranjang', 500);
        }
    }

    /**
     * Menambah produk ke keranjang
     */
    public function store(CartRequest $request)
    {
        try {
            $cart = $this->cartService->addToCart($request->validated());
            
            return ApiResponse::success('Produk berhasil ditambahkan ke keranjang', new CartResource($cart));
        } catch (Exception $e) {
            // Error 400 jika stok tidak cukup, 500 jika ada masalah teknis
            $code = $e->getMessage() == "Stok tidak mencukupi." ? 400 : 500;
            return ApiResponse::error($e->getMessage(), $code);
        }
    }

    /**
     * Menghapus satu item dari keranjang
     */
    public function destroy(Cart $cart)
    {
        try {
            $this->cartService->removeFromCart($cart);
            return ApiResponse::success('Item berhasil dihapus dari keranjang');
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 403);
        }
    }
}
