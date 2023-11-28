<?php
/**
 * @filesource CartService.php
 * @brief      CartService
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Services\Admin;

use App\Models\CartModel;

use App\Services\BaseService;
use Common\XLYException;
use Common\Logger;

class CartService extends BaseService {

    public function getInfo($params) {
        $cartModel = new CartModel();
        $cartList  = $cartModel->getInfoForAdmin($params);

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
        $cartList  = $cartModel->getListForAdmin($params);

        return $cartList;
    }

    /**
     * add item to cart
     *
     * @param $params
     *
     * @return false
     */
    public function add($params) {
        $cartModel = new CartModel();
        $res       = $cartModel->add($params);

        return $res;
    }
}