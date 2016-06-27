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
        'created',
        'updated',           
    );
    
    protected static $tableName = 'user_googles';
    
}
