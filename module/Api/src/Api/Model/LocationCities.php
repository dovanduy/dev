<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class LocationCities extends AbstractModel {
    
    protected static $properties = array(
		'id',
		'name',
		'code',
		'sort',
		'country_code',
		'state_code',
		'priority',
	);
    
    protected static $tableName = 'location_cities';
      
    public function getAll($param) {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'id', 
                'name', 
                'sort',
                'code',
                'country_code',
                'state_code'
            ))            
            ->where(static::$tableName . '.active = 1')     
            ->where(static::$tableName . '.country_code = ' . self::quote($param['country_code']))     
            ->where(static::$tableName . '.state_code = ' . self::quote($param['state_code']))     
            ->order('state_code ASC')              
            ->order('priority DESC')
            ->order('name ASC');     
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }       
    
}
