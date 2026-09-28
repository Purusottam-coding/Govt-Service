<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case PENDING = 'pending';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::UNDER_REVIEW => 'Under Review',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-300',
            self::UNDER_REVIEW => 'bg-blue-100 text-blue-800 border-blue-300',
            self::APPROVED => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::REJECTED => 'bg-rose-100 text-rose-800 border-rose-300',
        };
    }
}
