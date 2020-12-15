<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, false $false)
 */
class Delivery extends Model
{
    use HasFactory;

    protected $fillable = ['velocity', 'is_busy', 'location_id'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'location_id'];

    public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\Location');
    }
}
