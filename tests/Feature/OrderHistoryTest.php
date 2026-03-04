<?php

use App\Models\User;
use App\Models\Order;
use App\Enums\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can only see their own order history', function () {
    // 1. Arrange: Buat 2 user dan masing-masing punya 1 order
    $userToni = User::factory()->create();
    $userAndi = User::factory()->create();

    Order::factory()->create(['user_id' => $userToni->id, 'order_number' => 'ORD-TONI']);
    Order::factory()->create(['user_id' => $userAndi->id, 'order_number' => 'ORD-ANDI']);

    // 2. Act: Login sebagai Toni dan panggil API history
    $response = $this->actingAs($userToni)
                     ->getJson('/api/orders');

    // 3. Assert: Pastikan hanya order milik Toni yang muncul
    $response->assertStatus(200)
             ->assertJsonCount(1, 'data')
             ->assertJsonPath('data.0.order_number', 'ORD-TONI');
});

test('user can filter order history by status', function () {
    $user = User::factory()->create();

    // Buat 1 order SUCCESS dan 2 order PENDING
    Order::factory()->create(['user_id' => $user->id, 'status' => 'SUCCESS']);
    Order::factory()->create(['user_id' => $user->id, 'status' => 'PENDING']);
    Order::factory()->create(['user_id' => $user->id, 'status' => 'PENDING']);

    // Request dengan filter SUCCESS
    $response = $this->actingAs($user)
                     ->getJson('/api/orders?status=SUCCESS');

    // Harus hanya mengembalikan 1 data
    $response->assertStatus(200)
             ->assertJsonCount(1, 'data');
});