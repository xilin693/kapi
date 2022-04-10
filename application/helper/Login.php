<?php

namespace app\helper;

use app\cache\Login as LoginCache;
use king\lib\exception\UnauthorizedHttpException;
use king\lib\exception\BadRequestHttpException;

class Login
{
    public static function getAdminInfo($token = '')
    {
        $token = H('Authorization') ?: $token;

        $admin_info = LoginCache::checkToken($token);
        if (!$admin_info) {
            throw new UnauthorizedHttpException('token校验失败');
        }

        return $admin_info;
    }

    public static function getAccountId()
    {
        return LoginCache::getId(H('Authorization'));
    }

    public static function crypt($password)
    {
        return md5(C('permission.password_salt') . $password);
    }

    public static function makeToken($id)
    {
        $token = md5($id . ':' . time() . ':' . C('token_key'));
        LoginCache::setToken($token, $id);
        return $token;
    }

    public static function tokenValid($token)
    {
        if (strlen($token) != 32 || !preg_match('/^[A-Za-z0-9]+$/', $token)) {
            throw new BadRequestHttpException('数据校验失败'); // 此处仅针对恶意调用,不作明确提示
        }
    }
}
