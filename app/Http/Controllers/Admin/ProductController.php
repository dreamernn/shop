<?php
/**
 * @filesource ProductController.php
 * @brief      ProductController
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Http\Controllers\Admin;

use Common\Jwt;
use App\Http\Controllers\BaseController;
use App\Services\Admin\ProductService;
use Common\XLYException;
use Xly\Register;

class ProductController extends BaseController {
    /**
     * get product list
     *
     * @return array|mixed
     */
    public function list() {
        $params = $this->getParams();
//        $userInfo = Register::get('auth');
        $productList = (new ProductService())->getList($params);

        return responseSuccess('success!', $productList);
    }

    /**
     * update product
     *
     * @return array|mixed
     * @throws \Common\XLYException
     */
    public function edit() {
        $params = $this->getParams();
        $this->validMulitIsEmpty(['product_id', 'sku', 'name', 'description', 'price'], $params);
        $params['price'] = number_format($params['price'], 2);
        $productRes      = (new ProductService())->update($params);
        if (empty($productRes)) {
            return responseFail(XLYException::PRODUCT_EDIT_ERROR_MESSAGE, XLYException::PRODUCT_EDIT_ERROR_CODE);
        }

        return responseSuccess('success!', []);
    }
}