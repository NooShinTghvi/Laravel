<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static orderBy(string $string, string $string1)
 * @method static find($news_id)
 */
class News extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'context', 'image'];
    protected $visible = ['title', 'context', 'image', 'created_at'];
    protected $hidden = ['updated_at', 'deleted_at'];
}
