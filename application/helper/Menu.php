<?php

namespace app\helper;

use king\lib\exception\BadRequestHttpException;
use app\helper\Verify as VerifyHelper;
use app\model\Account as AccountModel;
use app\model\Role as RoleModel;

class Menu
{
    public static function getTree($rs, $sub = 'sub_menu')
    {
        $tree = [];
        $rs = array_column($rs, NULL, 'id');
        foreach ($rs as $value) {
            if (isset($rs[$value['pid']])) {
                $rs[$value['pid']][$sub][] = &$rs[$value['id']];
            } else {
                $tree[] = &$rs[$value['id']];
            }
        }

        return $tree;
    }

    public static function getRoleMenu($token, $id = '')
    {
        if (!$id) {
            $id = VerifyHelper::getMe($token);
        }

        $role_id = AccountModel::field(['role_ids'])->where('id', $id)->value();
        return RoleModel::field(['menu_id_list'])->where('id', $role_id)->value();
    }
}