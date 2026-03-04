<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUlids, HasFactory;

    protected $fillable = ['user_id', 'order_number', 'total_price', 'status', 'note', 'snap_token'];

    // Casting status ke Enum
    protected $casts = [
        'status' => Status::class,
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }
}
