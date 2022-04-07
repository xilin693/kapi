<?php

namespace app\service;

use king\lib\exception\BadRequestHttpException;
use app\model\Role as RoleModel;
use app\model\Account as AccountModel;
use app\model\Menu as MenuModel;
use app\helper\Menu as MenuHelper;

class Role
{
    public static function getList()
    {
        return RoleModel::field(['id', 'name', 'status', 'create_time', 'update_time'])->where('status', '<>', -1)
            ->orderby(['id' => 'desc'])->attr()->get();
    }
    
    public static function save($data)
    {
        $time = time();
        $data['create_time'] = $time;
        $data['update_time'] = $time;
        return RoleModel::save($data);
    }

    public static function update($id, $data)
    {
        $count = RoleModel::where('id', $id)->count();
        if ($count > 0) {
            $data['update_time'] = time();
            return RoleModel::save($data);
        } else {
            throw new BadRequestHttpException('更新内容不存在');
        }
    }

    public static function delete($id)
    {
        $count = AccountModel::where('role_ids', $id)->count();
        if ($count < 1) {
            return RoleModel::where('id', $id)->delete();
        } else {
            throw new BadRequestHttpException('该用户组下还有用户存在,无法删除');
        }
    }
    
    public static function getInfo($id)
    {
        return RoleModel::where('id', $id)->attr()->find();
    }

    public static function setStatus($id, $data, $allow)
    {
        return RoleModel::where('id', $id)->update($data, $allow);
    }

    public static function getRoleMenu($token)
    {
        $menu_list = json_decode(MenuHelper::getRoleMenu($token), true);
        $rs = MenuModel::where('id', 'in', $menu_list)->order(['sort' => 'desc'])->get();
        return MenuHelper::getTree($rs);
    }
}
