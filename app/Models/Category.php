<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name',];
    protected $visible = ['name', 'description'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];


    public function Category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }
}
