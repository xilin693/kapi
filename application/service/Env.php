<?php

namespace app\service;

use king\lib\exception\BadRequestHttpException;
use app\model\Env as EnvModel;
use app\cache\Env as EnvCache;
use app\model\Member as MemberModel;

class Env
{
    public static function getList($data)
    {
        $env_id = MemberModel::field(['env_id'])->where('account_id', $data['account_id'])->where('project_id', $data['project_id'])->value();
        $rs = EnvModel::field(['name', 'id', 'private', 'domain', 'url_prefix', 'global_header', 'global_cookie'])->where('project_id', $data['project_id'])->
        andWhere(function ($query) use ($data) {
            $query->where('private', 0)->orWhere(function ($query) use ($data) {
                $query->where('private', 1)->where('account_id', $data['account_id']);
            });
        })->get();

        $default_key = 0;
        $default_value = [];
        foreach ($rs as $key => &$value) {
            if ($value['id'] == $env_id) {
                $default_key = $key;
                $value['default'] = 1;
                $default_value = $value;
            } else {
                $value['default'] = 0;
            }
        }

        if ($default_key > 0) {
            unset($rs[$default_key]);
            array_unshift($rs, $default_value);
        }

        return $rs;
    }

    public static function getNameList($data)
    {
        return EnvModel::field(['name'])->where('project_id', $data['project_id'])->
        andWhere(function ($query) use ($data) {
            $query->where('private', 0)->orWhere(function ($query) use ($data) {
                $query->where('private', 1)->where('account_id', $data['account_id']);
            });
        })->column();
    }

    public static function save($data)
    {
        $where = ['account_id' => $data['account_id'], 'project_id' => $data['project_id']];
        $id = EnvModel::save($data, EnvModel::$fields);
        if ($id && !empty($data['default'])) {
            MemberModel::updateEnv($where, $id);
        }
        return $id;
    }

    public static function update($id, $data)
    {
        $where = ['account_id' => $data['account_id'], 'project_id' => $data['project_id']];
        $count = EnvModel::where('id', $id)->count();
        if ($count > 0) {
            $rs = EnvModel::save($data, EnvModel::$fields);
            if (!empty($data['default'])) {
                MemberModel::updateEnv($where, $id);
            }

            return $rs;
        } else {
            throw new BadRequestHttpException('更新内容不存在');
        }
    }

    public static function delete($id)
    {
        $count = EnvModel::where('id', $id)->count();
        if ($count > 0) {
            return EnvModel::where('id', $id)->delete();
        } else {
            throw new BadRequestHttpException('删除对象不存在');
        }
    }

    public static function getInfo($id)
    {
        $row = EnvModel::where('id', $id)->find();
        $row['default'] = 0;
        $env_id = MemberModel::field(['env_id'])->where('account_id', $row['account_id'])
            ->where('project_id', $row['project_id'])->value();
        if ($env_id == $id) {
            $row['default'] = 1;
        }

        return $row;
    }
}
