<?php

namespace App\Services\Admin;

use App\Models\CartModel;

use App\Services\BaseService;
use Common\XLYException;
use Common\Logger;

class CartService extends BaseService {

    public function getInfo($params) {
        $cartModel = new CartModel();
        $cartList = $cartModel->getInfoForAdmin($params);

        return $cartList;
    }

    /**
     * get cart list
     *
     * @param $params
     *
     * @return array
     */
    public function getList($params) {
        $cartModel = new CartModel();
        $cartList = $cartModel->getListForAdmin($params);

        return $cartList;
    }

    /**
     * @param $params
     */
    public function add($params) {
        $cartModel = new CartModel();
        $res = $cartModel->add($params);
        return $res;
    }
}