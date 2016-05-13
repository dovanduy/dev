<?php

namespace Api\Model;

use Application\Lib\Arr;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class WebsiteHasUsers extends AbstractModel {
    
    protected static $properties = array(
        'website_id',
        'user_id',
        'updated',
        'created',
        'active',
    );
    
    protected static $primaryKey = array('user_id', 'website_id');
    
    protected static $tableName = 'website_has_users';
    
    public function addUpdate($param)
    {  
        $values = array(
			'website_id' => $param['website_id'],
			'user_id' => $param['user_id'],
			'last_login' => new Expression('UNIX_TIMESTAMP()'),
		);
        if (self::batchInsert(
                $values, 
                array(
                    'last_login' => new Expression('UNIX_TIMESTAMP()'),
                    'active' => '1',
                ),
                false
            )
        ) {  
            return true;
        }
        return false;        
    }
    
    public function getAll($param) {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'website_id', 
                'user_id', 
                'created', 
                'updated',
                'active',
            ))           
            ->where(static::$tableName . '.active = 1');
        if (!empty($param['website_id'])) {      
            if (is_array($param['website_id'])) {
                $param['website_id'] = implode(',', $param['website_id']);
            }
            $select->where(static::$tableName . '.website_id IN ('. $param['website_id'] . ')');  
        }
        if (!empty($param['user_id'])) {      
            if (is_array($param['user_id'])) {
                $param['user_id'] = implode(',', $param['user_id']);
            }
            $select->where(static::$tableName . '.user_id IN ('. $param['user_id'] . ')');  
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );
    } 
    
}
