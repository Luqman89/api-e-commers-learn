<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasUlids;

    protected $fillable = [
        'order_id', 
        'product_size_id', 
        'quantity', 
        'price'
    ];

    /**
     * Relasi balik ke Order utama
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mengetahui detail ukuran dan produk yang dibeli
     */
    public function productSize()
    {
        return $this->belongsTo(ProductSizes::class);
    }
}
