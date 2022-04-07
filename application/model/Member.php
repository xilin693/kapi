<?php

namespace app\model;

use king\Model;
use app\model\Account as AccountModel;

class Member extends Model
{
    protected static $table = 'project_account';

    public static function getAccountIdAttr($id)
    {
        $name = AccountModel::field(['realname'])->where('id', $id)->value();
        return [$name, 'name'];
    }

    public static function updateEnv($where, $env)
    {
        return self::where($where)->update(['env_id' => $env]);
    }
}
