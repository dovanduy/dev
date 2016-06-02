<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Arr;

class ShareUrls extends AbstractModel {

    protected static $properties = array(
        'id',
        'url',
        'website_id',
        'created',
        'updated',           
    );
    
    protected static $tableName = 'share_urls';

    /*
    * @desction get List users
    */
    public function getForShare($param = array())
    {   
        if (empty($param['limit'])) {
            $param['limit'] = 2;
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('share_urls')
            ->order('shared ASC');        
        $selectString = $sql->getSqlStringForSqlObject($select);
        $data = static::toArray(static::selectQuery($selectString));
        return $data;
    }    
    
    public function add($param = array())
    {
        if (empty($param['website_id'])            
            || empty($param['url'])) {
            return false;
        }        
        $values = array(
            'website_id' => $param['website_id'],
            'url' => $param['url'],
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
