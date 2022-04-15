<?php

namespace app\controller\www;

use king\lib\Response;
use app\validate\Tag as TagValidate;
use app\service\Tag as TagService;

class Tag
{
    public function get()
    {
        $rs = TagService::getList();
        Response::sendSuccessJson($rs);
    }

    public function add()
    {
        $data = P();
        TagValidate::check($data, 'save');
        $rs = TagService::save($data);
        Response::sendSuccessJson($rs);
    }

    public function edit($id)
    {
        $data = steam($id);
        TagValidate::check($data, 'update');
        $rs = TagService::update($data);
        Response::sendSuccessJson($rs);
    }

    public function delete($id)
    {
        TagValidate::check($id, 'delete');
        TagService::delete($id);
        Response::sendSuccessJson();
    }

    public function detail($id)
    {
        TagValidate::check($id, 'detail');
        $rs = TagService::getInfo($id);
        Response::sendSuccessJson($rs);
    }
}

