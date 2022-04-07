<?php

namespace app\validate;

use king\lib\Valid;

class Item
{
    public static function check($data, $current = '')
    {
        $data = is_array($data) ? $data : ['id' => $data];
        $valid = Valid::getClass($data, $current);
        $scene = [
            'save' => ['dict_id', 'name'],
            'delete' => ['id'],
            'update' => ['id', 'name']
        ];
        $valid->setScene($scene);
        $valid->addRule('id', 'checkId', 'id');
        $valid->addRule('dict_id', 'required|int', '字典id');
        $valid->addRule('name', 'required|minLength,2', '字典内容');
        $valid->response();
    }
}