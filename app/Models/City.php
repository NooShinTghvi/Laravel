<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['name', 'province_id', 'county_id'];
    protected $visible = ['name'];
    protected $hidden = ['province_id', 'county_id'];

    public function Province(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('Modules\User\Entities\Province', 'province_id');
    }

    public function County(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('Modules\User\Entities\County', 'county_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }
}
