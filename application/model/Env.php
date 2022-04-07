<?php

namespace app\model;

use king\Model;

class Env extends Model
{
    protected static $table = 'project_env';
    public static $fields = ['id', 'project_id', 'domain', 'global_header', 'global_cookie',
        'private', 'account_id', 'url_prefix', 'name'];
}
