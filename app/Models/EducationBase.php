<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationBase extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    protected $visible = ['id', 'name'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function Users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function Exams(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
