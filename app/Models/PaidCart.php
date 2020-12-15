<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaidCart extends Model
{
    use HasFactory;

    protected $fillable = ['order_date', 'state', 'cart_id', 'delivery_id'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'cart_id', 'delivery_id'];
}
