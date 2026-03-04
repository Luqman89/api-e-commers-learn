<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSizes extends Model
{
    use HasUlids, HasFactory;

    protected $table = 'product_sizes';

    protected $fillable = ['product_id', 'size', 'stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
