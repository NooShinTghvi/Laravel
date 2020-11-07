<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    protected $hidden = ['user_id'];

    public function contacts()
    {
        return $this->belongsToMany('App\Models\Contact', 'contact_group');
    }
}
