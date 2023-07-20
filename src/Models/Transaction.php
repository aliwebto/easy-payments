<?php

namespace Aliwebto\EasyPayment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    protected $fillable = [
        "uuid",
        "description",
        "amount",
        "specificCard",
        "model_id",
        "model_type",
        "transaction_uuid",
        "paid_at",
        "paidAmount"
    ];
    protected $casts = [
        "paid_at" => "datetime"
    ];

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
