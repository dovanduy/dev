<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Arr;
use Application\Lib\Util;

class SharePostIds extends AbstractModel {

    protected static $properties = array(
        'id',
        'facebook_user_id',
        'product_id',
        'share_id',
        'group_id',
        'post_id',
        'created',
        'updated',           
    );
    
    protected static $tableName = 'share_post_ids';
        
    public function addUpdate($param = array())
    {
        $values = \Zend\Json\Decoder::decode($param['values'], \Zend\Json\Json::TYPE_ARRAY);            
        if (empty($values)) {
            return false;
        }
        foreach ($values as &$value) {
            $value['created'] = new Expression('UNIX_TIMESTAMP()');
            $value['updated'] = new Expression('UNIX_TIMESTAMP()');
        }
        unset($value);
        if (self::batchInsert(
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
