<?php

use king\lib\env;

return [
    'white_list' => Env::get('permission.white_list'), // 校验白名单
    'token_header' => Env::get('permission.token_header'), // token的header
    'token_salt' => Env::get('permission.token_salt'), // 生成用户token的salt
    'token_expire' => Env::get('permission.token_expire'), // token过期时间
    'captcha_header' => Env::get('permission.captcha_header'), // 验证码的header
    'password_salt' => Env::get('permission.password_salt'), // 用户密码加密的salt
    'params' => [
        'max_retry_period' => Env::get('permission.max_retry_period'), // 密码重试周期,为0不限制
        'max_retry_times' => Env::get('permission.max_retry_times') // 一个周期内最多可以重试次数,为0不限制
    ]
];