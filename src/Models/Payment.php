<?php

namespace Aliwebto\EasyPayment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        "amount",
        "paid_at",
        "gateway_name",
        "gateway_transaction_id",
        "status_code",
        "transaction_id",
    ];
    protected $casts = [
        "paid_at" => "datetime"
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
