<?php

namespace App\Services\Admin;

use App\Models\ProductModel;

use App\Services\BaseService;
use Common\XLYException;
use Common\Logger;

class ProductService extends BaseService {

    /**
     * get product list
     *
     * @param $params
     *
     * @return array
     */
    public function getList($params) {
        $productModel = new ProductModel();
        $productList  = $productModel->getList($params);

        return $productList;
    }

    /**
     * @param $params
     *
     * @return bool
     */
    public function update($params) {
        $productModel = new ProductModel();
        $productId    = $params['product_id'];
        $res          = $productModel->updateProduct($productId, $params);
        return $res;
    }
}