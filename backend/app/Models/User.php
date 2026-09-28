<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'profile_photo',
        'verification_otp',
        'otp_expires_at',
        'otp_last_sent_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_otp',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'otp_last_sent_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ---------- Role Helpers ---------- */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCitizen(): bool
    {
        return $this->role === 'citizen';
    }

    /* ---------- Privacy & Masking Helpers ---------- */

    public function getMaskedEmailAttribute(): string
    {
        return self::maskEmail($this->email);
    }

    public static function maskEmail(?string $email): string
    {
        if (!$email || !str_contains($email, '@')) {
            return (string) $email;
        }

        [$username, $domain] = explode('@', $email, 2);
        $len = strlen($username);

        if ($len <= 2) {
            $maskedUser = substr($username, 0, 1) . '*';
        } elseif ($len <= 5) {
            $maskedUser = substr($username, 0, 1) . str_repeat('*', $len - 2) . substr($username, -1);
        } else {
            // Keep first 5 characters (or proportional), asterisk the middle, and keep the last character
            $startLen = min(5, (int) floor($len * 0.3));
            if ($startLen < 2) {
                $startLen = 2;
            }
            $maskedCount = $len - $startLen - 1;
            $maskedUser = substr($username, 0, $startLen) . str_repeat('*', max(3, $maskedCount)) . substr($username, -1);
        }

        return $maskedUser . '@' . $domain;
    }

    /* ---------- Relationships ---------- */

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
