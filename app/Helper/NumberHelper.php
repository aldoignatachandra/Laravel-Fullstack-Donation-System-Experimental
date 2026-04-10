<?php

namespace App\Helper;

use Illuminate\Support\Number;

class NumberHelper
{

    public static function formatIDR($amount): string
    {
        return Number::currency($amount, 'IDR', config('app.locale'));

    }

}
