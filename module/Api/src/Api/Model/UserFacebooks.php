<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Util;

class UserFacebooks extends AbstractModel {

    protected static $properties = array(
        'user_id',
        'facebook_id',
        'facebook_name',
        'facebook_username',
        'facebook_email',
        'facebook_first_name',
        'facebook_last_name',
        'facebook_link',
        'facebook_image',
        'facebook_gender', 
        'created',
        'updated',           
    );
    
    protected static $tableName = 'user_facebooks';

    
}
