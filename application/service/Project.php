<?php

namespace app\service;

use app\helper\Element;
use app\model\ApiDetail;
use king\lib\exception\BadRequestHttpException;
use app\model\Project as ProjectModel;
use app\model\Api as ApiModel;
use app\model\Account as AccountModel;
use app\model\ApiDetail as ApiDetailModel;
use app\model\ProjectAccount as ProjectAccountModel;
use app\helper\Element as ElementHelper;

class Project
{
    public static function getList($data)
    {
        $query = ProjectModel::where('account_id', $data['account_id']);
        if (empty($data['pid'])) {
            $query->where('pid', '>', 0);
        }

        $rs = $query->order('sort')->get();
        $username = AccountModel::field(['username'])->where('id', $data['account_id'])->value();
        if ($username == 'guest') { // 用于访问项目进度
            $rs[] = ['id' => 'org', 'name' => '项目一览表', 'icon' => 'c-scale-to-original', 'icon_color' => '#0a3e82'];
        } elseif ($username == 'liucx') { // java同事测试
            $rs[] = ['id' => 'org', 'name' => '我的项目', 'icon' => 'c-scale-to-original', 'icon_color' => '#0a3e82'];
        } else {
            if ($data['account_id'] == 1) {
                $rs[] = ['id' => 'kpl', 'name' => '绩效列表', 'icon' => 'notebook-2', 'icon_color' => '#3AAFA9'];
            } else {
                $rs[] = ['id' => 'kpi', 'name' => '绩效考核', 'icon' => 'medal-1', 'icon_color' => '#3AAFA9'];
                $rs[] = ['id' => 'history', 'name' => '历史绩效', 'icon' => 'trophy', 'icon_color' => '#8FC1E3'];
            }

            $rs[] = ['id' => 'org', 'name' => '我的项目', 'icon' => 'c-scale-to-original', 'icon_color' => '#0a3e82'];
        }

        return $rs;
    }

    public static function importJson($data)
    {
        $file = ROOT_PATH . 'public/' . $data['filename'];
        $content = file_get_contents($file);
        $array = json_decode($content, true);
        if (count($array) > 0) {
            if ($data['account_id'] > 0 && $data['project_id'] > 0) {
                ApiModel::where('project_id', $data['project_id'])->delete();
                ApiDetailModel::where('project_id', $data['project_id'])->delete();
            }

            foreach ($array as $key => $value) {
                $new_array = ['name' => $value['name'], 'project_id' => $data['project_id'],
                    'account_id' => $data['account_id']];
                $insert = ApiModel::insert($new_array);
                if (isset($value['data']) && count($value['data']) > 0) {
                    foreach ($value['data'] as $k => $v) {
                        $array2 = only($v, ['name', 'method', 'uri', 'complete', 'path_data']);
                        $array2['project_id'] = $data['project_id'];
                        $array2['account_id'] = $data['account_id'];
                        $array2['pid'] = $insert;
                        $insert2 = ApiModel::insert($array2);
                        $array3 = only($v, ['header_data', 'query_data', 'form_data', 'put_data',
                            'return_data', 'put_doc', 'comment', 'utime']);
                        $array3['api_id'] = $insert2;
                        $array3['project_id'] = $data['project_id'];
                        ApiDetailModel::insert($array3);
                    }
                }
            }
        }
    }

    public static function exportJson($data)
    {
        $rs = ApiModel::field(['id', 'name'])->where('pid', 0)->where('project_id', $data['project_id'])->get();
        foreach ($rs as $key => $row) {
            $rs[$key]['data'] = ApiModel::setTable('project_api a')->
            field(['a.name', 'a.method', 'a.uri', 'a.complete', 'a.path_data', 'b.header_data', 'b.query_data',
                'b.form_data', 'b.put_data', 'b.return_data', 'b.put_doc', 'b.comment', 'b.utime'])
                ->join('project_api_detail b', 'a.id = b.api_id')->where('a.pid', $row['id'])->get();
        }

        return json_encode($rs);
    }

