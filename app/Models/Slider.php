<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static orderBy(string $string, string $string1)
 */
class Slider extends Model
{
    use HasFactory;

    protected $fillable = ['image'];
    protected $visible = ['image', 'created_at'];
    protected $hidden = ['updated_at', 'deleted_at'];
}
