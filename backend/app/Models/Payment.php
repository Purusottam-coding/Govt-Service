<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'application_id',
        'amount',
        'payment_method',
        'transaction_id',
        'payment_statement',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /* ---------- Relationships ---------- */

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
