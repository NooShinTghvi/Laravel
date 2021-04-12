<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $userId)
 * @method static create(array $array)
 * @method static find($cart_id)
 */
class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'expire_date',];
    protected $hidden = ['id', 'user_id', 'is_pay', 'transaction_id', 'expire_date', 'created_at', 'updated_at', 'deleted_at',];

    public function Exams(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        /*return $this->hasManyThrough('Modules\Exam\Entities\Exam',
            'Modules\Cart\Entities\ShoppingCartDetails','cart_id', 'id', 'id', 'exam_id');*/
        return $this->belongsToMany(Exam::class, 'exam_cart');
    }

    public function Transaction(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    public function User(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
