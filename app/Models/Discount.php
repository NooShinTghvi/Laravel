<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, mixed $get)
 */
class Discount extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'type', 'value', 'maximum_value', 'expire_date', 'count', 'specific_exam_enable', 'exam_id',];
    protected $visible = ['code', 'type', 'value', 'maximum_value', 'expire_date', 'count', 'used_number', 'specific_exam_enable', 'exam_id',];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['expire_date'];
    protected $dateFormat = 'Y-m-d';

    /*protected $casts = [
        'expire_date' => 'datetime:Y-m-d',
    ];*/

    public function Exams(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_discount');
    }
}
