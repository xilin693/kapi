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
        Response::sendResponseJson(200, $rs);
    }

    public function add()
    {
        $data = P();
        TagValidate::check($data, 'save');
        $rs = TagService::save($data);
        Response::sendResponseJson(200, $rs);
    }

    public function edit($id)
    {
        $data = steam($id);
        TagValidate::check($data, 'update');
        $rs = TagService::update($data);
        Response::sendResponseJson(200, $rs);
    }

    public function delete($id)
    {
        TagValidate::check($id, 'delete');
        TagService::delete($id);
        Response::sendResponseJson(200);
    }

    public function detail($id)
    {
        TagValidate::check($id, 'detail');
        $rs = TagService::getInfo($id);
        Response::sendResponseJson(200, $rs);
    }
}

