<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Util;
use Application\Lib\Log;

class ContactLists extends AbstractModel {

    protected static $properties = array(
        'id',
        'name',
        'email',
        'mobile',      
        'address',      
        'sent_at',      
        'created',
        'updated', 
    );
    
    protected static $tableName = 'contact_lists';
    
   /*
    * @desction get List users
    */
    public function getAll($param)
    {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName);
        if (!empty($param['email'])) {
            $select->where(new Expression(static::$tableName .  ".email LIKE '%{$param['email']}%'"));
        }
        if (!empty($param['name'])) {
            $select->where(new Expression(static::$tableName .  ".name LIKE '%{$param['name']}%'"));
        }      
        if (!empty($param['mobile'])) {
            $select->where(new Expression(static::$tableName .  ".mobile LIKE '%{$param['mobile']}%'"));
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            $select->offset(0);
        }
        $select->order(static::$tableName . '.sent_at ASC');
        $select->order(static::$tableName . '.email ASC'); 
        $selectString = $sql->getSqlStringForSqlObject($select);
        return static::toArray(static::selectQuery($selectString));
    }
    
}
