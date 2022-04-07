<?php

namespace app\helper;

class Element
{
    public static function randomColor()
    {
        mt_srand();
        $colors = ['#00a2ae', '#f5317f', '#3d546f', '#7266e6', '#ffbf00', '#2395f1', '#00a854', '#bfbfbf', '#0099cc',
            '#99cccc','#cc9999', '#ffcccc', '#003366', '#cccc00', '#99cc00', '#99cc99', '#ccccff', '#663300',
            '#9933cc', '#cccc66', '#339933', '#99ccff', '#66cccc', '#3399cc', '#0099cc', '#996600', '#666699',
            '#ffcc99', '#006699', '#663366', ];
        return $colors[mt_rand(0, count($colors) - 1)];
    }

    public static function randomIcon()
    {
        mt_srand();
        $icons = ['star-on', 's-goods', 's-help', 's-tools', 'picture', 's-home', 's-data', 'menu', 's-shop', 's-order',
            's-platform', 'camera-solid', 'message-solid', 's-promotion', 's-ticket', 's-management', 's-operation',
            's-claim', 's-cooperation', 's-marketing', 'coordinate', 'pie-chart', 'film', 'receiving', 'collection',
            'sell', 'present', 'money', 'price-tag', 'news', 'set-up', 'trophy', 'timer', 'medal', 'basketball',
            'orange', 'lollipop', 'goblet-square-full', 'ice-cream', 'cold-drink', 'discover', 'watch-1', 'sunset',
            'chicken', 'food', 'dish', 'sunny', 'truck', 'bicycle', 'sunrise-1', 'lightning'];
        return $icons[mt_rand(0, count($icons) - 1)];
    }

    public static function getCorrectParam($data)
    {
        switch ($data['method']) {
            case 'post':
                $field = 'form_data';
                break;
            case 'put':
                $field = 'put_doc';
                break;
            default:
                $field = 'query_data';
                break;
        }

        return $field;
    }

    public static function removeEmptyKey($data, $fields = [])
    {
        foreach ($fields as $field) {
            if (!empty($data[$field]) && $data[$field] != 'null') {
                $rs = json_decode($data[$field], true);
                $new_rs = [];
                foreach ($rs as $value) {
                    if (isset($value['key']) && $value['key'] != '') {
                        $new_rs[] = $value;
                    }
                }

                $data[$field] = json_encode($new_rs);
            }
        }

        return $data;
    }

    public static function getYApiParam($data, $extend = '')
    {
        $new_data = [];
        if (count($data) > 0) {
            foreach ($data as $key => $v) {
                if ($extend) {
                    $new_data[$key]['check'] = ($v['required'] == 0) ? false : true;
                }

                $new_data[$key]['key'] = $v['name'];
                $new_data[$key]['value'] = $v['example'] ?? ($v['value'] ?? '');
                $new_data[$key]['submit'] = true;
                $new_data[$key]['description'] = $v['desc'] ?? '';
            }
        }

        return $new_data;
    }

    private static function getId()
    {
        return mt_rand(100, 10000000);
    }

    public static function getPutData($array, $type = 'json')
    {
        $data = ['put_data' => [], 'put_doc' => []];
        if (!is_array($array)) {
            $array = json_decode($array, true);
        }

        if (is_array($array) && count($array) > 0) {
            if ($type == 'raw') {
                $data['put_data']  = $array;
                $data['put_doc'] == [];
            } else {
                foreach ($array as $key => $value) {
                    if ($key == 'rs' && is_array($value)) {
                        $children = [];
                        if (isset($value['items']['properties'])){
                            foreach ($value['items']['properties'] as $k => $v) {
                                $children[] = ['id' => self::getId(), 'key' => $k, 'param' => ($v['title'] ?? ''), 'type' => 'string',
                                    'description' => ($v['description'] ?? '')];
                            }
                        }

                        $data['put_doc'][] = ['id' => self::getId(), 'key' => $key, 'param' => ($value['title'] ?? ''), 'type' => 'object',
                            'description' => ($value['description'] ?? ''), 'children' => $children];
                    } else {
                        $data['put_doc'][] = ['id' => self::getId(), 'key' => $key, 'param' => ($value['title'] ?? ''), 'type' => $value['type'],
                            'description' => ($value['description'] ?? '')];
                    }

                    $data['put_data'][$key] = ($value['title'] ?? '');
                }
            }
        }

        return $data;
    }

    public static function getParam($param, $field = 'value')
    {
        $qs_data = [];
        if (!empty($param)) {
            $qs = json_decode($param, true);
            foreach ($qs as $value) {
                if (!empty($value['submit'])) {
                    $qs_data[$value['key']] = $value[$field];
                }
            }
        }

        return $qs_data;
    }

    public static function addSlash($uri)
    {
        return '/' . ltrim($uri, '/');
    }

    public static function getPathData($uri)
    {
        $uris = explode('/', $uri);
        $path = [];
        if (count($uris) > 0) {
            foreach ($uris as $segment) {
                if (substr($segment,0,1) == ':') {
                    $path[]= ['key' => substr($segment, 1), 'value' => '', 'description' => ''];
                }
            }
        }

        return json_encode($path);
    }
}
