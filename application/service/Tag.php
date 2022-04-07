<?php

namespace app\service;

use king\lib\exception\BadRequestHttpException;
use app\model\Tag as TagModel;
use app\cache\Tag as TagCache;

class Tag
{
    public static function getList()
    {
        return TagModel::get();
    }
    
    public static function save($data)
    {
        return TagModel::save($data);
    }

    public static function update($data)
    {
        $count = TagModel::where('id', $data['id'])->count();
        if ($count > 0) {
            return TagModel::save($data);
        } else {
            throw new BadRequestHttpException('更新内容不存在');
        }
    }

    public static function delete($id)
    {
        $count = TagModel::where('id', $id)->count();
        if ($count > 0) {
            return TagModel::where('id', $id)->delete();
        } else {
            throw new BadRequestHttpException('删除对象不存在');       
        }
    }
        
    public static function getInfo($id)
    {
        return TagModel::where('id', $id)->find();
    }
}
