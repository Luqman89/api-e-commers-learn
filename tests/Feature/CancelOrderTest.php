<?php

use App\Enums\Status;
use App\Models\Order;
use App\Models\ProductSizes;
use App\Models\User;

test('user can cancel pending order and stock is restored', function () {
    $user = User::factory()->create();
    $productSize = ProductSizes::factory()->create(['stock' => 10]);

    // 1. Arrange: Buat order PENDING dengan qty 2 (berarti stok sisa 8)
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => Status::PENDING
    ]);
    
    $order->items()->create([
        'product_size_id' => $productSize->id,
        'quantity' => 2,
        'price' => 100000
    ]);
    
    // Simulasikan stok awal setelah checkout (10 - 2 = 8)
    $productSize->decrement('stock', 2);

    // 2. Act: Panggil API Cancel
    $response = $this->actingAs($user)
                     ->postJson("/api/orders/{$order->id}/cancel");

    // 3. Assert: Cek status & stok kembali jadi 10
    $response->assertStatus(200);
    
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => Status::CANCELLED
    ]);

    $this->assertDatabaseHas('product_sizes', [
        'id' => $productSize->id,
        'stock' => 10 // Kembali ke asal!
    ]);
});