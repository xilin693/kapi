<?php

namespace app\model;

use king\Model;
use app\model\Account as AccountModel;

class Kpi extends Model
{
    protected static $table = 'kpi';

    public static function getAccountIdAttr($id)
    {
        $realname = AccountModel::field(['realname'])->where('id', $id)->value();
        return [$realname, 'realname'];
    }

    public static function getCtimeAttr($ctime)
    {
        $time = date('Y-m', $ctime - 30 * 86400); // 实际考核的是上个月的时间
        return [$time, 'month'];
    }
}
