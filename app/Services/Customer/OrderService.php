<?php

namespace App\Services\Customer;


use App\Services\BaseService;
use App\Models\OrderModel;
use App\Models\CartModel;
use Common\XLYException;
use Common\Logger;

class OrderService extends BaseService {
    /**
     * @param $params
     */
    public function add($params) {
        $cartIdArr = $cartDetail = [];
        foreach ($params['cart_list'] as $val) {
            $cartIdArr[]  = $val['cart_id'];
            $cartDetail[] = ['cart_id' => $val['cart_id'], 'quantity' => $val['quantity'], 'price' => $val['price']];
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

            $cartModel = new cartModel();
            $cartModel->editStatus(['cart_id' => $cartIdArr], 1);
            $orderModel->commit();
        } catch (XLYException $e) {

            $orderModel->rollBack();

            return false;
        }

        return true;
    }
}