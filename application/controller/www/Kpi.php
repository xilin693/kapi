<?php

namespace app\controller\www;

use king\lib\Response;
use app\controller\common\Template;
use app\validate\Kpi as KpiValidate;
use app\validate\Page as PageValidate;
use app\service\Kpi as KpiService;
use app\helper\Login as LoginHelper;

class Kpi
{
    private $account_id;

    public function __construct()
    {
        $this->account_id = LoginHelper::getAccountId();
    }
    public function get()
    {
        if ($this->account_id == 1) {
            $data = G();
            PageValidate::check($data);
            $rs = KpiService::getList($data);
            Response::sendResponseJson(200, $rs);
        } else {
            Response::sendResponseJson(400, '权限不足');
        }
    }

    public function me()
    {
        $data = G();
        $data['account_id'] = $this->account_id;
        PageValidate::check($data);
        $rs = KpiService::getList($data);
        Response::sendResponseJson(200, $rs);
    }

    public function add()
    {
        $data = P();
        $data['current_account_id'] = $this->account_id;
        KpiValidate::check($data, 'save');
        $rs = KpiService::save($data);
        Response::sendResponseJson(200, $rs);
    }

    public function all()
    {
        if ($this->account_id == 1) {
            $data = P();
            $rs = KpiService::saveAll($data);
            Response::sendResponseJson(200, $rs);
        } else {
            Response::sendResponseJson(400, '权限不足');
        }
    }

    public function detail($id)
    {
        $rs = KpiService::getInfo($id);
        Response::sendResponseJson(200, $rs);
    }

    public function edit()
    {
        $data = steam();
        $data['re_account_id'] = $this->account_id;
        $rs = KpiService::update($data);
        Response::sendResponseJson(200, $rs);
    }

    public function excel()
    {
        if ($this->account_id == 1) {
            $data = G();
            $data['month'] = $data['month'] ?? date('Y-m');
            $data['per_page'] = 500;
            $data['page'] = 1;
            $rs = KpiService::getList($data);
            foreach ($rs['rs'] as &$row) {
                $row['ctime'] = date('Y-m');
            }
            Response::sendResponseJson(200, $rs['rs']);
        } else {
            Response::sendResponseJson(400, '权限不足');
        }
    }
}
