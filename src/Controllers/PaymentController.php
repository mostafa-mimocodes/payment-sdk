<?php

namespace Mimocodes\Payment\Controllers;

use Mimocodes\Payment\Test;

class PaymentController
{
    public function __invoke(Test $test)
    {
        $quote = $test->justDoIt();

        return view('payment::index',compact('quote'));
    }
}
