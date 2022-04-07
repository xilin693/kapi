<?php

namespace app\service;

use king\lib\exception\BadRequestHttpException;
use app\model\project as projectModel;
use app\model\Account as AccountModel;
use app\model\Kpi as KpiModel;

class Kpi
{
    public static function getList($data)
    {
        $query = KpiModel::field(['id', 'account_id', 'ctime', 'my_score_sum', 're_score_sum', 'final_score_sum', 'grade']);
        if (isset($data['account_id'])) {
            $query->where('account_id', $data['account_id']);
        }

        if (!empty($data['month'])) {
            $ctime = strtotime($data['month']);
            $query->where('ctime', $ctime);
        }

        if (!empty($data['realname'])) {
            $account_id = AccountModel::field(['id'])->where('realname', $data['realname'])->value();
            $query->where('account_id', $account_id);
        }

        return $query->page($data['per_page'], $data['page']);
    }
    
    public static function save($data)
    {
        $time = strtotime(date('Y-m'));
        $final_score = KpiModel::field(['final_score_sum'])->where('account_id', $data['account_id'])->where('ctime', $time)->value();
        if (!$final_score) {
            if ($data['edit'] == 'my') {
                if ($data['account_id'] != $data['current_account_id']) {
                    throw new BadRequestHttpException('请求用户非法');
                }

                $count = KpiModel::where('account_id', $data['account_id'])->where('ctime', $time)->count();
                if ($count > 0) {
                    throw new BadRequestHttpException('绩效已提交过');
                } else {
                    $data['my_score'] = json_encode($data['my_score']);
                    $data['ctime'] = $time;
                    unset($data['edit'], $data['re_score'], $data['re_score_sum'], $data['current_account_id']);
                    return KpiModel::save($data);
                }
            } elseif ($data['edit'] == 're') {
                $count = projectModel::where('leader_id', $data['current_account_id'])->count();
                if ($count > 0) {
                    $re_score_sum = KpiModel::field(['re_score_sum'])->where('account_id', $data['account_id'])
                        ->where('ctime', $time)->value();
                    if (!$re_score_sum) {
                        $update = ['re_score' => json_encode($data['re_score']), 're_score_sum' => $data['re_score_sum'],
                            're_account_id' => $data['current_account_id']];
                        return KpiModel::where('account_id', $data['account_id'])->where('ctime', $time)->update($update);
                    } else {
                        throw new BadRequestHttpException('该员工复评已提交过');
                    }

                } else {
                    throw new BadRequestHttpException('只有项目经理才能复评');
                }
            }            
        } else {
            throw new BadRequestHttpException('已有终评数据,无法更新');
        }
    }

    public static function saveAll($data)
    {
        $update = [];
        foreach ($data['tableData'] as $row) {
            $update[] = ['id' => intval($row['id']), 'grade' => $row['grade'], 'final_score_sum' => $row['final_score_sum']];
        }

        return KpiModel::batchUpdate($update);
    }
        
    public static function getInfo($account_id)
    {
        $ctime = strtotime(date('Y-m'));
        $row = KpiModel::attr()->where('account_id', $account_id)->where('ctime', $ctime)->find();
        if ($row) {
            $row['my_score'] = json_decode($row['my_score'], true);
            $row['re_score'] = json_decode($row['re_score'], true);
            $row['ctime'] = date('Y-m');
            return $row;
        }
    }
}
