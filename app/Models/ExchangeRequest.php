<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency_give',
        'amount_give',
        'currency_get',
        'amount_get',
        'is_done',
        'fee',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