    public static function importYapiJson($data)
    {
        $file = ROOT_PATH . 'public/' . $data['filename'];
        $content = file_get_contents($file);
        $array = json_decode($content, true);
        if (count($array) > 0) {
            if ($data['account_id'] > 0 && $data['project_id'] > 0) {
                ApiModel::where('project_id', $data['project_id'])->delete();
                ApiDetailModel::where('project_id', $data['project_id'])->delete();
            }

            foreach ($array as $key => $value) {
                $insert = ['name' => $value['name'], 'project_id' => $data['project_id'],
                    'account_id' => $data['account_id']];
                $id = ApiModel::insert($insert); // 插入类别
                foreach ($array[$key]['list'] as $k => $v) {
                    $path_data = ElementHelper::getYapiParam($v['req_params']);
                    $complete = ($v['status'] == 'done') ? 1 : 0;
                    $insert2 = ['pid' => $id, 'name' => $v['title'], 'project_id' => $data['project_id'],
                        'account_id' => $data['account_id'], 'method' => strtolower($v['method']),
                        'path_data' => json_encode($path_data), 'complete' => $complete, 'uri' => $v['path']];
                    $api_id = ApiModel::insert($insert2);
                    $header_data = ElementHelper::getYapiParam($v['req_headers']);
                    $query_data = [];
                    if ($insert2['method'] == 'get') {
                        $query_data = ElementHelper::getYapiParam($v['req_query'], 'check');
                    }

                    $form_data = [];
                    if ($insert2['method'] == 'post') {
                        $form_data = ElementHelper::getYapiParam($v['req_body_form'], 'check');
                    }

                    $data2 = ['put_data' => [], 'put_doc' => []];
                    if ($insert2['method'] == 'put') {
                        if (isset($v['req_body_type'])) {
                            if ($v['req_body_type'] == 'raw') {
                                if (isset($v['req_body_other'])) {
                                    $data2 = ElementHelper::getPutData($v['req_body_other'], 'raw');
                                }
                            } else if ($v['req_body_type'] == 'json') {
                                $array2 = json_decode($v['req_body_other'], true);
                                if (isset($array2['properties'])) {
                                    $data2 = ElementHelper::getPutData($array2['properties'], 'json');
                                }
                            }
                        }
                    }

                    $return_data = [];
                    if (isset($v['res_body'])) {
                        $array3 = json_decode($v['res_body'], true);
                        if (isset($array3['properties'])) {
                            $data3 = ElementHelper::getPutData($array3['properties']);
                            $return_data = $data3['put_doc'];
                        }
                    }

                    $insert3 = [
                        'api_id' => $api_id,
                        'header_data' => json_encode($header_data),
                        'query_data' => json_encode($query_data),
                        'form_data' => json_encode($form_data),
                        'put_data' => json_encode($data2['put_data']),
                        'put_doc' => json_encode($data2['put_doc']),
                        'return_data' => json_encode($return_data),
                        'utime' => $v['up_time'],
                        'comment' => $v['desc'] ?? '',
                        'project_id' => $data['project_id']
                    ];
                    ApiDetailModel::insert($insert3);
                }
            }
        }
    }

    public static function getGroup($data, $type)
    {
        if ($data['account_id'] == 1 || $type) { // 管理员直接获取所有一级项目组
            $query = ProjectModel::where('pid', 0);
            if ($type) {
                $query->where('id', '<>', 7);
            }

            $data2 = $query->get();
        } else { // 普通人员需要从会员表中获取有权限的项目组
            $data2[] = ProjectModel::where('id', 7)->find();
            $rs = ProjectAccountModel::field(['distinct project_pid,raw'])->where('account_id', $data['account_id'])->get();
            foreach ($rs as $row) {
                $data2[] = ProjectModel::where('id', $row['project_pid'])->find();
            }
        }

        return $data2;
    }

    public static function getInvite($data)
    {
        $query = ProjectAccountModel::field(['project_id']);
        if ($data['account_id'] != 1) {
            $query->where('account_id', $data['account_id']);
        }

        if (isset($data['pid'])) {
            $query->where('project_pid', $data['pid']);
        }
        $ids = $query->column();
        return ProjectModel::where('id', 'in', $ids)->get();
    }

    public static function save($data)
    {
        $count = ProjectModel::where('name', $data['name'])->where('pid', $data['pid'])->count();
        if ($count < 1) {
            if ($data['pid'] > 0) {
                $data['icon_color'] = ElementHelper::randomColor();
                $data['icon'] = ElementHelper::randomIcon();
            }

            $rs = ProjectModel::save($data);
            if ($rs && ($data['pid'] > 0)) {
                $insert = ['project_pid' => $data['pid'], 'project_id' => $rs, 'account_id' => $data['account_id'], 'group' => 1];
                ProjectAccountModel::save($insert);
            }

            return $rs;
        } else {
            throw new BadRequestHttpException('项目名称已存在');
        }
    }

