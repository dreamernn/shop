<?php
/**
 * @filesource OrderService.php
 * @brief      OrderService
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Services\Customer;


use App\Services\BaseService;
use App\Models\OrderModel;
use App\Models\CartModel;
use Common\XLYException;

class OrderService extends BaseService {
    /**
     * add order
     * @param $params
     *
     * @return bool
     */
    public function add($params) {
        $cartIdArr = $cartDetail = [];
        if (isset($params['cart_list']) && is_array($params['cart_list'])) {
            foreach ($params['cart_list'] as $val) {
                $cartIdArr[]  = $val['cart_id'];
                $cartDetail[] = ['cart_id' => $val['cart_id'], 'quantity' => $val['quantity'], 'price' => $val['price']];
            }
        }

        unset($params['cart_list']);
        $params['cart_detail'] = json_encode($cartDetail, JSON_UNESCAPED_UNICODE);
        $orderModel            = new OrderModel();

        $orderModel->beginTransaction();
        try {
            $res = $orderModel->add($params);    //add info
            if (!$res) {
                throw new XLYException('edit fail');
            }

            if (!empty($cartIdArr)) {
                $cartModel = new cartModel();
                $cartModel->editStatus(['cart_id' => $cartIdArr], 1);
            }

            $orderModel->commit();
        } catch (XLYException $e) {

            $orderModel->rollBack();

            return false;
        }

        return true;
    }
}