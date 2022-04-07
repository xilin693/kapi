<?php

namespace app\controller\www;

use king\lib\Response;
use app\service\Captcha as CaptchaService;

class Captcha
{
    public function index()
    {
        $rs = CaptchaService::get();
        Response::sendResponseJson(200, $rs);
    }
}
