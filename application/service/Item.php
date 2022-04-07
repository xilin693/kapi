<?php

namespace app\service;

use king\lib\exception\BadRequestHttpException;
use app\model\Item as ItemModel;
use app\cache\Item as ItemCache;
use app\cache\Dict as DictCache;
use app\helper\Verify as VerifyHelper;

class Item
{
    public static function save($data)
    {
        $type = DictCache::get($data['dict_id'], 'type');
        if ($type === '') {
            throw new BadRequestHttpException('字典不存在');
        } elseif ($type == 1) {
            VerifyHelper::checkJson($data['name']);
        }

        $count = ItemModel::where('name', $data['name'])->where('dict_id', $data['dict_id'])->count();
        if ($count < 1) {
            $id = ItemModel::save($data);
            ItemCache::set($id, $data);
            return $id;
        } else {
            throw new BadRequestHttpException('字典内容已存在');
        }
    }

    public static function update($data)
    {
        $data['dict_id'] = ItemModel::field(['dict_id'])->where('id', $data['id'])->value();
        if ($data['dict_id']) {
            ItemCache::set($data['id'], $data);
            return ItemModel::save($data);
        } else {
            throw new BadRequestHttpException('字典不存在');
        }
    }

    public static function delete($id)
    {
        $dict_id = ItemModel::field(['dict_id'])->where('id', $id)->value();
        if ($dict_id) {
            ItemCache::delete($dict_id, $id);
            return ItemModel::where('id', $id)->delete();
        } else {
            throw new BadRequestHttpException('字典不存在');
        }
    }
}
