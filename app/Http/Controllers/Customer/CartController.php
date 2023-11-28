<?php
/**
 * @filesource CartController.php
 * @brief      CartController
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Http\Controllers\Customer;

use Common\Jwt;
use App\Http\Controllers\BaseController;
use App\Services\Customer\CartService;
use Common\XLYException;
use Xly\Register;

class CartController extends BaseController {
    /**
     * get cart list
     *
     * @return array|mixed
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
     * get total and totalPrice from data of cart
     *
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
     * add data to cart
     *
     * @return array|mixed
     * @throws \Common\XLYException
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