<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::REFUNDED => 'Refunded',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-300',
            self::COMPLETED => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::FAILED => 'bg-rose-100 text-rose-800 border-rose-300',
            self::REFUNDED => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
