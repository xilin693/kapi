<?php

namespace app\controller\www;

use app\model\ProjectAccount;
use king\lib\Response;
use app\validate\Project as ProjectValidate;
use app\service\Project as ProjectService;
use app\helper\Login as LoginHelper;
use king\lib\File;

class Project
{
    private $account_id;

    public function __construct()
    {
        $this->account_id = LoginHelper::getAccountId();
    }

    public function get()
    {
        $data = G();
        $data['account_id'] = $this->account_id;
        $rs = ProjectService::getList($data);
        Response::sendResponseJson(200, $rs);
    }

    public function group($type = '')
    {
        $data['account_id'] = $this->account_id;
        $rs = ProjectService::getGroup($data, $type);
        Response::sendResponseJson(200, $rs);
    }

    public function invite($pid = null)
    {
        if (isset($pid)) {
            $data['pid'] = $pid;
        }

        $data['account_id'] = $this->account_id;
        $rs = ProjectService::getInvite($data);
        Response::sendResponseJson(200, $rs);
    }

    public function export()
    {
        $data = P();
        $data['account_id'] = $this->account_id;
        ProjectValidate::check($data, 'export');
        $rs = ProjectService::exportJson($data);
        echo $rs;
    }

    public function organize()
    {
        $org = ['id' => 1, 'name' => '研发中心', 'title' => '', 'status' => 'x'];
        $org['children'] = ProjectService::getOrganize($this->account_id);
        Response::sendResponseJson(200, $org);
    }

    public function importJson()
    {
        $data = P();
        $data['account_id'] = $this->account_id;
        ProjectValidate::check($data, 'json');
        $rs = [];
        switch ($data['type']) {
            case 1: {
                $rs = ProjectService::importJson($data);
                break;
            }
            case 2: {
                $rs = ProjectService::importYapiJson($data);
                break;
            }
            case 3: {
                $rs = ProjectService::importPostmanJson($data);
                break;
            }
            default:
                break;
        }

        Response::sendResponseJson(200, $rs);
    }

    public function add()
    {
        $data = P();
        $data['pid'] = $data['pid'] ?? 0;
        $data['account_id'] = $this->account_id;
        ProjectValidate::check($data, 'save');
        $rs = ProjectService::save($data);
        Response::sendResponseJson(200, $rs);
    }

    public function edit($id)
    {
        $data = steam($id);
        $data['account_id'] = $this->account_id;
        ProjectValidate::check($data, 'update');
        $rs = ProjectService::update($id, $data);
        Response::sendResponseJson(200, $rs);
    }

    public function progress($id)
    {
        $data = steam($id);
        // $data['leader_id'] = $this->account_id;
        ProjectValidate::check($data, 'update');
        $rs = ProjectService::updateProgress($data);
        Response::sendResponseJson(200, $rs);
    }

    public function delete($id)
    {
        if ($this->account_id != 1) {
            exit;
        }

        $data['id'] = $id;
        ProjectValidate::check($data, 'delete');
        ProjectService::delete($id);
        Response::sendResponseJson(200);
    }

    public function detail($id)
    {
        $data['id'] = $id;
        ProjectValidate::check($data, 'detail');
        $rs = ProjectService::getInfo($id);
        Response::sendResponseJson(200, $rs);
    } 
}
