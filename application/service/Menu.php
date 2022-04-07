<?php

namespace app\service;

use app\model\Menu as MenuModel;
use app\helper\Menu as MenuHelper;
use king\lib\exception\BadRequestHttpException;

class Menu
{
    public static function getList()
    {
        $rs = MenuModel::order(['sort' => 'desc'])->get();
        return MenuHelper::getTree($rs);
    }
    
    public static function save($data)
    {
        return MenuModel::save($data);
    }

    public static function update($id, $data)
    {
        $count = MenuModel::where('id', $id)->count();
        if ($count > 0) {
            return MenuModel::save($data);
        } else {
            throw new BadRequestHttpException('更新内容不存在');
        }
    }

    public static function delete($id)
    {
        return MenuModel::where('id', $id)->delete();
    }
    
    public static function getInfo($id)
    {
        return MenuModel::where('id', $id)->find();
    }
}
