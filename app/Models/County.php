<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['name','province_id'];
    protected $visible = ['name'];
    protected $hidden = ['province_id'];

    public function Province(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('Modules\User\Entities\Province','province_id');
    }
}
