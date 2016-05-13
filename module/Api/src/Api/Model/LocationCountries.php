<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class LocationCountries extends AbstractModel {
    
    protected static $properties = array(
		'id',
		'name',
		'iso_a2',
		'sort',
	);
    
    protected static $tableName = 'location_countries';
      
    public function getAll($param) {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'id', 
                'name', 
                'sort',
                'iso_a2'
            ))            
            ->where(static::$tableName . '.active = 1')     
            ->order('sort');     
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }   
    
}
