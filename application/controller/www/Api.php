<?php

namespace app\controller\www;

use app\controller\common\Template;
use king\lib\Response;
use app\validate\Page as PageValidate;
use app\validate\Api as ApiValidate;
use app\service\Api as ApiService;
use app\helper\Login as LoginHelper;
use app\helper\Element as ElementHelper;

class Api
{
    private $items;
    private $count;

    public function get($project_id)
    {
        $data = G();
        $data['project_id'] = $project_id;
        PageValidate::check($data);
        ApiValidate::check($data, 'get');
        $rs = ApiService::getList($data);
        Response::sendSuccessJson($rs);
    }

    public function add()
    {
        $data = P();
        $data['account_id'] = LoginHelper::getAccountId();
        ApiValidate::check($data, 'save');
        $rs = ApiService::save($data);
        Response::sendSuccessJson($rs);
    }

    public function copy()
    {
        $data = P();
        $data['account_id'] = LoginHelper::getAccountId();
        ApiValidate::check($data, 'save');
        $rs = ApiService::copy($data);
        Response::sendSuccessJson($rs);
    }

    public function json()
    {
        $data = P();
        $json = @json_decode($data['json'], true);
        if (!is_array($json)) {
            Response::sendResponseJson(400, '无效的json');
        } else {
            $this->resolveJson($json);
            if ($this->count > 1000) {
                Response::sendResponseJson(400, '超过最大嵌套数');
            } else {
                Response::sendSuccessJson(array_values($this->items));
            }
        }
    }

    public function resolveJson($array)
    {
        $child = [];
        if (isset($array[0])) {
            $array = $array[0];
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $type = $this->getType($value);
                if ($type == 'array' && !isset($value[0])) {
                    $type = 'object';
                }

                if (is_array($value)) {
                    $item = $this->resolveJson($value);
                    if (is_array($item)) {
                        $item = array_values($item);
                    } else {
                        $type = 'array';
                        $item = '';
                    }
                    $child[] = ['id' => $this->getId(), 'key' => $key, 'check' => true, 'type' => $type, 'description' => '', 'children' => $item];
                } else {
                    $child[] = [ 'id' => $this->getId(), 'key' => $key, 'check' => true, 'type' => $type, 'description' => ''];
                }
                $this->count++;
            }

            $this->items = $child;
            return $child;
        }
    }

    private function getId()
    {
        return mt_rand(100, 10000000);
    }

    private function getType($string)
    {
        if (is_numeric($string)) {
            return 'int';
        } else if (is_bool($string)) {
            return 'boolean';
        } elseif (is_array($string)) {
            return 'array';
        } else {
            return 'string';
        }
    }

    public function saveCategory()
    {
        $data = P();
        ApiValidate::check($data, 'save');
        $rs = ApiService::save($data, ['project_id', 'name']);
        Response::sendSuccessJson($rs);
    }

    public function category($project_id)
    {
        $data['project_id'] = $project_id;
        $data['pid'] = 0;
        ApiValidate::check($data, 'get');
        $rs = ApiService::getCategory($data);
        Response::sendSuccessJson($rs);
    }

    public function sort()
    {
        $data = steam();
        $rs = ApiService::saveSort($data);
        Response::sendSuccessJson($rs);
    }

    public function edit($id)
    {
        $data = steam($id);
        ApiValidate::check($data, 'update');
        $data = ElementHelper::removeEmptyKey($data, ['header_data', 'form_data', 'query_data', 'put_doc', 'return_data']);
        $rs = ApiService::update($id, $data);
        Response::sendSuccessJson($rs);
    }

    public function account($project_id)
    {
        $data['project_id'] = $project_id;
        ApiValidate::check($data, 'get');
        $rs = ApiService::getAccountList($data);
        Response::sendSuccessJson($rs);
    }

    public function relative($id)
    {
        $rs = ApiService::getRelativeList(intval($id));
        Response::sendSuccessJson($rs);
    }

    public function copyRelative()
    {
        $data = P();
        $rs = ApiService::copyRelative($data);
        Response::sendSuccessJson($rs);
    }

    public function project($id)
    {
        $rs = ApiService::getApiList($id);
        Response::sendSuccessJson($rs);
    }

    public function setField($id)
    {
        $data = steam($id);
        ApiValidate::check($data, 'update');
        $rs = ApiService::updateField($data, ['complete', 'name', 'pid']);
        Response::sendSuccessJson($rs);
    }

    public function search()
    {
        $data = G();
        PageValidate::check($data);
        ApiValidate::check($data, 'get');
        $rs = ApiService::getList($data);
        Response::sendSuccessJson($rs);
    }

    public function delete($id)
    {
        ApiValidate::check($id, 'delete');
        ApiService::delete($id);
        Response::sendSuccessJson();
    }
    
    public function detail($id)
    {
        ApiValidate::check($id, 'detail');
        $rs = ApiService::getInfo($id);
        Response::sendSuccessJson($rs);
    }

    public function run()
    {
        $data = P();
        $rs = ApiService::runApi($data);
        echo json_encode($rs);
    }
}
