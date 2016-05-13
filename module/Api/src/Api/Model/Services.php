<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Services extends AbstractModel {

    protected static $properties = array(
        'is_locale',
        'parent_id',
        'service_id',
        'name',
        'values',
        'tag',
        'type',
        'status',
        'iseq',
        'page',
        'limit',
        'sort'
    );

    protected static $tableName = 'services';

    public function add($param) {

         $result = self::spQuery(
            'services_add',
            array(
                'login_id' => '',
                'tag' => $param['tag'],
                'type' => $param['type'],
                'iseq' => $param['iseq'],
                'parent_id' => $param['parent_id'],
                'name' => $param['name'],
                'values' => $param['values']
            ),
            self::RETURN_TYPE_ONE
        );
        if (!empty($result['errCode'])) {
            switch ($result['errCode']) {
                case 1:
                    self::errorNotExist('parent_id');
                    return false;
                case 2:
                    self::errorNotExist('name');
                    return false;
                case 3:
                    self::errorNotExist('values');
                    return false;
            }
        }
        return !empty($result['service_id']) ? $result['service_id'] : 0;
    }
    
    public function save($param) {
        if (!isset($param['service_id'])) {
            return self::add($param);
        }
        $result = self::spQuery ( 'services_update',
             array (
                'service_id' => isset ( $param ['service_id'] ) ? $param ['service_id'] : '',
                'ptag' => isset ( $param ['tag'] ) ? $param ['tag'] : 0,
                'ptype' => isset ( $param ['type'] ) ? $param ['type'] : 0,
                'piseq' => isset ( $param ['iseq'] ) ? $param ['iseq'] : '',
                'pparent_id' => isset ( $param ['parent_id'] ) ? $param ['parent_id'] : '',
                //'name' => isset ( $param ['name'] ) ? $param ['name'] : '',
                //'values' => isset ( $param ['values'] ) ? $param ['values'] : '',
                
        ), self::RETURN_TYPE_ONE );
        if (!empty($result['errCode'])) {
            switch ($result['errCode']) {
                case 1:
                    self::errorNotExist('service_id');
                    return false;
                case 2:
                    self::errorNotExist('tag');
                    return false;
                case 3:
                    self::errorNotExist('type');
                    return false;
                case 4:
                    self::errorNotExist('iseq');
                    return false;
                case 5:
                    self::errorNotExist('parent_id');
                    return false;
            }
        }
        return true;
    }
    
    public function getList($param) {
        $result = self::spQuery(
            'services_getall_a',
            array(
                'role'   => isset($param['role']) ? $param['role'] : 1,
                'status' => isset($param['status']) ? $param['status'] : 1,
                'keyword'=> isset($param['keyword']) ? $param['keyword'] : '',
                'page'   => isset($param['page']) ? $param['page'] : 1,
                'limit'  => isset($param['limit']) ? $param['limit'] : 10,
            ),
            self::RETURN_TYPE_MULTIPLE_RESULTSET
        );
        return array(
            'count' => isset($result[0][0]['foundRows']) ? $result[0][0]['foundRows'] : 0,
            'data'  => isset($result[1]) ? $result[1] : array(),
            'limit' => isset($param['limit']) ? $param['limit'] : 10
        );
    }
    
    public function getDetail($params) {
        $result = self::spQuery(
                'services_get',
                array(
                    'pservice_id' => isset($params['service_id']) ? $params['service_id'] : 1,
                    'plocale' => isset($params['locale']) ? $params['locale'] : '',
                ),
                self::RETURN_TYPE_MULTIPLE_RESULTSET
        );
        if (isset($result[0][0])) {
            return $result[0][0];
        } else if (isset($result[1][0])) {
            return $result[1][0];
        }
        return array();
    }
}
