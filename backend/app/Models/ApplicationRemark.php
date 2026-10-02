<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationRemark extends Model
{
    protected $fillable = [
        'application_id',
        'user_id',
        'sender_role',
        'sender_name',
        'message',
    ];

    /* ---------- Relationships ---------- */

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ---------- Helper Methods ---------- */

    public function isAdmin(): bool
    {
        return $this->sender_role === 'admin';
    }

    public function isCitizen(): bool
    {
        return $this->sender_role === 'citizen';
    }
}
