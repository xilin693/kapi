<?php

namespace app\validate;

use king\lib\Valid;

class Dict
{
    public static function check($data, $current = '')
    {
        $data = is_array($data) ? $data : ['id' => $data];
        $valid = Valid::getClass($data, $current);
        $scene = [
            'get' => ['id'],
            'save' => ['name', 'type', 'tag_id'],
            'delete' => ['id'],
            'item' => ['item'],
            'update' => ['id', 'name', 'type']
        ];
        $valid->setScene($scene);
        $valid->addRule('id', 'checkId', 'id');
        $valid->addRule('name', 'required|minLength,2|maxLength,20', '字典名称');
        $valid->addRule('type', 'required|int,[0,1]', '字典类型');
        $valid->addRule('tag_id', 'int', '字典标签');
        $valid->addRule('item', 'required|minLength,1', '字典类别');
        $valid->response();
    }
}