<?php

namespace App\Models;

use Xly\Db\Db;

class BaseModel {

    const FIELD_REPLACER = '?';

    protected $_tableName = '';
    protected $_primaryKey = 'id';

    protected $_adapter = null;


    public function __construct($dbName = 'default') {
        $config = config('database');
        empty($dbName) && $dbName = env('DB_DATABASE');
        $this->_adapter = Db::getDbAdapter($config[$dbName], $this->_tableName);
    }

    public function getAdapter() {
        return $this->_adapter;
    }

    public function findFiledById($primaryId, $fields = '*') {
        if (empty($primaryId)) {
            return [];
        }
        $primaryId = $this->_escape($primaryId);
        $sql       = "select {$fields} from {$this->_tableName} where {$this->_primaryKey} = {$primaryId}";
        $result    = $this->_adapter->query($sql);
        if (empty($result)) {
            return [];
        } else {
            return $result[0];
        }
    }

    public function findByKeyValue($key, $value, $fields = '*') {
        $value  = $this->_escape($value);
        $sql    = "select {$fields} from {$this->_tableName} where {$key} ='{$value}'";
        $result = $this->_adapter->query($sql);
        if (empty($result)) {
            return [];
        } else {
            return $result;
        }
    }

    public function findOneByKeyValue($key, $value, $fields = '*') {
        $value  = $this->_escape($value);
        $sql    = "select {$fields} from {$this->_tableName} where {$key} ='{$value}' LIMIT 1";
        $result = $this->_adapter->query($sql);
        if (empty($result)) {
            return [];
        } else {
            return $result[0] ?? [];
        }
    }

    public function search($params, $page = 0, $pageSize = 0, $fields = '*', $order = '') {
        $params = $this->_escape($params);
        $sql    = "SELECT {$fields} FROM {$this->_tableName} WHERE 1 ";

        foreach ($params as $key => $value) {
//            if(strpos($value,' in (') !== false || strpos($value,' between ')){
//                $sql .= " AND ' {$value}'";
//            } else {
//                $sql .= " AND {$key} = '{$value}' ";
//            }
            $sql .= " AND {$key} = '{$value}' ";
        }

        if ($pageSize > 0) {
            $pageSize = intval($pageSize);
            $offset   = intval(($page - 1) * $pageSize);
            $sql      .= " limit {$offset}, {$pageSize}";
        }

        if (!empty($order)) {
            $sql .= " order by '{$order}'";
        }
        $ret = $this->_adapter->query($sql);

        return $ret;
    }

    public function secureQuery($sql, $params) {
//        $sql = $this->_secureSql($sql, $params);
        $result = $this->_adapter->rawQuery($sql, $params);
        if (empty($result)) {
            return [];
        } else {
            return $result;
        }
    }

    /**
     * insert data with created_at
     *
     * @param array $params
     * @param array $fields
     * @param bool  $isEscape
     *
     * @return boolean
     */
    public function insertRecord($params, $fields, $isEscape = true) {
        if (empty($params['created_at'])) {
            $params['created_at'] = date('Y-m-d H:i:s');
            $fields[]             = 'created_at';
        }

        return $this->rawInsertRecord($params, $fields, $isEscape);
    }

    /**
     * insert data (raw)
     *
     * @param array $params
     * @param array $fields
     * @param bool  $isEscape
     *
     * @return boolean
     */
    public function rawInsertRecord($params, $fields, $isEscape = true) {
        $data = $this->validateField($params, $fields);
        if ($isEscape) {
            $data = $this->_escape($data);
        }

        return $this->_adapter->insert($data);
    }

    public function batchInsert($params) {
        return $this->_adapter->batchInsert($params);
    }


    public function updateRecordById($params, $id, $fields) {
        $data                = $this->validateField($params, $fields);
        $fields[]            = 'update_date';
        $data['update_date'] = date('Y-m-d H:i:s');
        $data                = $this->_escape($data);

        return $this->_adapter->where($this->_primaryKey, $id)->update($data);
    }

    public function updateByParams($row, $value, $data) {
        if (empty($value)) {
            return false;
        }
        $data = $this->_escape($data);

        return $this->_adapter->where($row, $value)->update($data);
    }

    public function updateRecords($sql, $params) {
        $sql = $this->_secureSql($sql, $params);

        return $this->_adapter->executeUpdateSql($sql);
    }

