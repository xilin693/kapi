<?php

namespace app\controller\www;

use king\lib\Response;
use app\validate\Account as AccountValidate;
use app\validate\Page as PageValidate;
use app\service\Account as AccountService;

class Account
{
    public function get($type = '')
    {
        $data = G();
        $data['type'] = $type;
        PageValidate::check($data);
        AccountValidate::check($data, 'search');
        $rs = AccountService::getList($data);
        Response::sendResponseJson(200, $rs);
    }

    public function add()
    {
        $data = P();
        AccountValidate::check($data, 'reg');
        $rs = AccountService::save($data);
        Response::sendResponseJson(200, $rs);
    }

    public function edit($id)
    {
        $data = steam($id);
        AccountValidate::check($data, 'update');
        $rs = AccountService::update($id, $data);
        Response::sendResponseJson(200, $rs);
    }

    public function audit($id)
    {
        $data = steam($id);
        AccountValidate::check($data, 'audit');
        $rs = AccountService::update($id, $data, ['audit', 'role_ids']);
        Response::sendResponseJson(200, $rs);
    }

    public function alterPassword()
    {
        $data = steam();
        $data['id'] = $this->account_id;
        AccountValidate::check($data, 'password');
        $rs = AccountService::updatePassword($data);
        Response::sendResponseJson(200, $rs);
    }

    public function resetPassword($id)
    {
        $data = steam($id);
        $data['token'] = H('Authorization');
        AccountValidate::check($data, 'reset');
        $rs = AccountService::resetPassword($id, $data['password'], $data['token']);
        Response::sendResponseJson(200, $rs);
    }

    public function delete($id)
    {
        AccountValidate::check($id, 'delete');
        AccountService::delete($id);
        Response::sendResponseJson(200);
    }

    public function detail($id)
    {
        AccountValidate::check($id, 'detail');
        $rs = AccountService::getInfo($id);
        Response::sendResponseJson(200, $rs);
    }
}
