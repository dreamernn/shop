<?php

namespace App\Http\Controllers\Customer;

use Common\Jwt;
use App\Http\Controllers\BaseController;
use App\Services\Customer\CartService;
use Common\XLYException;
use Xly\Register;

class CartController extends BaseController {
    /**
     * list api
     */
    public function list() {
        $params   = $this->getParams();
        $userInfo = Register::get('auth');
        if (!$userInfo) {
            return responseFail(XLYException::USER_NEED_LOGIN_ERROR_MESSAGE, XLYException::USER_NEED_LOGIN_ERROR_CODE);
        }

        $params['user_id'] = $userInfo['user_id'];
        $cartList          = (new CartService())->getListForCustomer($params);

        return responseSuccess('success!', $cartList);
    }

    /**
     * @return array|mixed
     */
    public function getTotalAndPrice() {
        $userInfo = Register::get('auth');
        if (!$userInfo) {
            return responseFail(XLYException::USER_NEED_LOGIN_ERROR_MESSAGE, XLYException::USER_NEED_LOGIN_ERROR_CODE);
        }
        $params['user_id'] = $userInfo['user_id'];
        $data              = (new CartService())->getTotalAndPrice($params);

        return responseSuccess('success!', $data);
    }

    /**
     * add for cart
     */
    public function add() {
        $params = $this->getParams();
        $this->validMulitIsNull(['product_id', 'quantity', 'sku'], $params);
        $userInfo          = Register::get('auth');
        $params['user_id'] = $userInfo['user_id'];
        $cartAddRes        = (new CartService())->add($params);
        if (empty($cartAddRes)) {
            return responseFail(XLYException::CART_ADD_ERROR_MESSAGE, XLYException::CART_ADD_ERROR_CODE);
        }

        return responseSuccess('success!', []);
    }
}