    public function findByParams($params, $fields = '*') {
        $params = $this->_escape($params);

        $sql = "SELECT {$fields} FROM {$this->_tableName} WHERE 1 ";

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $sql .= " AND {$key} in ('".implode('\',\'', $value)."') ";
            } else {
                $sql .= " AND {$key} = '{$value}' ";
            }
        }

        $ret = $this->_adapter->query($sql);

        return $ret;
    }

    public function countByParams($params) {
        $params = $this->_escape($params);

        $sql = "SELECT count(*) as count FROM {$this->_tableName} WHERE 1 ";

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $sql .= " AND {$key} in ('".implode('\',\'', $value)."') ";
            } else {
                $sql .= " AND {$key} = '{$value}' ";
            }
        }

        $ret = $this->_adapter->query($sql);

        return $ret[0]['count'];
    }

    public function findByParamsSort($params, $sortKey = 'create_date', $sort = 'desc', $fields = '*') {
        $params = $this->_escape($params);

        $sql = "SELECT {$fields} FROM {$this->_tableName} WHERE 1 ";

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $sql .= " AND {$key} in ('".implode('\',\'', $value)."') ";
            } else {
                $sql .= " AND {$key} = '{$value}' ";
            }
        }
        $sql .= " order by $sortKey $sort";

        $ret = $this->_adapter->query($sql);

        return $ret;
    }

    /**
     * Insert or update a record matching the attributes, and fill it with values.
     *
     * @param array $attributes
     * @param array $values
     *
     * @return bool
     */
    public function updateOrInsert(array $attributes, array $values = []) {

        if ($this->findByParams($attributes)) {
            return $this->updateByMoreParams($attributes, $values);
        }

        return $this->_adapter->insert(array_merge($attributes, $values));
    }

    /**
     * get single data
     *
     * @param array  $params
     * @param string $fields
     *
     * @return array
     */
    public function findLastByParams($params, $fields = '*') {
        $ret     = [];
        $dataArr = $this->findByParams($params, $fields);
        if (!empty($dataArr)) {
            $ret = $dataArr[0];
        }

        return $ret;
    }

    public function updateByMoreParams($params, $data) {
        $data = $this->_escape($data);
        $q    = $this->_adapter;
        if (empty($params)) {
            return false;
        }

        foreach ($params as $key => $value) {
            $q->where($key, $value);
        }

        return $q->update($data);
    }


    /**
     * delete
     *
     * @param array $params
     */
    public function deleteByParams($params) {
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $this->_adapter->where($key, $value);
            }
        }

        return $this->_adapter->delete();
    }

    /**
     * get table name
     *
     * @return string
     */
    public function getTableName() {
        return $this->_tableName;
    }

    public function getPrimartKey() {
        return $this->_primaryKey;
    }

    /**
     * parse field of select
     *
     * @param array $fieldArr
     *
     * @return string
     */
    protected function parseSelectFields($fieldArr) {
        $fields = '*';

        if (!empty($fieldArr)) {
            $fields = implode(',', $fieldArr);
        }

        return $fields;
    }

    private function _secureSql($sql, $param = []) {
        $finalSql = "";
        $remain   = trim($sql);

        foreach ($param as $p) {
            $replacePos = strpos($remain, self::FIELD_REPLACER);

            if (empty($replacePos)) {
                break;
            }
            $finalSql .= substr($remain, 0, $replacePos)."'".$this->_escape($p)."'";

            $remain = substr($remain, $replacePos + 1);

            if (empty($remain)) {
                break;
            }
        }

        if ($remain != '') {
            $finalSql .= $remain;
        }


        return $finalSql;
    }

    protected function _escape($params) {
        if (is_array($params)) {
            $ret = [];
            foreach ($params as $k => $v) {
                $tmpVal = '';
                if (is_array($v)) {
                    $tmpVal = filter_var_array($v, FILTER_SANITIZE_STRING);
                } else {
                    $tmpVal = filter_var($v, FILTER_SANITIZE_STRING);
                    $tmpVal = html_entity_decode($tmpVal, ENT_QUOTES);
                    $tmpVal = $value = str_replace(['\\', "'", '"'], ['\\\\', "\'", '\"'], $tmpVal);
                }
                $ret[$k] = $tmpVal;
            }
        } else {
            $ret = filter_var($params, FILTER_SANITIZE_STRING);
        }

        return $ret;
    }

    protected function validateField($data, $params) {
        $ret = [];
        foreach ($data as $dkey => $dvalue) {
            if (in_array($dkey, $params)) {
                $ret[$dkey] = $dvalue;
            }
        }

        return $ret;
    }

    /**
     * begin transaction
     *
     * @throws \Xly\Db\Exception
     */
    public function beginTransaction() {
        $this->_adapter->beginTransaction();
    }

    /**
     * rollback transaction
     *
     * @throws \Xly\Db\Exception
     */
    public function rollBack() {
        $this->_adapter->rollBack();
    }

    /**
     * commit transaction
     *
     * @throws \Xly\Db\Exception
     */
    public function commit() {
        $this->_adapter->commit();
    }
}
