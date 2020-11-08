<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static where(string $string, $id)
 */
class ContactGroup extends Model
{
    use HasFactory;

    protected $table = 'contact_group';

    protected $fillable = ['group_id', 'contact_id'];

    protected $hidden = ['created_at', 'updated_at'];
}
