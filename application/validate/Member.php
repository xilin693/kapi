<?php

namespace app\validate;

use king\lib\Valid;

class Member
{
    public static function check($data, $current = '')
    {
        $data = is_array($data) ? $data : ['id' => $data];
        $valid = Valid::getClass($data, $current);
        $scene = [
            'get' => ['account_id'],
            'update' => ['id'],
            'delete' => ['id'],
            'detail' => ['id'],
            'project' => ['accounts', 'project_id', 'account_id']
        ];
        $valid->setScene($scene);
        $valid->hideSceneField('save', ['id']);
        $valid->addRule('id', 'required|int|gt,0', 'id');
        $valid->addRule('accounts', 'array');
        $valid->addRule('account_id', 'checkId');
        $valid->addRule('project_id', 'required|int|gt,0', 'project_id');
        $valid->response();
    }
}
