<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class LocationsCountries extends AbstractModel {
    
    protected static $properties = array(
		'id',
		'name'	
	);
    
    protected static $tableName = 'location_countries';
      
    public function getList($param = array()) {
        $result = self::spQuery(
            'location_countries_getall', 
            array(),
            self::RETURN_TYPE_ALL
        );
        return $result;
        return array(            
            'count' => isset($result[0][0]['foundRows']) ? $result[0][0]['foundRows'] : 0,
            'data' => isset($result[1]) ? $result[1] : array(),
            'limit' => $param['limit']
        );
    }
    
}
