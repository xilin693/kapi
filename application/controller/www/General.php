<?php

namespace app\controller\www;

use \PDO;
use king\lib\Response;
use king\lib\Upload;
use app\model\Api as ApiModel;
use app\model\ApiDetail as ApiDetailModel;
use app\service\Account as AccountService;
use app\validate\Account as AccountValidate;
use app\cache\Login as LoginCache;
use app\service\Role as RoleService;

class General
{
    public function index()
    {
        $data['site'] = 'kapi';
        Response::sendSuccessJson($data);
    }

    public function rest()
    {
        $data = P();
        $config = C('database.rest');
        $dsn = 'mysql:host=' . $config['host'] . ';port=3306;dbname=' . $data['db'] . ';charset=' . $config['charset'];
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        try {
            $pdo = new Pdo($dsn, $config['user'], $config['password'], $options);
            $rs = $pdo->query('SHOW FULL COLUMNS FROM ' . $data['table'])->fetchAll();
            $info = [];
            if ($rs) {
                foreach ($rs as $key => $value) {
                    if ($value['Key'] != 'PRI') {
                        $info[$value['Field']] = $value['Comment'];
                    }
                }
            }

            $array = [];
            $php_rule = '';
            $post_type = isset($data['post_type']) ? 1 : 0;
            if ($post_type) {
                $array['post_data'] = json_encode($info, JSON_UNESCAPED_UNICODE);
                foreach ($info as $key => $value) {
                    $id = rand(100, 10000000);
                    $array['post_doc'][] = ['id' => $id, 'key' => $key, 'value' => "", 'check' => true, 'description' => $value];
                    $php_rule .= '$valid->addRule(\'' . $key . '\', \'\', \'' . $value . '\');' . "\r\n";
                }
            } else {
                foreach ($info as $key => $value) {
                    $array['form_data'][] = ['key' => $key, 'value' => "", 'check' => true, 'submit' => true, 'description' => $value];
                    $php_rule .= '$valid->addRule(\'' . $key . '\', \'\', \'' . $value . '\');' . "\r\n";
                }
            }


            $array['select_url'] = '/' . $data['rest'];
            $array['query_data'] = [
                ['key' => 'page', 'check' => false, 'value' => '1', 'description' => '当前页'],
                ['key' => 'per_page', 'check' => false, 'value' => '10', 'description' => '每页条数']
            ];
            $array['put_data'] = json_encode($info, JSON_UNESCAPED_UNICODE);
            foreach ($info as $key => $value) {
                $id = rand(100, 10000000);
                $array['put_doc'][] = ['id' => $id, 'key' => $key, 'value' => "", 'check' => true, 'description' => $value];
            }

            foreach ($info as $key => $value) {
                $id = rand(100, 10000000);
                $array['return_data'][] = ['id' => $id,'key' => $key, 'type' => "string", 'params' => "", 'description' => $value];
            }
            $array['add_url'] = '/' . $data['rest'];
            $array['edit_url'] = '/' . $data['rest'] . '/:id';
            $array['delete_url'] = '/' . $data['rest'] . '/:id';
            $array['detail_url'] = '/' . $data['rest'] . '/:id';
            $array['status_url'] = '/' . $data['rest'] . '/:id/status';
            $array['status_put_data'] = json_encode(['status' => '']);
            $array['php_rule'] = $php_rule;
            Response::sendSuccessJson($array);
        } catch (\Throwable $e) {
            Response::sendResponseJson(400, '数据获取失败:' . $e->getMessage() );
        }
    }

