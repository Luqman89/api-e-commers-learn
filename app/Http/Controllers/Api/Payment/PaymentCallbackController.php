<?php

namespace App\Http\Controllers\Api\Payment;

use App\Enums\Status;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentCallbackController extends Controller
{
    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        // Memastikan gross_amount memiliki 2 angka di belakang koma (string) agar hash cocok dengan Midtrans
        $grossAmount = number_format($request->gross_amount, 2, '.', ''); 

        $hashed = hash("sha512", $request->order_id . $request->status_code . $grossAmount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return ApiResponse::error('Invalid signature', 403);
        }

        $order = Order::with('items.productSize')->where('order_number', $request->order_id)->first();
        if (!$order) return ApiResponse::error('Order not found', 404);

        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status;

        DB::transaction(function () use ($order, $transactionStatus, $fraudStatus) {
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $order->update(['status' => Status::PENDING]);
                } else {
                    $order->update(['status' => Status::SUCCESS]);
                }
            } elseif ($transactionStatus == 'settlement') {
                $order->update(['status' => Status::SUCCESS]);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                
                // LOGIKA PENGEMBALIAN STOK
                // Kita hanya kembalikan stok jika status sebelumnya bukan SUCCESS/FAILED
                if ($order->status !== Status::SUCCESS && $order->status !== Status::FAILED) {
                    foreach ($order->items as $item) {
                        $item->productSize()->increment('stock', $item->quantity);
                    }
                }

                $order->update(['status' => Status::FAILED]);
            } elseif ($transactionStatus == 'pending') {
                $order->update(['status' => Status::PENDING]);
            }
        });

        return ApiResponse::success('Callback handled successfully');
    }
}
