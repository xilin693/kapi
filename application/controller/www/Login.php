<?php

namespace app\controller\www;

use king\lib\Response;
use king\lib\Permission as permissionLib;
use app\validate\Login as LoginValidate;

class Login
{
    public function index()
    {
        $data = P();
        $data['code'] = H(C('permission.captcha_header'));
        LoginValidate::check($data);
        $rs = permissionLib::getClass()->loadByUser($data['username'], $data['password']);
        Response::sendResponseJson(200, $rs);
    }

    public function logout()
    {
        $token = H(C('permission.token_header'));
        permissionLib::getClass()->tokenValid($token);
        permissionLib::getClass()->logout($token);
        Response::sendResponseJson(200, '已登出');

    }

    public function exec()
    {
        if (!is_cli()) {
            exit;
        }

        $rs = EnvModel::field(['id', 'header_value', 'header_key'])->get();
        foreach ($rs as $row) {
            if ($row['header_key']) {
                $global_header = json_encode([['key' => $row['header_key'], 'value' => $row['header_value']]]);
                EnvModel::where('id', $row['id'])->update(['global_header' => $global_header]);
            }
        }
    }
}
