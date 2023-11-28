<?php
/**
 * @filesource CartService.php
 * @brief      CartService
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Services\Customer;

use App\Models\CartModel;

use App\Services\BaseService;
use Common\XLYException;
use Log\CommonLog;

class CartService extends BaseService {
    /**
     * get cart list service
     *
     * @param       $params
     * @param bool  $ifEffective
     * @param false $isAll
     *
     * @return array|mixed
     */
    public function getList($params, $ifEffective = true, $isAll = false) {
        $cartModel = new CartModel();
        if (false === $isAll) {
            $params['is_del'] = 0;
        }

        if ($ifEffective == true) {
            $params['status'] = 0;
        }

        return $cartModel->getList($params);
    }

    /**
     * get cart list for customer
     *
     * @param $params
     *
     * @return array
     */
    public function getListForCustomer($params, $isAll = false) {
        $cartModel = new CartModel();
        if (false === $isAll) {
            $params['is_del'] = 0;
        }

        $params['status'] = 0;
        $cartList         = $cartModel->getListForCustomer($params);

        return $cartList;
    }

    /**
     * get total and totalPrice
     *
     * @param $params
     *
     * @return array
     */
    public function getTotalAndPrice($params) {
        $cartModel        = new CartModel();
        $data             = ['total' => 0, 'total_price' => 0.00];
        $params['is_del'] = 0;
        $params['status'] = 0;
        $cartList         = $cartModel->getListForCustomer($params);
        if ($cartList) {
            foreach ($cartList as $val) {
                $data['total']       += $val['quantity'];
                $data['total_price'] += $val['quantity'] * $val['price'];
            }
        }
        $data['total_price'] = number_format($data['total_price'], 2);

        return $data;
    }

    /**
     * add item to cart
     *
     * @param $params
     *
     * @return bool
     */
    public function add($params) {
        $cartModel = new CartModel();
        $where     = [
            'user_id'    => $params['user_id'],
            'product_id' => $params['product_id'],
            'sku'        => $params['sku'],
            'status'     => 0,
            'is_del'     => 0,
        ];
        $cartInfo  = $cartModel->getInfo($where, 'cart_id');
        if (!empty($cartInfo) && (int)$cartInfo['cart_id'] > 0) {
            $res = $cartModel->updateByMoreParams($where, $params); //update info
        } else {
            $res = $cartModel->add($params);    //add info
        }

        return $res;
    }
}