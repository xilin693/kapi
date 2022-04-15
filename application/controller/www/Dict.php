<?php

namespace app\controller\www;

use king\lib\Response;
use app\validate\Dict as DictValidate;
use app\service\Dict as DictService;

class Dict
{
    public function get($id)
    {
        DictValidate::check($id, 'get');
        $rs = DictService::getList($id);
        Response::sendSuccessJson($rs);
    }

    public function add()
    {
        $data = P();
        DictValidate::check($data, 'save');
        $rs = DictService::save($data);
        Response::sendSuccessJson($rs);
    }

    public function edit($id)
    {
        $data = steam($id);
        DictValidate::check($data, 'update');
        DictService::update($data);
        Response::sendSuccessJson();
    }

    public function delete($id)
    {
        DictValidate::check($id, 'delete');
        DictService::delete($id);
        Response::sendSuccessJson();
    }

    public function getTag($id)
    {
        DictValidate::check($id, 'get');
        $rs = DictService::getTagList($id);
        Response::sendSuccessJson($rs);
    }
}
