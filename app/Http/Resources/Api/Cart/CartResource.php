<?php

namespace App\Http\Resources\Api\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Mengambil data produk melalui relasi productSize
        $product     = $this->productSize->product;
        $productSize = $this->productSize;
        
        return [
            'id'               => $this->id,
            'quantity'         => $this->quantity,
            'product' => [
                'id'    => $product->id,
                'name'  => $product->name,
                'image' => $product->image ? url('storage/' . $product->image) : null,
            ],
            'size_detail' => [
                'id'   => $productSize->id,
                'size' => $productSize->size,
            ],
            'price_per_item'   => $product->price,
            'subtotal_price'   => $product->price * $this->quantity,
        ];
    }
}
