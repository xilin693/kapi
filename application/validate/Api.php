<?php

namespace app\validate;

use king\lib\Valid;

class Api
{
    public static function check($data, $current = '')
    {
        $data = is_array($data) ? $data : ['id' => $data];
        $valid = Valid::getClass($data, $current);
        $scene = [
            'get' => [],
            'update' => ['id'],
            'delete' => ['id'],
            'detail' => ['id'],
        ];
        $valid->setScene($scene);
        $valid->hideSceneField('save', ['id']);
        $valid->addRule('id', 'required|int|gt,0', 'id');
        $valid->addRule('name', 'required|minLength,2', '接口名称');
        $valid->response();
    }
}
