<?php

namespace App\Http\Resources\Api\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_name' => $this->productSize->product->name,
            'size' => $this->productSize->size,
            'quantity' => $this->quantity,
            'price_at_purchase' => $this->price,
            'subtotal' => $this->price * $this->quantity,
        ];
    }
}