    public function interface()
    {
        $data = P();
        $account_id = $this->account_id;
        $count = ApiModel::where('project_id', $data['project_id'])->where('pid', 0)->where('name', $data['category'])->count();
        if ($count < 1) {
            $pid = ApiModel::insert(['pid' => 0, 'name' => $data['category'], 'account_id' => $account_id, 'project_id' => $data['project_id']]);
            if ($data['select']) {
                $api_id = ApiModel::insert(['pid' => $pid, 'method' => 'get', 'name' => $data['select_text'], 'uri' => $data['select_url'],
                    'account_id' => $account_id, 'project_id' => $data['project_id']]);
                if ($data['query_data']) {
                    ApiDetailModel::insert(['api_id' => $api_id, 'query_data' => $data['query_data']]);
                }
            }

            if ($data['add']) {
                $api_id = ApiModel::insert(['pid' => $pid, 'method' => 'post', 'name' => $data['add_text'], 'uri' => $data['add_url'],
                    'account_id' => $account_id, 'project_id' => $data['project_id'], 'post_type' => $data['post_type']]);
                if ($data['post_type']) {
                    if ($data['post_data']) {
                        ApiDetailModel::insert(['api_id' => $api_id, 'put_data' => $data['post_data'], 'put_doc' => $data['post_doc']]);
                    }
                } else {
                    if ($data['query_data']) {
                        ApiDetailModel::insert(['api_id' => $api_id, 'form_data' => $data['form_data']]);
                    }
                }

            }

            if ($data['edit']) {
                $api_id = ApiModel::insert(['pid' => $pid, 'method' => 'put', 'name' => $data['edit_text'], 'uri' => $data['edit_url'],
                    'account_id' => $account_id, 'project_id' => $data['project_id'], 'path_data' => $this->getPath($data['edit_url'])]);
                if ($data['put_data']) {
                    ApiDetailModel::insert(['api_id' => $api_id, 'put_data' => $data['put_data'], 'put_doc' => $data['put_doc']]);
                }
            }

            if ($data['delete']) {
                ApiModel::insert(['pid' => $pid, 'method' => 'delete', 'name' => $data['delete_text'], 'account_id' => $account_id,
                    'uri' => $data['delete_url'], 'project_id' => $data['project_id'], 'path_data' => $this->getPath($data['delete_url'])]);
            }

            if ($data['detail']) {
                $api_id = ApiModel::insert(['pid' => $pid, 'method' => 'get', 'name' => $data['detail_text'], 'account_id' => $account_id,
                    'uri' => $data['detail_url'], 'project_id' => $data['project_id'], 'path_data' => $this->getPath($data['detail_url'])]);
                if ($data['return_data']) {
                    ApiDetailModel::insert(['api_id' => $api_id, 'return_data' => $data['return_data']]);
                }
            }

            if ($data['status']) {
                $api_id = ApiModel::insert(['pid' => $pid, 'method' => 'put', 'name' => $data['status_text'], 'account_id' => $account_id,
                    'uri' => $data['status_url'], 'project_id' => $data['project_id'], 'path_data' => $this->getPath($data['status_url'])]);
                if ($data['status_put_data']) {
                    ApiDetailModel::insert(['api_id' => $api_id, 'put_data' => $data['status_put_data']]);
                }
            }
        } else {
            Response::sendResponseJson(400, '类别已存在');
        }
    }

    private function getPath($path)
    {
        $pathes = explode('/', $path);
        $data = [];
        foreach ($pathes as $p) {
            if (isset($p[0]) && $p[0] == ':') {
                $data[] = ['key' => substr($p, 1), 'value' => '', 'description' => ''];
            }
        }

        return json_encode($data);
    }

    public function userInfo()
    {
        $user_id = LoginCache::getId(H('Authorization'));
        AccountValidate::check($user_id, 'detail');
        $rs = AccountService::getInfo($user_id);
        Response::sendSuccessJson($rs);
    }

    public function roles()
    {
        $rs = RoleService::getList();
        Response::sendSuccessJson($rs);
    }

    public function file()
    {
        $upload = Upload::getClass($_FILES);
        $name = $upload->saveFile();
        if (!$name) {
            Response::sendResponseJson(400, '上传失败,' . $upload->getError());
        } else {
            Response::sendSuccessJson(['filename' => $name]);
        }
    }

    public function delFile()
    {
        $file = ROOT_PATH . 'public/' . G('file');
        return unlink($file);
    }
}
