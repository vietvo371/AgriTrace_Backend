<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrAccessLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'batch_id',
        'access_time',
        'ip_address',
        'device_info',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'access_time' => 'datetime',
    ];

    /**
     * Get the batch that owns the access log.
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
