<?php

namespace app\controller\www;

use king\lib\Response;
use app\validate\Menu as MenuValidate;
use app\service\Menu as MenuService;

class Menu
{
    public function get()
    {
        $rs = MenuService::getList();
        Response::sendSuccessJson($rs);
    }

    public function add()
    {
        $data = P();
        MenuValidate::check($data, 'save');
        $data['segment'] = $data['url'] ? explode('/', $data['url'])[0] : '';
        $rs = MenuService::save($data);
        Response::sendSuccessJson($rs);
    }

    public function edit($id)
    {
        $data = steam($id);
        $data['segment'] = $data['url'] ? explode('/', $data['url'])[0] : '';
        MenuValidate::check($data);
        $rs = MenuService::update($id, $data);
        Response::sendSuccessJson($rs);
    }

    public function delete($id)
    {
        MenuValidate::check($id, 'delete');
        MenuService::delete($id);
        Response::sendSuccessJson();
    }

    public function detail($id)
    {
        MenuValidate::check($id, 'detail');
        $rs = MenuService::getInfo($id);
        Response::sendSuccessJson($rs);
    }
}
