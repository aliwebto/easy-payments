<?php

namespace Aliwebto\EasyPayment;

use Aliwebto\EasyPayment\Models\Transaction;

trait Payable
{
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'model');
    }
    public function paid(): bool
    {
        return $this->transactions()->where("paid_at","!=",null)->exists();
    }
}
