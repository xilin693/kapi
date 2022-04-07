<?php

namespace app\validate;

use king\lib\Valid;

class Project
{
    public static function check($data, $current = '')
    {
        $data = is_array($data) ? $data : ['id' => $data];
        $valid = Valid::getClass($data, $current);
        $scene = [
            'get' => [],
            'save' => [],
            'json' => ['type', 'project_id', 'filename'],
            'export' => ['type', 'project_id'],
            'update' => ['id'],
            'delete' => ['id'],
            'detail' => ['id'],
        ];
        $valid->setScene($scene);
        $valid->addRule('id', 'required|checkId');
        $valid->addRule('type', 'required|in,[1,2,3]', '导入类型');
        $valid->addRule('project_id', 'required|int|gt,0|checkProjectId', '项目名称');
        $valid->addRule('filename', 'required', '导入json');
        $valid->response();
    }
}
