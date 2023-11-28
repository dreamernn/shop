<?php
/**
 * @filesource ProductController.php
 * @brief      ProductController
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Http\Controllers\Customer;

use App\Services\Customer\CartService;
use Common\Jwt;
use App\Http\Controllers\BaseController;
use App\Services\Customer\ProductService;
use Common\XLYException;
use Xly\Register;

class ProductController extends BaseController {
    /**
     * get product list
     *
     * @return array|mixed
     */
    public function list() {
        $params      = $this->getParams();
        $userInfo    = Register::get('auth');
        $productList = (new ProductService())->getList($params);
        if (!empty($productList)) {
            $customerCartList = [];
            $cartList         = (new CartService())->getList(['user_id' => $userInfo['user_id']]);
            if (!empty($cartList)) {
                foreach ($cartList as $val) {
                    $customerCartList[$val['product_id']]['quantity'][] = $val['quantity'];
                }
            }

            foreach ($productList as &$val) {
                $val['cart_info']['quantity'] = isset($customerCartList[$val['product_id']]) ? array_sum($customerCartList[$val['product_id']]['quantity']) : 0;
                if ($val['sku']) {
                    $val['skuList'] = explode(',', $val['sku']);
                }
            }
        }

        return responseSuccess('success!', $productList);
    }
}