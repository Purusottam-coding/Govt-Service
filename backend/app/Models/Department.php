<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'phone',
        'email',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /* ---------- Relationships ---------- */

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function applications()
    {
        return $this->hasManyThrough(Application::class, Service::class);
    }
}