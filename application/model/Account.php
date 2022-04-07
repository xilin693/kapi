<?php

namespace app\model;

use king\Model;

class Account extends Model
{
    protected static $table = 'sys_user';
    public static $date_time = ['create_time', 'last_login_time'];
    public static $insert_time = ['create_time'];

    public static function getStatusAttr($value)
    {
        $status = [1 => '正常', -1 => '锁定'];
        return $status[$value] ?? '';
    }
}
