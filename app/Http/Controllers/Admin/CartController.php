<?php
/**
 * @filesource CartController.php
 * @brief      CartController
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Http\Controllers\Admin;

use Common\Jwt;
use App\Http\Controllers\BaseController;
use App\Services\Admin\CartService;
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
        $cartList = (new CartService())->getList($params);

        return responseSuccess('success!', $cartList);
    }

    /**
     * get cart info
     *
     * @return array|mixed
     * @throws \Common\XLYException
     */
    public function info() {
        $params = $this->getParams();
        $this->validMulitIsNull(['cart_id'], $params);
        $cartInfo = (new CartService())->getInfo($params);

        return responseSuccess('success!', $cartInfo);
    }
}