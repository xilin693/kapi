<?php

namespace app\validate;

use king\lib\Valid;

class Page
{
    public static function check(&$data, $per_page = 20)
    {
        $valid = Valid::getClass($data);
        $data['page'] = $data['page'] ?? 1;
        $data['per_page'] = $data['per_page'] ?? $per_page;
        $valid->addRule('page', 'gt,0|lt,1000', '页数');
        $valid->addRule('per_page', 'gt,0|lt,50', '条数');
        $valid->response();
    }
}