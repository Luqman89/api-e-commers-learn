<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\OrderRequest;
use App\Http\Resources\Api\Order\OrderResource;
use App\Models\Order;
use App\Services\Api\OrderService;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Fitur Checkout: Mengubah Keranjang menjadi Order
     */
    public function store(OrderRequest $request)
    {
        try {
            $order = $this->orderService->checkout($request->validated());
            
            return ApiResponse::success(
                'Pesanan berhasil dibuat. Silakan lakukan pembayaran.', 
                new OrderResource($order)
            );
        } catch (Exception $e) {
            // Kita kirim pesan error spesifik (misal: stok habis)
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    /**
     * Menampilkan Riwayat Pesanan User
     */
    public function index(Request $request)
    {
        try {
            // Ambil status dari input, misal: ?status=SUCCESS
            $status = $request->input('status'); 
            
            $orders = $this->orderService->getMyOrders($status);
            $resource = OrderResource::collection($orders)->response()->getData(true);
            
            return ApiResponse::success('Riwayat Pesanan Berhasil Diambil', $resource);
        } catch (Exception $e) {
            return ApiResponse::error('Gagal mengambil riwayat pesanan', 500);
        }
    }

    /**
     * Detail Pesanan Spesifik
     */
    public function showByNumber($order_number)
    {
        try {
            // Cari order berdasarkan nomor invoice dan pastikan milik user yang login
            $order = Order::with(['items.productSize.product'])
                ->where('order_number', $order_number)
                ->where('user_id', auth()->id()) 
                ->firstOrFail();

            // Mengembalikan data menggunakan Resource agar format JSON rapi
            return ApiResponse::success('Detail Pesanan Berhasil Dimuat', new OrderResource($order));
            
        } catch (Exception $e) {
            return ApiResponse::error('Pesanan tidak ditemukan', 404);
        }
    }

    public function cancel($id, OrderService $orderService)
    {
        try {
            $order = $orderService->cancel($id);
            
            return response()->json([
                'message' => 'Pesanan berhasil dibatalkan.',
                'data' => $order
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400); // Bad Request jika gagal logic (misal sudah SUCCESS tidak bisa dicancel)
        }
    }
}
