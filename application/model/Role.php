<?php

namespace app\model;

use king\Model;

class Role extends Model
{
    protected static $table = 'sys_role';

    public static function getStatusAttr($value)
    {
        $status = ['-1', '已删除', '0' => '停用', '1' => '正常'];
        return $status[$value];
    }
}
