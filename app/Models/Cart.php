<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasUlids;

    protected $fillable = ['user_id', 'product_size_id', 'quantity'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function productSize() {
        return $this->belongsTo(ProductSizes::class);
    }
}
