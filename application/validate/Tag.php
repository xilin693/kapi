<?php

namespace app\validate;

use king\lib\Valid;

class Tag
{
    public static function check($data, $current = '')
    {
        $data = is_array($data) ? $data : ['id' => $data];
        $valid = Valid::getClass($data, $current);
        $scene = [
            'save' => ['name'],
            'update' => ['id', 'name'],
            'delete' => ['id'],
            'detail' => ['id']
        ];
        $valid->setScene($scene);
        $valid->addRule('id', 'required|int', 'id');
        $valid->addRule('name', 'required|length,1,10', 'id');
        $valid->response();
    }
}