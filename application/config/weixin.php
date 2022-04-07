<?php

use king\lib\Env;

return [
    'wx_url_prefix' => 'https://api.weixin.qq.com/',
    'app_id' => Env::get('weixin.app_id'),
    'token' => '', //服务器验证token
    'app_secret' => Env::get('weixin.app_secret'),
    'second' => 7000 // 签名缓存时间
];