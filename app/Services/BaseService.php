<?php

namespace App\Services;

use App\Services\Redis\RedisService;
use Common\XLYException;
use Common\Jwt;

class BaseService {
    protected $fieldArr = [];

    public function setAuthorization($token_data) {
        //生成登录态auth_token
        $jwt        = new Jwt();
        $auth_token = $jwt->encode($token_data);

        return $auth_token;
    }
}
