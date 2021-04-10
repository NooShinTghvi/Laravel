<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'factorNumber', 'cart_id', 'discount_id', 'description'];
    protected $visible = ['amount', 'factorNumber', 'description', 'created_at'];
    protected $hidden = ['updated_at', 'deleted_at'];
}
