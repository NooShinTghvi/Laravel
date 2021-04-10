<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $phaseId)
 * @method static create(array $array)
 */
class UserPhase extends Model
{
    use HasFactory;

    protected $table = 'user_phase';
    protected $fillable = ['user_id', 'phase_id',];
    protected $visible = ['created_at', 'rating', 'grade', 'balance'];
    protected $hidden = ['updated_at', 'deleted_at'];
}
