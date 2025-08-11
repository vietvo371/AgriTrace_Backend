<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'batch_id',
        'image_url',
        'image_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'image_type' => 'string',
    ];

    /**
     * Get the batch that owns the image.
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
