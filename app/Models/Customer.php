<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'full_name',
        'phone_number',
        'email',
        'password_hash',
        'address',
        'profile_image',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the batches for the customer.
     */
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    /**
     * Get the reviews for the customer.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
