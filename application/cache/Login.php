<?php

namespace app\cache;

use king\lib\Cache;

class Login extends Cache
{
    private static $expire = 259200;
    protected static $connection = 'cache.redis';

    public static function setToken($token, $id)
    {
        return parent::set($token, $id, self::$expire);
    }

    public static function checkToken($token)
    {
        $id = self::getId($token);
        if ($id) {
            self::expireToken($token);
            return true;
        } else {
            return false;
        }
    }

    public static function getId($token)
    {
        return parent::get($token);
    }

    public static function expireToken($token, $expire = '')
    {
        return parent::expire($token, ($expire ?:self::$expire));
    }
}