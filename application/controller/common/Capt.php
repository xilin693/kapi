<?php

namespace app\controller\common;

use king\lib\Captcha;

class Capt
{
    public function index()
    {
        $capt = Captcha::getClass();
        $capt->render(false);
    }
}