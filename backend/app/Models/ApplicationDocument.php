<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    protected $fillable = [
        'application_id',
        'document_name',
        'file_path',
    ];

    /* ---------- Relationships ---------- */

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
