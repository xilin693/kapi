<?php

namespace app\validate;

use king\lib\Valid;

class Kpi
{
    public static function check($data, $current = '')
    {
        $data = is_array($data) ? $data : ['id' => $data];
        $valid = Valid::getClass($data, $current);
        $scene = [];
        if ($data['edit'] == 'my') {
            $scene = [
                'save' => ['account_id', 'my_score', 'my_score_sum'],
            ];
        } elseif ($data['edit'] == 're') {
            $scene = [
                'save' => ['account_id', 're_score', 're_score_sum'],
            ];
        }

        $valid->setScene($scene);
        $valid->addRule('account_id', 'required|checkId', '用户账号');
        $valid->addRule('my_score', 'required|checkScore', '分数');
        $valid->addRule('my_score_sum', 'required|checkSum', '总分');
        $valid->addRule('re_score', 'required|checkScore', '分数');
        $valid->addRule('re_score_sum', 'required|checkSum', '总分');
        $valid->response();
    }
}
