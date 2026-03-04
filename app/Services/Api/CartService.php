<?php

namespace App\Services\Api;

use App\Models\Cart;
use App\Models\ProductSizes;
use Exception;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Menambah atau memperbarui item di keranjang
     */
    public function addToCart(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();
            $size = ProductSizes::findOrFail($data['product_size_id']);

            // 1. Cek ketersediaan stok di tabel product_sizes
            if ($size->stock < $data['quantity']) {
                throw new Exception("Stok untuk ukuran {$size->size} tidak mencukupi.");
            }

            // 2. Cet apakah barang dengan ukuran yang sama sudah ada di keranjang user
            $cartItem = Cart::where('user_id', $user->id)
                ->where('product_size_id', $data['product_size_id'])
                ->first();

            if ($cartItem) {
                // Jika sudah ada, tambahkan quantity-nya
                $newQuantity = $cartItem->quantity + $data['quantity'];

                // Cek lagi apakah total quantity baru melampaui stok
                if ($size->stock < $newQuantity) {
                    throw new Exception("Total di keranjang melampaui stok yang tersedia.");
                }

                $cartItem->update(['quantity' => $newQuantity]);
                return $cartItem->load(['productSize.product']);
            }

            // 3. Jika belum ada, buat record baru
            return Cart::create([
                'user_id'         => $user->id,
                'product_size_id' => $data['product_size_id'],
                'quantity'        => $data['quantity']
            ])->load(['productSize.product']);
        });
    }

    /**
     * Mengambil semua isi keranjang milik user yang sedang login
     */
    public function getMyCart()
    {
        $carts = Cart::with(['productSize.product'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        // Hitung total harga seluruh keranjang
        $totalPrice = $carts->sum(function ($cart) {
            return $cart->productSize->product->price * $cart->quantity;
        });

        return [
            'items'       => $carts,
            'total_price' => $totalPrice,
            'total_items' => $carts->sum('quantity')
        ];
    }

    /**
     * Menghapus satu item dari keranjang
     */
    public function removeFromCart(Cart $cart)
    {
        // Pastikan user hanya bisa menghapus keranjang miliknya sendiri
        if ($cart->user_id !== auth()->id()) {
            throw new Exception("Akses ditolak.");
        }

        return $cart->delete();
    }
}