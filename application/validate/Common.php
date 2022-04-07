<?php

namespace app\validate;

use king\lib\Valid;
use king\lib\Captcha;
use app\helper\Verify as VerifyHelper;

class Common
{
    public static function checkId($id)
    {
        $data['id'] = $id;
        $valid = Valid::getClass($data);
        $valid->addRule('id', 'required|int|gt,0', 'id');
        $valid->response();
    }

    public static function checkMe($id, $valid)
    {
        if (!VerifyHelper::whetherMe($id, $valid->data['token'])) {
            $valid->setError('只能修改自己的密码');
        }
    }

    public static function checkCaptcha($value, $valid)
    {
        $capt = Captcha::getClass();
        if ($capt->valid($value, $valid->data['code']) != true) {
            $valid->setError('验证码错误');
        }
    }

    public static function checkRoleExist($value, $valid)
    {
        $keys = array_keys($valid->data);
        $intersect = array_intersect($keys, ['id','name', 'menu_id_list', 'status']);
        if (count($intersect) < 2) {
            $valid->setError('更新的内容不能为空');
        }
    }

    public static function checkProjectId($value, $valid)
    {
        $count = VerifyHelper::checkProjectOwner($value, $valid->data['account_id']);
        if ($count < 1) {
            $valid->setError('只允许项目所有者导入');
        }
    }

    public static function checkScore($value, $valid)
    {
        foreach ($value as $k => $v) {
            if (!is_numeric($v)) {
                $valid->setError('分数不正确');
            }
        }
    }

    public static function checkSum($value, $valid)
    {
        if (!is_numeric($value) || $value > 100) {
            $valid->setError('总分不正确');
        }
    }
}
