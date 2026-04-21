<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'feature',
        'provider',
        'model',
        'api_version',
        'status_code',
        'success',
        'error_type',
    ];

    protected $casts = [
        'success' => 'boolean',
        'status_code' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
