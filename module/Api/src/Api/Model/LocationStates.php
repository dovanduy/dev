<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class LocationStates extends AbstractModel {
    
    protected static $properties = array(
		'id',
		'name',
		'iso',
		'sort',
		'country_code',
        'priority',
	);
    
    protected static $tableName = 'location_states';
      
    public function getAll($param) {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'id', 
                'name', 
                'sort',
                'iso',
                'country_code'
            ))            
            ->where(static::$tableName . '.active = 1')     
            ->where(static::$tableName . '.country_code = ' . self::quote($param['country_code']))     
            ->order('country_code ASC')                      
            ->order('priority DESC')
            ->order('name ASC');     
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }       
    
}
