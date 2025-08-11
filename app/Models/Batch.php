<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'batch_code',
        'weight',
        'variety',
        'planting_date',
        'harvest_date',
        'cultivation_method',
        'location',
        'gps_coordinates',
        'qr_code',
        'qr_expiry',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'planting_date' => 'date',
        'harvest_date' => 'date',
        'qr_expiry' => 'datetime',
        'weight' => 'float',
    ];

    /**
     * Get the user that owns the batch.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that owns the batch.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the reviews for the batch.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the images for the batch.
     */
    public function images()
    {
        return $this->hasMany(BatchImage::class);
    }

    /**
     * Get the access logs for the batch.
     */
    public function accessLogs()
    {
        return $this->hasMany(QrAccessLog::class);
    }
}
