<?php

namespace app\helper;

use king\lib\Signature;
use king\lib\exception\UnauthorizedHttpException;
use king\lib\exception\BadRequestHttpException;
use app\cache\Login as LoginCache;
use app\model\Project as ProjectModel;

class Verify
{
    public static function auth($token)
    {
        $rs = LoginCache::checkToken($token);
        if (!$rs) {
            throw new UnauthorizedHttpException('token校验失败');
        }
    }

    public static function sign($req)
    {
        if (!Signature::getClass()->validate($req)) {
            throw new BadRequestHttpException('验签失败');
        }
    }

    public static function whetherMe($id, $token)
    {
        return ($id == self::getMe($token));
    }

    public static function getMe($token)
    {
        return LoginCache::getId($token);
    }

    public static function keyMatch(string $key1, string $key2): bool
    {
        $key2 = str_replace(['/*'], ['/.*'], $key2);

        $pattern = '/(.*):[^\/]+(.*)/';
        for (; ;) {
            if (false === strpos($key2, '/:')) {
                break;
            }

            $key2 = preg_replace_callback(
                $pattern,
                function ($m) {
                    return $m[1] . '(\d+)' . $m[2];
                },
                $key2
            );
        }

        return self::regexMatch($key1, '^' . $key2 . '$');
    }

    private static function regexMatch(string $key1, string $key2): bool
    {
        return (bool)preg_match('~' . $key2 . '~', $key1);
    }

    public static function checkProjectOwner($project_id, $account_id)
    {
        return ProjectModel::where('account_id', $account_id)->where('id', $project_id)->count();
    }
}
