<?php

namespace App\Http\Controllers\Customer;

use App\Services\Customer\OrderService;
use Common\Jwt;
use App\Http\Controllers\BaseController;
use App\Services\Customer\CartService;
use Common\XLYException;
use Xly\Register;

class OrderController extends BaseController {
    /**
     * add for order
     */
    public function add() {
        $params = $this->getPostParams();
        if ($params) {
            $params = json_decode($params, true);
        } else {
            return responseFail(XLYException::ERROR_MESSAGE_PARAMETER_WRONG, XLYException::ERROR_CODE_PARAMETER_WRONG);
        }

        $this->validMulitIsNull(['first_name', 'last_name', 'email'], $params);
        $this->validMulitIsEmpty(['cart_list'], $params);
        $this->validEmail($params['email']);
        $userInfo          = Register::get('auth');
        $params['user_id'] = $userInfo['user_id'];
        $orderAddRes       = (new OrderService())->add($params);
        if (empty($orderAddRes)) {
            return responseFail(XLYException::ORDER_ADD_ERROR_MESSAGE, XLYException::ORDER_ADD_ERROR_CODE);
        }

        return responseSuccess('success!', []);
    }
}