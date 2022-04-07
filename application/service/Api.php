<?php

namespace app\service;

use king\lib\exception\BadRequestHttpException;
use app\model\Api as ApiModel;
use app\model\ApiDetail as ApiDetailModel;
use app\model\Account as AccountModel;
use app\cache\Api as ApiCache;
use app\helper\Menu as MenuHelper;
use app\helper\Element as ElementHelper;
use app\model\Env as EnvModel;
use king\lib\Request;
use king\lib\Response;

class Api
{
    public static function getList($data)
    {
        $query = ApiModel::attr()->where('project_id', $data['project_id'])->where('pid', '>', 0);
        if (!empty($data['account_id'])) {
            $query->where('account_id', $data['account_id']);
        }

        if (!empty($data['uri'])) {
            $source_uri = trim($data['uri']);
            $uri = '/' . ltrim($source_uri, '/');
            $query->andWhere(function ($query) use ($uri, $source_uri) {
                $query->where('uri', 'like', '%' . $uri . '%');
                $query->orWhere('name', 'like', '%' . $source_uri . '%');
            });
        }

        if (!empty($data['method'])) {
            $query->where('method', $data['method']);
        }

        return $query->page($data['per_page'], $data['page']);
    }

    public static function getApiList($id)
    {
        $rs = ApiModel::field(['name as label', 'id', 'pid'])->where('project_id', $id)->order(['sort' => 'asc'])->get();
        $tree = MenuHelper::getTree($rs, 'children');
        return $tree;
    }

    public static function getAccountList($data)
    {
        $rs = ApiModel::field(['distinct account_id,raw'])->where('project_id', $data['project_id'])
            ->where('account_id', '>', 0)->column();
        $rs2 = AccountModel::field(['id', 'username', 'realname'])->where('id', 'in', $rs)->get();
        return $rs2;
    }

    public static function save($data, $allow = [])
    {
        $pid = $data['pid'] ?? 0;
        if (!empty($data['uri'])) {
            $data['uri'] = ElementHelper::addSlash($data['uri']);
        }

        $count = ApiModel::where('project_id', $data['project_id'])->where('pid', $pid)->where('name', $data['name'])->count();
        if ($count > 0) {
            throw new BadRequestHttpException('名称已存在');
        } else {
            unset($data['category'], $data['realname'], $data['complete_text']);
            return ApiModel::save($data, $allow);
        }
    }

    public static function copy($data)
    {
        $row = ApiModel::where('id', $data['source_id'])->find();
        if ($data['pid'] == 0) { // pid为0表示是复制的是接口分类
            $row['project_id'] = $data['project_id'];
            $row['account_id'] = $data['account_id'];
            $row['name'] = $data['name'];
            unset($row['id']);
            $pid = self::save($row, ApiModel::$fields);
            $rs = ApiModel::where('pid', $data['source_id'])->get();
            foreach ($rs as $row2) {
                $row2['pid'] = $pid;
                $row2['project_id'] = $data['project_id'];
                $row2['account_id'] = $data['account_id'];
                $source_id = $row2['id'];
                unset($row2['id']);
                $insert_id = ApiModel::save($row2, ApiModel::$fields);
                self::saveDetail($insert_id, $source_id);
            }
        } else {
            unset($row['id']);
            $row['pid'] = $data['pid'];
            $row['account_id'] = $data['account_id'];
            $row['name'] = $data['name'];
            $insert_id = self::save($row);
            self::saveDetail($insert_id, $data['source_id']);
            return $insert_id;
        }
    }

    private static function saveDetail($new_api_id, $source_api_id)
    {
        $row = ApiDetailModel::where('api_id', $source_api_id)->find();
        $row['api_id'] = $new_api_id;
        return ApiDetailModel::insert($row);
    }