    public static function update($id, $data)
    {
        if ($id == 7) {
            throw new BadRequestHttpException('不能删除个人空间');
        }
        $count = ProjectModel::where('id', $id)->count();
        if ($count > 0) {
            $group = ProjectAccountModel::field(['group'])->where('account_id', $data['account_id'])->where('project_id', $id)->value();
            if ($data['account_id'] == 1 || $group == 1) {
                unset($data['account_id']);
                $rs = ProjectModel::save($data);
                if ($rs && isset($data['pid'])) {
                    ProjectAccountModel::where('project_id', $id)->update(['project_pid' => $data['pid']]);
                }
            }

        } else {
            throw new BadRequestHttpException('更新内容不存在');
        }
    }

    public static function updateProgress($data)
    {
        return ProjectModel::save($data);
    }

    public static function delete($id)
    {
        $row = ProjectModel::field(['id', 'pid'])->where('id', $id)->find();
        if ($row) {
            $count_pid = ProjectModel::where('pid', $id)->count();
            if ($count_pid < 1) { // 如果没有子项目
                ProjectModel::where('id', $id)->delete();
                if ($row['pid'] > 0) { // 如果是子项目则删除接口
                    $ids = ApiModel::field(['id'])->where('project_id', $id)->column();
                    ApiModel::where('project_id', $id)->delete();
                    ApiDetailModel::where('api_id', 'in', $ids)->delete();
                }
            } else {
                throw new BadRequestHttpException('项目组下还有子项目无法删除');
            }
        } else {
            throw new BadRequestHttpException('删除对象不存在');
        }
    }

    public static function getInfo($id)
    {
        return ProjectModel::where('id', $id)->find();
    }

    public static function getOrganize($account_id)
    {
        $username = AccountModel::field(['username'])->where('id', $account_id)->value();
        if ($username == 'admin' || $username == 'guest') {
            $rs = ProjectModel::where('pid', 0)->get();
        } else {
            $pids = ProjectAccountModel::field(['distinct project_pid'])->where('account_id', $account_id)->column();
            $rs = ProjectModel::where('id', 'in', $pids)->get();
        }

        $array = [];
        foreach ($rs as $row) {
            if ($row['id'] != 7 && $row['id'] != 22) {
                $leader = AccountModel::field(['realname'])->where('id', $row['leader_id'])->value();
                $rs2 = ProjectModel::where('pid', $row['id'])->get();
                $children = [];
                if (count($rs2) > 0) {
                    foreach ($rs2 as $row2) {
                        $rs3 = ProjectAccountModel::field(['account_id'])->where('project_id', $row2['id'])->column();
                        if (in_array($account_id, $rs3) || $account_id == $row['leader_id'] || $account_id == 1 || $username == 'guest') {
                            $rs4 = AccountModel::field(['id', 'realname'])->where('id', 'in', $rs3)->get();

                            $names_array = [];
                            foreach ($rs4 as $row4) {
                                if (!empty($row4['realname'])) {
                                    if (($account_id == $row['leader_id'] && $row4['id'] != $row['leader_id']) || $account_id == 1) {
                                        $id = 'cli_' . $row4['id'];
                                    } else {
                                        $id = 'pro_' . $row4['id'];
                                    }

                                    $status = 'x';
                                    if ($row4['id'] == $account_id) {
                                        $status = 'y';
                                    }

                                    $names_array[] = ['id' => $id, 'name'=> '参与人员', 'title' => $row4['realname'],
                                        'status' => $status];
                                }
                            }

                            if ($account_id == $row['leader_id'] || $account_id == 1) {
                                $id = 'ddu_' . $row2['id'];
                            } else {
                                $id = 'ddd_' . $row2['id'];
                            }

                            $children[] = ['id' => $id, 'name' => $row2['name'],'status' => $row2['status'],
                                'title' => '', 'progress' => $row2['progress'], 'children' => $names_array];
                        }

                    }
                }

                if ($account_id == $row['leader_id'] || $account_id == 1) {
                    $id = 'lea_' . $row['id'];
                } else {
                    $id = 'leu_' . $row['id'];
                }

                $array[] = ['id' => $id, 'name' => $row['name'] . '-' . $row['version'], 'title' => $leader, 'children' => $children,
                    'status' => $row['status'], 'progress' => $row['progress'], 'pro' => 1];
            }
        }

        return $array;
    }
}
