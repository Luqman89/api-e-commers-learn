<?php

namespace App\Enums;

enum Status : string
{
    case PENDING    = 'PENDING';
    case PROCESSING = 'PROCESSING';
    case SUCCESS    = 'SUCCESS';
    case FAILED     = 'FAILED';
    case CANCELLED  = 'CANCELLED';
    
    public function label(): string
    {
        return match($this) {
            self::PENDING    => 'Menunggu Pembayaran',
            self::PROCESSING => 'Sedang Diproses',
            self::SUCCESS    => 'Berhasil',
            self::FAILED     => 'Gagal',
            self::CANCELLED  => 'Dibatalkan',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn($item) => [
            'value' => $item->value,
            'label' => $item->label()
        ])->values()->toArray();
    }
}