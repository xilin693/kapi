<?php

namespace app\service;

use king\lib\exception\BadRequestHttpException;
use app\model\Dict as DictModel;
use app\model\Item as ItemModel;
use app\cache\Dict as DictCache;

class Dict
{
    public static function getList($dict_id)
    {
        return ItemModel::where('dict_id', $dict_id)->get();
    }

    public static function getTagList($tag_id)
    {
        $data = [];
        $rs = DictModel::field(['id', 'name'])->where('tag_id', $tag_id)->get();
        foreach ($rs as $row) {
            $data[$row['id']] = ItemModel::field(['id', 'name'])->where('dict_id', $row['id'])->get();
            $data[$row['id']]['dict_name'] = $row['name'];
        }

        return $data;
    }

    public static function save($data)
    {
        $id = DictModel::field(['id'])->where('name', $data['name'])->where('tag_id', $data['tag_id'])->value();
        if ($id) {
            throw new BadRequestHttpException('字典已存在');
        } else {
            $id = DictModel::save($data);
            DictCache::set($id, $data);
            return $id;
        }
    }

    public static function update($data)
    {
        $id = DictModel::save($data);
        DictCache::set($id, $data);
    }

    public static function delete($id)
    {
        $type = DictCache::get($id, 'type');
        if ($type == 1) { // json格式字典项只有一条直接删除
            ItemModel::where('dict_id', $id)->delete();
        } else {
            $count = ItemModel::where('dict_id', $id)->count();
            if ($count > 0) {
                throw new BadRequestHttpException('该字典项还有数据,无法直接删除');
            }
        }

        $rs = DictCache::delete($id);
        if ($rs) {
            return DictModel::where('id', $id)->delete();
        }
    }
}
