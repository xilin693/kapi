<?php

namespace app\controller\www;

use king\lib\Response;
use app\validate\Item as ItemValidate;
use app\service\Item as ItemService;

class Item
{
    public function add($id)
    {
        $data = P();
        $data['dict_id'] = $id;
        ItemValidate::check($data, 'save');
        $rs = ItemService::save($data);
        Response::sendSuccessJson($rs);
    }

    public function edit($id)
    {
        $data = steam($id);
        ItemValidate::check($data, 'update');
        ItemService::update($data);
        Response::sendSuccessJson();
    }

    public function delete($id)
    {
        ItemValidate::check($id, 'delete');
        ItemService::delete($id);
        Response::sendSuccessJson();
    }
}
