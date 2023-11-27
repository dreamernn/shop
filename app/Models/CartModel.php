<?php

namespace App\Models;

use App\Models\BaseModel;

class CartModel extends BaseModel {
    /**
     * tableName
     *
     * @var string
     */
    protected $_tableName = 'carts';

    /**
     * primaryKey
     *
     * @var string
     */
    protected $_primaryKey = 'cart_id';

    /**
     * fields
     *
     * @var string[]
     */
    protected $fields = [
        'cart_id',
        'user_id',
        'product_id',
        'quantity',
        'sku',
        'status',
        'created_at',
    ];

    /**
     * get info from list
     *
     * @param array $params
     * @param array $fields
     *
     * @return mixed
     */
    public function getInfo($params = [], $fields = '*') {
        $cartList = $this->search($params, 0, 0, $fields);

        return !empty($cartList) ? $cartList[0] : [];
    }

    /**
     * get list
     *
     * @param array $params
     * @param array $fields
     *
     * @return mixed
     */
    public function getList($params = [], $fields = '*') {
        $cartList = $this->search($params, 0, 0, $fields);

        return !empty($cartList) ? $cartList : [];
    }

    /**
     * get info for admin
     *
     * @param       $params
     * @param array $fields
     *
     * @return array|mixed
     */
    public function getInfoForAdmin($params = [], $fields = []) {
        $fields = empty($fields) ? $this->fields : $fields;
        foreach ($fields as &$field) {
            $field = 'carts.'.$field;
        }
        $fields[] = 'carts.is_del,carts.updated_at';
        $fields[] = 'products.sku, products.name, products.description, products.price';
        $fields[] = 'users.user_id, users.username';
        $fields   = implode(',', $fields);
        $table    = $this->_tableName;
        $sql      =
            "select {$fields} from {$table} 
            LEFT JOIN products ON  {$table}.product_id = products.product_id 
            LEFT JOIN users ON  {$table}.user_id = users.user_id 
            WHERE {$table}.cart_id = ?";

        $dataList = $this->secureQuery($sql, $params);
        if (empty($dataList)) {
            return [];
        }

        return $dataList[0];
    }


    /**
     * get list for admin
     *
     * @param       $params
     * @param array $fields
     *
     * @return array|mixed
     */
    public function getListForAdmin($params = [], $fields = []) {
        $fields = empty($fields) ? $this->fields : $fields;
        foreach ($fields as &$field) {
            $field = 'carts.'.$field;
        }
        $fields[] = 'carts.is_del,carts.updated_at';
        $fields[] = 'products.name, products.description, products.price';
        $fields[] = 'users.user_id, users.username';
        $fields   = implode(',', $fields);
        $table    = $this->_tableName;
        $sql      =
            "select {$fields} from {$table} 
            LEFT JOIN products ON  {$table}.product_id = products.product_id 
            LEFT JOIN users ON  {$table}.user_id = users.user_id order by carts.updated_at desc";
        $dataList = $this->secureQuery($sql, []);
        if (empty($dataList)) {
            return [];
        }

        return $dataList;
    }


    /**
     * get list for customer
     *
     * @param       $params
     * @param array $fields
     *
     * @return array|mixed
     */
    public function getListForCustomer($params = [], $fields = []) {
        $fields = empty($fields) ? $this->fields : $fields;
        foreach ($fields as &$field) {
            $field = 'carts.'.$field;
        }
        $fields[] = 'products.name, products.price, products.description';
        $fields   = implode(',', $fields);
        $table    = $this->_tableName;
        $sql      =
            "select {$fields} from {$table} LEFT JOIN products ON  {$table}.product_id = products.product_id where {$table}.user_id = ? and {$table}.is_del = ? and {$table}.status = ?order by {$table}.updated_at desc";
        $dataList = $this->secureQuery($sql, [$params['user_id'], $params['is_del'], $params['status']]);
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
     * @return false
     */
    public function add($data) {
        return $this->insertRecord($data, $this->fields);
    }

    /**
     * @param $params
     * @param $status
     *
     * @return array
     */
    public function editStatus($params, $status){
        $where = '';
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $where .= " AND {$key} in ('".implode('\',\'', $value)."') ";
            } else {
                $where .= " AND {$key} = '{$value}' ";
            }
        }

        $sql = "UPDATE {$this->_tableName} set `status` = {$status} 
                WHERE 1 {$where}";
        return $this->secureQuery($sql, null);
    }
}