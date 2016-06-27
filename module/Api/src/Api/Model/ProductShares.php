<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Arr;
use Application\Lib\Util;

class ProductShares extends AbstractModel {

    protected static $properties = array(
        'id',     
        'product_id',
        'owner_id',
        'is_group',
        'is_wall',
        'social_id',
        'created',
        'updated',           
    );
    
    protected static $tableName = 'product_shares';

    /*
    * @desction get List users
    */
    public function getAll($param = array())
    {  
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('product_shares') 
            ->where('product_id = ' . $param['product_id'])
            ->order('updated DESC');
        if (isset($param['is_wall']) && $param['is_wall'] !== '') {
            $select->where('is_wall = ' . $param['is_wall']);
        }
        if (isset($param['is_group']) && $param['is_group'] !== '') {
            $select->where('is_group = ' . $param['is_group']);
        }
        if (!empty($param['owner_id'])) {
            $select->where('owner_id = ' . $param['owner_id']);
        }        
        $data = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        ); 
        return $data;
    }
    
    public function add($param = array())
    {
        if (empty($param['product_id']) 
            || empty($param['owner_id']) 
            || empty($param['social_id'])) {
            return false;
        }
        $values = array(
            'social_id' => $param['social_id'],
            'owner_id' => $param['owner_id'],
            'product_id' => $param['product_id'],
            'is_wall' => !empty($param['is_wall']) ? 1 : 0,
            'is_group' => !empty($param['is_group']) ? 1 : 0, 
            'created' => new Expression('UNIX_TIMESTAMP()'),
            'updated' => new Expression('UNIX_TIMESTAMP()'),
        );       
        if (!empty($values) && self::batchInsert(
                $values, 
                array( 
                    'updated' => new Expression('VALUES(`updated`)'),
                ),
                false
            )
        ) {            
            return true;
        }
        return false;  
    }
    
}
