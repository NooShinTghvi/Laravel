<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $id)
 * @method static create(array $array)
 */
class UPLQuestion extends Model
{
    use HasFactory;

    protected $table = 'u_p_l_question';
    protected $fillable = ['u_p_l_id', 'question_id', 'selected_choice', 'status'];
    protected $visible = [];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
