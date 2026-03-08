<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_target',
        'target_nominal',
        'terkumpul',
        'foto',
        'tanggal_target',
    ];

    protected $casts = [
        'tanggal_target' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressAttribute()
    {
        if ($this->target_nominal <= 0) {
            return 0;
        }

        return min(round($this->terkumpul / $this->target_nominal * 100, 1), 100);
    }
}
