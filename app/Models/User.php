<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'mobile'];
    protected $visible = ['first_name', 'last_name', 'email', 'melli_code', 'mobile'];
    protected $hidden = ['password', 'remember_token', 'updated_at',];
    protected $casts = ['email_verified_at' => 'datetime',];

//    public function sendPasswordResetNotification($token)
//    {
//        $this->notify(new UserResetPassword($token));
//    }


    public function field(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Field::class);
    }


    public function EducationBase(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EducationBase::class);
    }

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function Carts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Cart::class);
    }

    //all exam : Both purchased and in the cart
    public function Exams(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(ExamCart::class, Cart::class);
    }


    public function Phases(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Phase::class, 'user_phase', 'phase_id', 'user_id');
    }
}
