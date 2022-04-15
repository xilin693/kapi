<?php

namespace app\controller\www;

use king\lib\Response;
use app\validate\Page as PageValidate;
use app\validate\Env as EnvValidate;
use app\service\Env as EnvService;
use app\helper\Login as LoginHelper;

class Env
{
    public function get()
    {
        $data = G();
        PageValidate::check($data);
        EnvValidate::check($data, 'get');
        $rs = EnvService::getList($data);
        Response::sendSuccessJson($rs);
    }

    public function project($project_id)
    {
        $data['account_id'] = LoginHelper::getAccountId();
        $data['project_id'] = $project_id;
        EnvValidate::check($data, 'get');
        $rs = EnvService::getList($data);
        Response::sendSuccessJson($rs);
    }
    
    public function add()
    {
        $data = P();
        EnvValidate::check($data, 'save');
        $rs = EnvService::save($data);
        Response::sendSuccessJson($rs);
    }

    public function edit($id)
    {
        $data = steam($id);
        EnvValidate::check($data, 'update');
        $rs = EnvService::update($id, $data);
        Response::sendSuccessJson($rs);
    }

    public function delete($id)
    {
        EnvValidate::check($id, 'delete');
        EnvService::delete($id);
        Response::sendSuccessJson();
    }
    
    public function detail($id)
    {
        EnvValidate::check($id, 'detail');
        $rs = EnvService::getInfo($id);
        Response::sendSuccessJson($rs);
    }
}
