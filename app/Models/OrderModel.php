<?php
/**
 * @filesource OrderModel.php
 * @brief      OrderModel
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Models;

use App\Models\BaseModel;

class OrderModel extends BaseModel {
    /**
     * tableName
     *
     * @var string
     */
    protected $_tableName = 'orders';

    /**
     * primaryKey
     *
     * @var string
     */
    protected $_primaryKey = 'order_id';

    /**
     * fields
     *
     * @var string[]
     */
    protected $fields = [
        'order_id',
        'user_id',
        'cart_detail',
        'first_name',
        'last_name',
        'email',
        'status',
        'created_at',
    ];

    /**
     * add order
     *
     * @param $id
     * @param $data
     *
     * @return false
     */
    public function add($data) {
        return $this->insertRecord($data, $this->fields);
    }
}