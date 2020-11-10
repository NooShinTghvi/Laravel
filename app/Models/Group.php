<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, int|string|null $id)
 * @method static create(array $array)
 * @method static find($groupId)
 */
class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    protected $hidden = ['user_id', 'created_at', 'updated_at'];

    public function contacts()
    {
        return $this->belongsToMany('App\Models\Contact', 'contact_group');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
