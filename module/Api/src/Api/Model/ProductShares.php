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
        'user_id',
        'facebook_id',
        'group_id',       
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
        if (!empty($param['facebook_id'])) {
            $select->where('facebook_id = ' . $param['facebook_id']);
        }
        if (!empty($param['user_id'])) {
            $select->where('user_id = ' . self::quote($param['user_id']));
        }
        if (!empty($param['group_id'])) {
            $select->where('group_id = ' . self::quote($param['group_id']));
        }
        if (!empty($param['facebook_id'])) {
            $select->where('facebook_id = ' . $param['facebook_id']);
        } 
        if (!empty($param['group_only'])) {
            $select->where(new Expression("(group_id IS NOT NULL AND group_id <> '')"));
        }
        if (!empty($param['wall_only'])) {
            $select->where(new Expression("(group_id IS NULL OR group_id = '')"));
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
            || empty($param['facebook_id']) 
            || empty($param['social_id'])) {
            return false;
        }
        $values = array(
            'website_id' => $param['website_id'],
            'social_id' => $param['social_id'],
            'product_id' => $param['product_id'],
            'facebook_id' => $param['facebook_id'],            
            'user_id' => !empty($param['user_id']) ? $param['user_id'] : 0,
            'group_id' => !empty($param['group_id']) ? $param['group_id'] : null, 
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
