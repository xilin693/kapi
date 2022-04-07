<?php

namespace app\service;

use app\helper\Verify as VerifyHelper;
use king\lib\exception\BadRequestHttpException;
use app\model\Account as AccountModel;
use app\model\Member as MemberModel;
use app\helper\Login as LoginHelper;

class Account
{
    public static function getList($data)
    {
        $query = AccountModel::attr();

        if (!empty($data['type'])) {
            if ($data['type'] == 'name') {
                $ids = MemberModel::field(['account_id'])->where('project_id', $data['project_id'])->column();
                return $query->field(['id', 'username', 'realname'])->where('audit', 1)
                    ->where('id', 'not in', $ids)->where('id', '>', 1)->get();
            } elseif ($data['type'] == 'audit') {
                return $query->field(['id', 'username', 'realname', 'audit'])->where('audit', '>', 0)
                    ->order('audit', 'asc')->page($data['per_page'], $data['page']);
            }
        } else {
            return $query->where('audit', 1)->page($data['per_page'], $data['page']);
        }
    }

    public static function save($data)
    {
        $count = AccountModel::where('username', $data['username'])->count();
        if (!$count) {
            unset($data['password2']);
            $data['password'] = LoginHelper::crypt($data['password']);
            return AccountModel::save($data);
        } else {
            throw new BadRequestHttpException('用户名已存在');
        }
    }

    public static function update($id, $data)
    {
        $count = AccountModel::where('id', $id)->count();
        if ($count > 0) {
            return AccountModel::save($data, ['realname', 'avatar', 'role_ids', 'audit']);
        } else {
            throw new BadRequestHttpException('更新内容不存在');
        }
    }

    public static function updatePassword($data)
    {
        $count = AccountModel::where('id', $data['id'])->where('password', LoginHelper::crypt($data['old_password']))->count();
        if ($count > 0) {
            unset($data['password2']);
            $data['password'] = LoginHelper::crypt($data['password']);
            return AccountModel::save($data, ['password']);
        } else {
            throw new BadRequestHttpException('旧密码错误');
        }
    }

    public static function resetPassword($id, $password, $token)
    {
        if (VerifyHelper::getMe($token) == 1) {
            return AccountModel::where('id', $id)->update(['password' => LoginHelper::crypt($password)]);
        } else {
            throw new BadRequestHttpException('管理员才能重置密码');
        }
    }

    public static function delete($id)
    {
        if ($id > 1) {
            return AccountModel::where('id', $id)->delete();
        } else {
            throw new BadRequestHttpException('超级管理员不能删除');
        }
    }

    public static function getInfo($id)
    {
        return AccountModel::field(['id', 'username', 'realname', 'role_ids', 'avatar'])->where('id', $id)->attr()->find();
    }
}
