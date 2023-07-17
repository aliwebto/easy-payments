<?php

namespace Aliwebto\EasyPayment;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Aliwebto\EasyPayment\Skeleton\SkeletonClass
 */
class EasyPaymentFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'easy-payment';
    }
}
