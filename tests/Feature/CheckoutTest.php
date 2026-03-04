<?php

use App\Models\Cart;
use App\Models\ProductSizes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Gunakan RefreshDatabase agar database test selalu bersih setiap kali running
uses(RefreshDatabase::class);

test('user can checkout and stock is reduced', function () {
    //1. Persiapan data 
    $user = User::factory()->create();
    $productSize = ProductSizes::factory()->create(['stock' => 10]);

    // Masukkan barang ke keranjang
    Cart::create([
        'user_id' => $user->id,
        'product_size_id' => $productSize->id,
        'quantity' => 2,
    ]);


    // 2. Jalankan Aksi (Act)
    // Berperan sebagai user yang login (Sanctum)
    $response = $this->actingAs($user)
        ->postJson('/api/checkout', ['note' => 'Test checkout']);
    
    // 3. Verifikasi Hasil (Assert)
    $response->assertStatus(200)
             ->assertJsonPath('message', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');

    // Cek apakah stok berkurang (10 - 2 = 8)
    $this->assertDatabaseHas('product_sizes', [
        'id' => $productSize->id,
        'stock' => 8
    ]);

    // Cek apakah keranjang sudah kosong
    $this->assertDatabaseEmpty('carts');
});
