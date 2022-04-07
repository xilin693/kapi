<?php

namespace app\validate;

use king\lib\Valid;

class Menu
{
    public static function check($data, $current = '')
    {
        $data = is_array($data) ? $data : ['id' => $data];
        $valid = Valid::getClass($data, $current);
        $scene = [
            'delete' => ['id'],
            'detail' => ['id']
        ];
        $valid->setScene($scene);
        $valid->hideSceneField('save', ['id', 'redirect', 'component']);
        $valid->addRule('id', 'checkId');
        $valid->addRule('pid', 'int', 'pid');
        $valid->addRule('name', 'required|length,1,10', '菜单名称');
        $valid->addRule('sort', 'int', '排序');
        $valid->addRule('url', 'length,1,100', '接口地址');
        $valid->addRule('method', 'in,[get,post,put,delete]', '请求方式');
        $valid->addRule('redirect', 'length,1,50', '前端路由');
        $valid->addRule('component', 'length,1,50', '前端组件名称');
        $valid->addRule('icon', 'ext,[png,svg],', '图标');
        $valid->addRule('menu', 'required|in,[0,1]', '是否展示为菜单');
        $valid->addRule('status', 'required|in,[0,1]', '状态');
        $valid->response();
    }
}
