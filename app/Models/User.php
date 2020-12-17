<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @method static create(array $validatedData)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = ['first_name', 'last_name', 'mobile', 'email', 'credit', 'password', 'location_id'];

    protected $hidden = ['id', 'created_at', 'updated_at', 'password', 'remember_token', 'location_id'];

    protected $casts = ['email_verified_at' => 'datetime',];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class)->where('is_payed', '=', false);
    }
}
