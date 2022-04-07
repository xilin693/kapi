<?php

namespace app\service;

use king\lib\Captcha as CaptchaLib;

class Captcha
{
    public static function get()
    {
        $rs = CaptchaLib::getClass()->render(false);
        return json_decode($rs, true);
    }
}
