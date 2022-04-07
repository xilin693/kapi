<?php

namespace app\validate;

use king\lib\Valid;

class Login
{
    public static function check($data)
    {
        $valid = Valid::getClass($data);
        $valid->addRule('code', 'required|alphaNum');
        $valid->addRule('username', 'required|minLength,2|maxLength,20', '用户名');
        $valid->addRule('password', 'required|minLength,5', '密码');
        $valid->addRule('captcha', 'required|size,3|checkCaptcha,'. $data['code'], '验证码');
        $valid->response();
    }
}