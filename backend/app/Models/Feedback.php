<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'user_id',
        'service_id',
        'subject',
        'message',
        'admin_reply',
        'status',
    ];

    /* ---------- Relationships ---------- */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /* ---------- Helpers ---------- */

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'open' => 'badge-open',
            'replied' => 'badge-replied',
            'closed' => 'badge-closed',
            default => 'badge-secondary',
        };
    }
}
