<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case CITIZEN = 'citizen';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::CITIZEN => 'Citizen',
        };
    }
}
