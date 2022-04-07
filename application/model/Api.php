<?php

namespace app\model;

use king\Model;
use app\model\Account as AccountModel;

class Api extends Model
{
    protected static $table = 'project_api';
    public static $fields = ['id', 'project_id', 'pid', 'name', 'method', 'uri', 'post_type', 'account_id', 'complete', 'path_data', 'sort'];

    public static function getCompleteAttr($value)
    {
        $status = [1 => '已完成', 0 => '未完成'];
        return [$status[$value] ?? '', 'complete_text'];
    }

    public static function getPidAttr($pid)
    {
        $category = self::field(['name'])->where('id', $pid)->value();
        return [$category, 'category'];
    }

    public static function getAccountIdAttr($id)
    {
        $realname = AccountModel::field(['realname'])->where('id', $id)->value();
        return [$realname, 'realname'];
    }
}
