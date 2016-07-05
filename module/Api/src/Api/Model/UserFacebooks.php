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
        'access_token', 
        'access_token_expires_at', 
        'website_id', 
        'created',
        'updated',           
    );
    
    protected static $tableName = 'user_facebooks';

    /*
    * @desction get List users
    */
    public function getAdmin($param = array())
    {
        $param['user_id'] = implode(',', \Application\Module::getConfig('admin_user_id'));
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('user_facebooks')
            ->where(new Expression("user_id IN ({$param['user_id']})"))
            ->where(new Expression("IFNULL(access_token, '') <> ''"))
            ->order('updated DESC');        
        $selectString = $sql->getSqlStringForSqlObject($select);
        $users = static::toArray(static::selectQuery($selectString));
        $shareUrlModel = new ShareUrls;
        if (empty($param['limit'])) {
            $param['limit'] = 4;
        }
        return array(
            $users,
            $shareUrlModel->getForShare($param)
        );
    }    
    
    public function addUpdate($param)
    {         
        $param['facebook_username'] = '';
        $value = array(
            'user_id' => $param['user_id'],  
            'facebook_id' => $param['facebook_id'],  
            'facebook_name' => $param['facebook_name'],  
            'facebook_username' => $param['facebook_username'],  
            'facebook_email' => $param['facebook_email'],  
            'facebook_first_name' => $param['facebook_first_name'],  
            'facebook_last_name' => $param['facebook_last_name'],  
            'facebook_link' => $param['facebook_link'],  
            'facebook_image' => $param['facebook_image'],  
            'facebook_gender' => $param['facebook_gender'],              
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
