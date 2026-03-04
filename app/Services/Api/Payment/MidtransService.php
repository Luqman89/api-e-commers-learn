<?php

namespace App\Services\Api\Payment;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function getSnapToken(Order $order)
    {
        // 1. Susun Detail Barang
        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id'       => $item->product_size_id,
                'price'    => (int) $item->price,
                'quantity' => $item->quantity,
                'name'     => substr($item->productSize->product->name . ' (Size ' . $item->productSize->size . ')', 0, 50),
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) $order->total_price,
            ],
            'item_details' => $itemDetails, // Tambahkan baris ini!
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email'      => auth()->user()->email,
            ],
            // Opsional: Atur waktu kadaluarsa (misal 24 jam)
            'expiry' => [
                'unit'     => 'day',
                'duration' => 1
            ]
        ];

        return Snap::getSnapToken($params);
    }
}