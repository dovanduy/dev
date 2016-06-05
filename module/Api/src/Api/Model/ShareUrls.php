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
        'shared',
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
            $param['limit'] = 9;
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('share_urls')
            ->order('shared ASC')
            ->order('updated DESC')
            ->limit($param['limit']);        
        $selectString = $sql->getSqlStringForSqlObject($select);
        $data = static::toArray(static::selectQuery($selectString));       
        if (!empty($data)) {
            $id = Arr::field($data, 'id');       
            \Application\Lib\Log::info(implode(',', array_values($id)));
            self::update(array(
                'table' => 'share_urls',
                'set' => array(
                    'shared' => new Expression('IFNULL(shared,0) + 1')
                ),
                'where' => array(
                    new Expression('id IN (' . implode(',', array_values($id)) . ')')
                )
            ));          
        }
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
            'shared' => 0,
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
