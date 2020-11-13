<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @method static where(string $string, string $string1)
 * @method static create(array $array)
 */
class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'phone', 'user_id'];

    protected $hidden = ['user_id', 'created_at', 'updated_at'];

    public static function find($contactId)
    {
    }

    public function getImagePathAttribute($value)
    {
        if (is_null($value))
            return null;
        else
            return Storage::url($value);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group', 'contact_group');
    }
}
