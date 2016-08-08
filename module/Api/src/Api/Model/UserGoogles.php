<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Util;

class UserGoogles extends AbstractModel {

    protected static $properties = array(
        'user_id',
        'google_id',
        'google_name',
        'google_username',
        'google_email',
        'google_first_name',
        'google_last_name',
        'google_link',
        'google_image',
        'google_gender', 
        'access_token', 
        'access_token_expires_at', 
        'website_id', 
        'created',
        'updated',           
    );
    
    protected static $tableName = 'user_googles';
    
    public function addUpdate($param)
    {         
        $param['google_username'] = '';
        $value = array(
            'user_id' => $param['user_id'],  
            'google_id' => $param['google_id'],  
            'google_name' => $param['google_name'],  
            'google_username' => $param['google_username'],  
            'google_email' => $param['google_email'],  
            'google_first_name' => $param['google_first_name'],  
            'google_last_name' => $param['google_last_name'],  
            'google_link' => $param['google_link'],  
            'google_image' => $param['google_image'],  
            'google_gender' => $param['google_gender'],              
            'website_id' => $param['website_id'],             
            'created' => new Expression('UNIX_TIMESTAMP()'),
            'updated' => new Expression('UNIX_TIMESTAMP()'),
        );        
        $onDuplicate['updated'] = new Expression('VALUES(`updated`)');
        if (isset($param['access_token'])) {
            $onDuplicate['access_token'] = $value['access_token'] = $param['access_token'];
        }
        if (isset($param['access_token_expires_at'])) {
            $onDuplicate['access_token_expires_at'] = $value['access_token_expires_at'] = $param['access_token_expires_at'];
        }
        if (self::batchInsert(
                $value, 
                $onDuplicate,
                false
            )
        ) { 
            return true;
        }
        return false;        
    }
    
}
