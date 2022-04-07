<?php

namespace app\controller\www;

use king\lib\Response;
use app\validate\Role as RoleValidate;
use app\service\Role as RoleService;

class Role
{
    public function get()
    {
        $rs = RoleService::getList();
        Response::sendResponseJson(200, $rs);
    }

    public function add()
    {
        $data = P();
        RoleValidate::check($data, 'save');
        $rs = RoleService::save($data);
        Response::sendResponseJson(200, $rs);
    }

    public function edit($id)
    {
        $data = steam($id);
        RoleValidate::check($data, 'update');
        $rs = RoleService::update($id, $data);
        Response::sendResponseJson(200, $rs);
    }

    public function delete($id)
    {
        RoleValidate::check($id, 'delete');
        RoleService::delete($id);
        Response::sendResponseJson(200);
    }

    public function detail($id)
    {
        RoleValidate::check($id, 'detail');
        $rs = RoleService::getInfo($id);
        Response::sendResponseJson(200, $rs);
    }

    public function menu()
    {
        $token = H('Authorization');
        $rs = RoleService::getRoleMenu($token);
        Response::sendResponseJson(200, $rs);
    }
}
