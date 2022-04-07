<?php

namespace app\cache;

use king\lib\Cache;

class Item extends Cache
{
    public static function set($id, $data)
    {
        return parent::hSet('dict:' . $data['dict_id'], $id, $data['name']);
    }

    public static function get($dict_id, $id)
    {
        return parent::hGet('dict:' . $dict_id, $id);
    }

    public static function delete($dict_id, $id)
    {
        return parent::hDel('dict:' . $dict_id, $id);
    }

    public static function getList($dict_id)
    {
        return parent::hGetAll('dict:' . $dict_id);
    }
}