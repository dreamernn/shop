<?php
/**
 * @filesource ProductModel.php
 * @brief      ProductModel
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Models;

use App\Models\BaseModel;

class ProductModel extends BaseModel {
    /**
     * tableName
     *
     * @var string
     */
    protected $_tableName = 'products';

    /**
     * primaryKey
     *
     * @var string
     */
    protected $_primaryKey = 'product_id';

    /**
     * fields
     *
     * @var string[]
     */
    protected $fields = [
        'product_id',
        'sku',
        'name',
        'description',
        'price',
    ];

    /**
     * get list
     *
     * @param       $params
     * @param array $fields
     *
     * @return array|mixed
     */
    public function getList($params = [], $fields = []) {
        $fields   = implode(',', empty($fields) ? $this->fields : $fields);
        $dataList = $this->search($params, 0, 0, $fields);
        if (empty($dataList)) {
            return [];
        }

        return $dataList;
    }

    /**
     * update product
     *
     * @param $id
     * @param $data
     *
     * @return bool
     */
    public function updateProduct($id, $data) {
        return $this->updateByParams($this->_primaryKey, $id, $data);
    }
}