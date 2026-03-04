<?php

namespace App\Services\Api;

use App\Models\Cart;
use App\Models\Order;
use App\Enums\Status;
use App\Services\Api\Payment\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class OrderService
{
    /**
     * Proses Checkout: Mengubah Keranjang menjadi Pesanan
     */
    public function checkout(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();

            // 1. Ambil isi keranjang user
            $cartItems = Cart::with('productSize.product')
                ->where('user_id', $user->id)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new Exception("Keranjang belanja kosong.");
            }

            // 2. Hitung total harga dan validasi stok terakhir
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $productSize = $item->productSize;

                // Cek apakah stok masih mencukupi
                if ($productSize->stock < $item->quantity) {
                    throw new Exception("Stok untuk {$productSize->product->name} (Size {$productSize->size}) tidak mencukupi.");
                }

                $totalPrice += $productSize->product->price * $item->quantity;
            }

            // 3. Buat Header Pesanan (Order)
            $order = Order::create([
                'user_id'      => $user->id,
                'order_number' => $this->generateOrderNumber(),
                'total_price'  => $totalPrice,
                'status'       => Status::PENDING, // Menggunakan Enum
                'note'         => $data['note'] ?? null,
            ]);

            // 4. Pindahkan item keranjang ke Order Items & Potong Stok
            foreach ($cartItems as $item) {
                // Simpan ke detail order (Snapshot harga saat ini)
                $order->items()->create([
                    'product_size_id' => $item->product_size_id,
                    'quantity'        => $item->quantity,
                    'price'           => $item->productSize->product->price,
                ]);

                // POTONG STOK di tabel product_sizes
                $item->productSize->decrement('stock', $item->quantity);
            }

            // --- TAMBAHKAN POIN 5 DISINI ---
            // 5. Hapus semua isi keranjang user karena sudah jadi pesanan
            Cart::where('user_id', $user->id)->delete();
            // -------------------------------

            /** * POIN PENTING:
             * Load relasi DISINI agar objek $order memiliki data 'items' dan 'product' 
             * sebelum dikirim ke MidtransService.
             */
            $order->load(['items.productSize.product']);

            // 6. Panggil Midtrans Service dengan data yang sudah LENGKAP
            $midtransService = new MidtransService();
            $snapToken = $midtransService->getSnapToken($order);

            // 7. Simpan Token
            $order->update(['snap_token' => $snapToken]);

            // Return hasil akhir untuk respons API
            return $order;
        });
    }

    /**
     * Generate Nomor Invoice Unik (Contoh: INV-20260302-ABCDE)
     */
    private function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(5));
        return "INV-{$date}-{$random}";
    }

    /**
     * Ambil riwayat pesanan user dengan filter status opional
     */
    public function getMyOrders($status = null)
    {
        $query = Order::with('items.productSize.product')
            ->where('user_id', auth()->id());

        // Tambahkan filter jika parameter status dikirim
        if ($status) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate(10);
    }


    public function cancel($orderId)
{
    return DB::transaction(function () use ($orderId) {
        // 1. Cari order milik user yang sedang login
        $order = Order::where('user_id', auth()->id())
                      ->where('id', $orderId)
                      ->firstOrFail();

        // 2. Keamanan: Hanya order PENDING yang bisa dicancel
        if ($order->status !== Status::PENDING) {
            throw new Exception("Pesanan tidak dapat dibatalkan karena statusnya sudah {$order->status}.");
        }

        // 3. Kembalikan Stok (Looping items di dalam order)
        foreach ($order->items as $item) {
            $item->productSize->increment('stock', $item->quantity);
        }

        // 4. Update status order
        $order->update(['status' => Status::CANCELLED]);

        return $order;
    });
}
}