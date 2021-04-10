<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'context', 'image'];
    protected $visible = ['title', 'context', 'image', 'created_at'];
    protected $hidden = ['updated_at', 'deleted_at'];
}
