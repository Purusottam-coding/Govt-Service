<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'department_id',
        'name',
        'description',
        'required_documents',
        'fee',
        'processing_days',
        'status',
    ];

    protected $casts = [
        'required_documents' => 'array',
        'fee' => 'decimal:2',
        'status' => 'boolean',
    ];

    /* ---------- Relationships ---------- */

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}