<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    protected $fillable = [
        'application_id',
        'document_name',
        'file_path',
        'status',
        'admin_feedback',
        'replaced_at',
    ];

    protected $casts = [
        'replaced_at' => 'datetime',
    ];

    /* ---------- Helper Methods ---------- */

    public function isReplacementNeeded(): bool
    {
        return $this->status === 'replacement_needed';
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /* ---------- Relationships ---------- */

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
