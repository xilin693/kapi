<?php

namespace app\controller\www;

use king\lib\Response;
use app\validate\Page as PageValidate;
use app\validate\Member as MemberValidate;
use app\service\Member as MemberService;
use app\helper\Login as LoginHelper;
use app\controller\common\Template;

class Member
{
    private $account_id;

    public function __construct()
    {
        $this->account_id = LoginHelper::getAccountId();
    }

    public function get($project_id = 0)
    {
        $data = G();
        $data['project_id'] = $project_id;
        PageValidate::check($data);
        MemberValidate::check($data, 'get');
        $rs = MemberService::getList($data);
        Response::sendResponseJson(200, $rs);
    }
    
    public function add()
    {
        $data = P();
        MemberValidate::check($data, 'save');
        $rs = MemberService::save($data);
        Response::sendResponseJson(200, $rs);
    }

    public function edit($id)
    {
        $data = steam($id);
        $data['account_id'] = $this->account_id;
        MemberValidate::check($data, 'update');
        $rs = MemberService::update($id, $data);
        Response::sendResponseJson(200, $rs);
    }

    public function delete()
    {
        $data = G();
        $data['current_account_id'] = $this->account_id;
        MemberService::delete($data);
        Response::sendResponseJson(200);
    }

    public function project()
    {
        $data = G();
        MemberValidate::check($data, 'project');
        $rs = MemberService::getInfo($data);
        Response::sendResponseJson(200, $rs);
    }
}
