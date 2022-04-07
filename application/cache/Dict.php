<?php

namespace app\cache;

use king\lib\Cache;

class Dict extends Cache
{
    protected static $connection = 'cache.redis';

    public static function set($id, $data)
    {
        return parent::hSet('dicts', $id, $data['type'] . ':' . $data['name']);
    }

    public static function get($id, $field = '')
    {
        $value = parent::hGet('dicts', $id);
        if ($field) {
            $values = explode(':', $value);
            return ($field == 'type') ? $values[0] : $values[1];
        } else {
            return $value;
        }
    }

    public static function getAll($id)
    {
        return parent::hGetAll('dict:' . $id);
    }

    public static function delete($id)
    {
        return parent::hDel('dicts', $id);
    }
}