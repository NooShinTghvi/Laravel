<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'link'];
    protected $visible = ['id', 'name', 'link'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
