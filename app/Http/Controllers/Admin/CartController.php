<?php

namespace App\Http\Controllers\Admin;

use Common\Jwt;
use App\Http\Controllers\BaseController;
use App\Services\Admin\CartService;
use Common\XLYException;
use Xly\Register;

class CartController extends BaseController {
    /**
     * list api
     */
    public function list() {
        $params   = $this->getParams();
        $cartList = (new CartService())->getList($params);

        return responseSuccess('success!', $cartList);
    }

    /**
     * add for cart
     */
    public function info() {
        $params = $this->getParams();
        $this->validMulitIsNull(['cart_id'], $params);
        $cartInfo = (new CartService())->getInfo($params);

        return responseSuccess('success!', $cartInfo);
    }
}