    public static function runApi($data)
    {
        $codes = ['400' => 'Bad Request', '500' => 'Internal Server Error', '401' => 'Unauthorized', '200' => 'Success'];
        $env_data = EnvModel::where('id', $data['env_id'])->find();
        $uri = ltrim($data['uri'], '/');
        $path_data = @json_decode($data['path_data'], true);
        if (count($path_data) > 0) {
            foreach ($path_data as $path) {
                $uri = str_replace(':' . $path['key'], $path['value'], $uri);
            }
        }

        $url = $env_data['url_prefix'] . $env_data['domain'] . '/' . $uri;
        $header = ElementHelper::getParam($data['header_data']);
        $query_string = '';
        if ($data['method'] == 'get' || $data['method'] == 'delete') {
            $qs = ElementHelper::getParam($data['query_data']);
            if (count($qs) > 0) {
                $query_string = '?' . http_build_query($qs);
            }
            $req = new Request($url . $query_string, $data['method']);
        } else {
            $req = new Request($url, $data['method']);
            if ($data['method'] == 'post') {
                $req->body = ElementHelper::getParam($data['form_data']);
            } elseif ($data['method'] == 'put') {
                $req->body = $data['put_data'];
            }  else {
                $return['code'] = '400';
                $return['code_text'] = '请求方式错误';
                $return['data'] = '';
                return $return;
            }
        }

        $req->header = $header;
        $req->sendRequest();
        $info = $req->getResponseInfo();
        $return['code'] = $info['http_code'];
        if (empty($codes[$return['code']])) {
            $return['code_text'] = 'No Response';
        } else {
            $return['code_text'] = $codes[$return['code']];
        }

        $return['data'] = $req->getResponseBody();
        return $return;
    }

    public static function saveSort($data)
    {
        if (!empty($data['arr'])) {
            foreach ($data['arr'] as $sort => $id) {
               ApiModel::where('id', $id)->update(['sort' => $sort]);
            }

            return true;
        }
    }

    public static function getCategory($data)
    {
        $rs = ApiModel::field(['id', 'name'])->where($data)->get();
        return $rs;
    }

    public static function update($id, $data)
    {
        $count = ApiModel::where('id', $id)->count();
        if ($count > 0) {
            $uri = ElementHelper::addSlash($data['uri']);
            $api_update = ['method' => $data['method'], 'post_type' => $data['post_type'], 'uri' => $uri, 'id' => $data['id'], 'path_data' => $data['path_data']];
            ApiModel::save($api_update);
            if (!empty($data['put_data']) && $data['put_data'] != 'null' && $data['method'] == 'put') {
                $puts = json_decode($data['put_data']);
                if (!$puts) {
                    throw new BadRequestHttpException('raw参数必须为正确的Json');
                }
            }
            
            $api_detail_update = [
                'api_id' => $data['id'],
                'header_data' => $data['header_data'],
                'return_data' => $data['return_data'],
                'form_data' => $data['form_data'] ?? [],
                'put_data' => $data['put_data'] ?? '',
                'put_doc' => $data['put_doc'] ?? [],
                'query_data' => $data['query_data'] ?? [],
                'comment' => ($data['comment'] ?? ''),
                'route_url' => $data['route_url'] ?? '',
                'utime' => time()
            ];
            ApiDetailModel::replace($api_detail_update);
            return true;
        } else {
            throw new BadRequestHttpException('更新内容不存在');
        }
    }

    public static function getRelativeList($id)
    {
        $pid = ApiModel::field(['pid'])->where('id', $id)->value();
        if ($pid > 0) {
            return ApiModel::field(['id', 'name'])->where('pid', $pid)->where('method', 'post')->get();
        }
    }

    public static function copyRelative($data)
    {
        $form_data = ApiDetailModel::field(['form_data'])->where('api_id', intval($data['new_id']))->value();
        $rs = @json_decode($form_data, true);
        $put_data = [];
        if (is_array($rs)) {
            foreach ($rs as $row) {
                $put_data[$row['key']] = $row['value'];
            }

            return $put_data;
        } else {
            return null;
        }
    }

    public static function updateField($data, $allow)
    {
        $count = ApiModel::where('id', $data['id'])->count();
        if ($count > 0) {
            return ApiModel::save($data, $allow);
        } else {
            throw new BadRequestHttpException('更新内容不存在');
        }
    }

    public static function delete($id)
    {
        $children_num = ApiModel::where('pid', $id)->count();
        if ($children_num > 0) {
            throw new BadRequestHttpException('该分类下还有接口,无法删除');
        }

        $count = ApiModel::where('id', $id)->count();
        if ($count > 0) {
            return ApiModel::where('id', $id)->delete();
        } else {
            throw new BadRequestHttpException('删除对象不存在');
        }
    }

    public static function getInfo($id)
    {
        $row = ApiModel::attr()->where('id', $id)->find();
        if ($row) {
            $row2 = ApiDetailModel::where('api_id', $id)->find();
            return array_merge($row, ($row2 ?: []));
        }
    }
}
