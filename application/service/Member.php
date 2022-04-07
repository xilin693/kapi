<?php

namespace app\service;

use king\lib\exception\BadRequestHttpException;
use app\model\Member as MemberModel;
use app\model\Project as ProjectModel;

class Member
{
    public static function getList($data)
    {
        $query = MemberModel::attr();
        if ($data['project_id'] > 0) {
            $query->where('project_id', $data['project_id']);
        }

        return $query->page($data['per_page'], $data['page']);
    }

    public static function save($data)
    {
        $insert = [];
        $pid = ProjectModel::field(['pid'])->where('id', $data['project_id'])->value();
        $count = MemberModel::where('project_id', $data['project_id'])->where('account_id', 'in', $data['accounts'])->count();
        if ($count > 0) {
            throw new BadRequestHttpException('用户已添加过');
        } else {
            foreach ($data['accounts'] as $account_id) {
                $insert[] = ['project_pid' => $pid, 'project_id' => $data['project_id'], 'account_id' => $account_id];
            }

            return MemberModel::batchInsert($insert);
        }
    }

    public static function update($id, $data)
    {
        $row = MemberModel::where('id', $id)->find();
        if ($row) {
            if ($data['account_id'] != 1) {
                $group = MemberModel::field(['group'])->where('account_id', $data['account_id'])
                    ->where('project_id', $data['project_id'])->value();
                if ($group != 1) { //非组长
                    throw new BadRequestHttpException('权限不足');
                }
            }

            unset($data['account_id']);
            return MemberModel::save($data);
        } else {
            throw new BadRequestHttpException('更新内容不存在');
        }
    }

    public static function delete($data)
    {
        $group = MemberModel::field(['group'])->where('account_id', $data['current_account_id'])
            ->where('project_id', $data['project_id'])->value();
        if ($group == 1) {
            $count = MemberModel::where('account_id', $data['account_id'])->where('project_id', $data['project_id'])->count();
            if ($count > 0) {
                return MemberModel::where('account_id', $data['account_id'])->where('project_id', $data['project_id'])->delete();
            } else {
                throw new BadRequestHttpException('删除对象不存在');
            }
        } else {
            throw new BadRequestHttpException('权限不足');
        }
    }

    public static function getInfo($data)
    {
        return MemberModel::where('account_id', $data['account_id'])
            ->where('project_id', $data['project_id'])->find();
    }
}
